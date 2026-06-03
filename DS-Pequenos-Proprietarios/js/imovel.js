// ============================================================
// imovel.js — interatividade da página de detalhe do imóvel
// ============================================================

document.addEventListener("DOMContentLoaded", function () {

  // ── Imóvel ativo ─────────────────────────────────────────
  const imovelAtivo = Storage.get("imovelAtivo") || { id: 1 };
  const STORAGE_KEY = "imovel_" + imovelAtivo.id + "_registros";

  // ── Referências DOM ───────────────────────────────────────
  const nomeEl   = document.getElementById("nome-imovel");
  const photoTag = document.getElementById("photo-tag");
  const tbody    = document.getElementById("log-tbody");
  const btnAdd   = document.getElementById("btn-add-row");
  const contador = document.getElementById("contador-entradas");

  // ── Carrega dados salvos ──────────────────────────────────
  let dados = Storage.get(STORAGE_KEY) || {};
  let registros = dados.linhas && dados.linhas.length
    ? dados.linhas
    : [
        { oque: "", data: "", descricao: "" },
        { oque: "", data: "", descricao: "" },
        { oque: "", data: "", descricao: "" }
      ];

  // ── Nome do imóvel ────────────────────────────────────────
  function setNomeDisplay(nome) {
    const partes = nome.split(" ");
    if (partes.length >= 2) {
      const meio = Math.ceil(partes.length / 2);
      nomeEl.innerHTML = partes.slice(0, meio).join(" ") + "<br>" + partes.slice(meio).join(" ");
    } else {
      nomeEl.textContent = nome;
    }
  }

  const nomeInicial = dados.nome || ("Imóvel " + imovelAtivo.id);
  setNomeDisplay(nomeInicial);

  if (dados.endereco) photoTag.textContent = "📍 " + dados.endereco;

  // Edição inline do nome ao clicar
  nomeEl.addEventListener("click", function () {
    const nomeAtual = dados.nome || ("Imóvel " + imovelAtivo.id);

    const input = document.createElement("input");
    input.id = "nome-imovel-input";
    input.className = "text-5xl font-black";
    input.value = nomeAtual;
    input.style.cssText = "background:transparent; border:none; border-bottom:2px solid #facc15; color:#facc15; font-family:'Roboto',sans-serif; font-size:inherit; font-weight:900; text-transform:uppercase; outline:none; line-height:1; width:100%;";

    nomeEl.innerHTML = "";
    nomeEl.appendChild(input);
    input.focus();
    input.select();

    function salvarNome() {
      const novoNome = input.value.trim() || nomeAtual;
      dados = Storage.get(STORAGE_KEY) || {};
      dados.nome = novoNome;
      Storage.set(STORAGE_KEY, dados);
      setNomeDisplay(novoNome);
      showToast("Nome salvo!", "success");
    }

    input.addEventListener("blur", salvarNome);
    input.addEventListener("keydown", function (e) {
      if (e.key === "Enter")  input.blur();
      if (e.key === "Escape") setNomeDisplay(nomeAtual);
    });
  });

  // ── Renderiza tabela ──────────────────────────────────────
  function renderTabela() {
    tbody.innerHTML = "";
    registros.forEach(function (reg, i) {
      tbody.appendChild(criarLinha(reg, i));
    });
    atualizarContador();
  }

  function criarLinha(reg, index) {
    const tr = document.createElement("tr");
    tr.dataset.index = index;

    // Número
    const tdNum = document.createElement("td");
    tdNum.innerHTML = '<div class="row-num">' + (index + 1) + '</div>';

    // O que foi feito
    const tdOque = document.createElement("td");
    const taOque = document.createElement("textarea");
    taOque.className = "cell-input";
    taOque.placeholder = "Nome…";
    taOque.maxLength = 200;
    taOque.dataset.campo = "oque";
    taOque.value = reg.oque || "";
    tdOque.appendChild(taOque);

    // Data
    const tdData = document.createElement("td");
    const inputData = document.createElement("input");
    inputData.type = "date";
    inputData.className = "cell-date";
    inputData.dataset.campo = "data";
    inputData.value = reg.data || "";
    tdData.appendChild(inputData);

    // Descrição
    const tdDesc = document.createElement("td");
    const taDesc = document.createElement("textarea");
    taDesc.className = "cell-input";
    taDesc.placeholder = "Descreva…";
    taDesc.dataset.campo = "descricao";
    taDesc.value = reg.descricao || "";
    tdDesc.appendChild(taDesc);

    // Remover
    const tdAcao = document.createElement("td");
    tdAcao.style.cssText = "padding:6px 8px; vertical-align:middle; text-align:center;";
    const btnRem = document.createElement("button");
    btnRem.className = "btn-remove-row";
    btnRem.innerHTML = "✕";
    btnRem.title = "Remover linha";
    btnRem.addEventListener("click", function () {
      const idxAtual = parseInt(tr.dataset.index, 10);
      registros.splice(idxAtual, 1);
      renderTabela();
      salvarTudo();
      showToast("Linha removida.", "info");
    });
    tdAcao.appendChild(btnRem);

    tr.appendChild(tdNum);
    tr.appendChild(tdOque);
    tr.appendChild(tdData);
    tr.appendChild(tdDesc);
    tr.appendChild(tdAcao);

    [taOque, inputData, taDesc].forEach(function (el) {
      el.addEventListener("change", salvarTudo);
      el.addEventListener("blur",   salvarTudo);
    });

    return tr;
  }

  // ── Salva tudo no localStorage ────────────────────────────
  function salvarTudo() {
    const linhas = [];
    tbody.querySelectorAll("tr[data-index]").forEach(function (tr) {
      const reg = {};
      tr.querySelectorAll("[data-campo]").forEach(function (el) {
        reg[el.dataset.campo] = el.value;
      });
      linhas.push(reg);
    });
    registros = linhas;
    dados = Storage.get(STORAGE_KEY) || {};
    dados.linhas = linhas;
    Storage.set(STORAGE_KEY, dados);
  }

  // ── Contador de entradas ──────────────────────────────────
  function atualizarContador() {
    const n = registros.length;
    contador.textContent = n + (n === 1 ? " entrada" : " entradas");
  }

  // ── Adicionar linha ───────────────────────────────────────
  btnAdd.addEventListener("click", function () {
    registros.push({ oque: "", data: "", descricao: "" });
    renderTabela();
    salvarTudo();
    const ultimaLinha = tbody.lastElementChild;
    if (ultimaLinha) ultimaLinha.scrollIntoView({ behavior: "smooth", block: "nearest" });
    showToast("Linha adicionada!", "success");
  });

  // ── Init ──────────────────────────────────────────────────
  renderTabela();
});
