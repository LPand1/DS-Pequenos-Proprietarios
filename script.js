
document.addEventListener("DOMContentLoaded", function () {

  //nome imovel
  const imovelAtivo = Storage.get("imovelAtivo") || { id: 1 };
  const STORAGE_KEY = "imovel_" + imovelAtivo.id + "_registros";


  const nomeImovel = document.querySelector(".text-4xl.bg-blue-800");
  const tabela = document.querySelector("table");
  const tbody = tabela.querySelector("tbody") || tabela; // suporte a ambos

  // mostrar nome
  if (nomeImovel) {
    nomeImovel.textContent = "Imóvel " + imovelAtivo.id;

    // mudar o nome
    nomeImovel.title = "Clique para editar o nome";
    nomeImovel.style.cursor = "pointer";

    nomeImovel.addEventListener("click", function () {
      const nomeAtual = nomeImovel.textContent;
      const input = document.createElement("input");

      Object.assign(input.style, {
        fontSize: "inherit",
        fontFamily: "monospace",
        fontWeight: "bold",
        background: "#1e3a8a",
        color: "#fff",
        border: "2px solid #93c5fd",
        borderRadius: "6px",
        padding: "2px 6px",
        width: "300px"
      });

      input.value = nomeAtual;
      nomeImovel.replaceWith(input);
      input.focus();
      input.select();

      function salvarNome() {
        const novoNome = input.value.trim() || nomeAtual;
        nomeImovel.textContent = novoNome;
        input.replaceWith(nomeImovel);

        //mudar sem atualizar
        const dados = Storage.get(STORAGE_KEY) || {};
        dados.nome = novoNome;
        Storage.set(STORAGE_KEY, dados);
        showToast("Nome salvo!", "success");
      }

      input.addEventListener("blur", salvarNome);
      input.addEventListener("keydown", function (e) {
        if (e.key === "Enter") input.blur();
        if (e.key === "Escape") {
          nomeImovel.textContent = nomeAtual;
          input.replaceWith(nomeImovel);
        }
      });
    });

    // atualiza nome
    const dadosSalvos = Storage.get(STORAGE_KEY);
    if (dadosSalvos && dadosSalvos.nome) {
      nomeImovel.textContent = dadosSalvos.nome;
    }
  }

 
  // reescrever as 300 linhas do gabardo com 2
  const linhasExistentes = tabela.querySelectorAll("tr:not(:first-child)");
  linhasExistentes.forEach(function (tr) { tr.remove(); });

  // criar as linha do gabardo
  let registros = (Storage.get(STORAGE_KEY) || {}).linhas || [
    { inquilino: "", data: "", descricao: "" },
    { inquilino: "", data: "", descricao: "" },
    { inquilino: "", data: "", descricao: "" }
  ];

  registros.forEach(function (_, i) { adicionarLinha(i); });

  sincronizarCampos();

  // adicionar linha(nao sei como fazer pra savar no db ainda)
  const btnAdicionar = document.createElement("button");
  btnAdicionar.textContent = "+ Adicionar linha";
  Object.assign(btnAdicionar.style, {
    marginTop: "12px",
    padding: "8px 20px",
    background: "#1e3a8a",
    color: "#fff",
    fontFamily: "monospace",
    fontWeight: "bold",
    fontSize: "14px",
    border: "none",
    borderRadius: "6px",
    cursor: "pointer"
  });
  btnAdicionar.addEventListener("mouseenter", function () {
    btnAdicionar.style.background = "#1e40af";
  });
  btnAdicionar.addEventListener("mouseleave", function () {
    btnAdicionar.style.background = "#1e3a8a";
  });
  btnAdicionar.addEventListener("click", function () {
    registros.push({ inquilino: "", data: "", descricao: "" });
    adicionarLinha(registros.length - 1);
    salvarTudo();
    showToast("Linha adicionada!", "info");
  });

  tabela.parentElement.appendChild(btnAdicionar);

  // criar linha na tabela
  function adicionarLinha(index) {
    const tr = document.createElement("tr");
    tr.dataset.index = index;
    tr.style.height = "120px";

    const cellInquilino = criarCelula("textarea", index, "inquilino");
    const cellData = criarCelula("date", index, "data");
    const cellDescricao = criarCelula("textarea", index, "descricao");

    // açoes
    const cellAcao = document.createElement("td");
    cellAcao.style.cssText = "border: 1px solid white; padding: 4px; vertical-align: middle; text-align: center;";

    const btnRemover = document.createElement("button");
    btnRemover.textContent = "✕";
    Object.assign(btnRemover.style, {
      background: "#7f1d1d",
      color: "#fff",
      border: "none",
      borderRadius: "4px",
      padding: "4px 10px",
      cursor: "pointer",
      fontFamily: "monospace",
      fontWeight: "bold"
    });
    btnRemover.title = "Remover linha";
    btnRemover.addEventListener("click", function () {
      registros.splice(index, 1);
      tr.remove();
      reindexarLinhas();
      salvarTudo();
      showToast("Linha removida.", "info");
    });

    cellAcao.appendChild(btnRemover);
    tr.appendChild(cellInquilino);
    tr.appendChild(cellData);
    tr.appendChild(cellDescricao);
    tr.appendChild(cellAcao);
    tabela.appendChild(tr);
  }

  function criarCelula(tipo, index, campo) {
    const td = document.createElement("td");
    td.style.cssText = "border: 1px solid white; padding: 4px; vertical-align: top;";

    let el;
    if (tipo === "textarea") {
      el = document.createElement("textarea");
      el.style.cssText = "resize: vertical; min-height: 100px; width: 100%; background: transparent; color: white; font-family: monospace; font-weight: bold; border: none; outline: none; padding: 4px;";
    } else {
      el = document.createElement("input");
      el.type = "date";
      el.style.cssText = "background: transparent; color: white; font-family: monospace; font-weight: bold; border: none; outline: none; padding: 4px;";
    }

    el.dataset.index = index;
    el.dataset.campo = campo;

    // Auto save
    el.addEventListener("change", function () { salvarTudo(); });
    el.addEventListener("blur", function () { salvarTudo(); });

    td.appendChild(el);
    return td;
  }


  function sincronizarCampos() {
    registros.forEach(function (reg, i) {
      const linha = tabela.querySelector('tr[data-index="' + i + '"]');
      if (!linha) return;

      const campos = linha.querySelectorAll("[data-campo]");
      campos.forEach(function (el) {
        el.value = reg[el.dataset.campo] || "";
      });
    });
  }



  // index(nao tava funcionando sem essa gambiarra)
  function reindexarLinhas() {
    const linhasDOM = tabela.querySelectorAll("tr[data-index]");
    linhasDOM.forEach(function (tr, i) {
      tr.dataset.index = i;
      tr.querySelectorAll("[data-index]").forEach(function (el) {
        el.dataset.index = i;
      });
    });
    registros = [];
    linhasDOM.forEach(function (tr) {
      const reg = {};
      tr.querySelectorAll("[data-campo]").forEach(function (el) {
        reg[el.dataset.campo] = el.value;
      });
      registros.push(reg);
    });
  }

  // achoes
  const headerRow = tabela.querySelector("tr:first-child");
  if (headerRow) {
    const th = document.createElement("th");
    th.textContent = "Ações";
    th.style.cssText = "color: white; font-family: monospace; font-weight: bold; padding: 4px 8px;";
    headerRow.appendChild(th);
  }

});