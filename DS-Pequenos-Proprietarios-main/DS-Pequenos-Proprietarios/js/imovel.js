document.addEventListener('DOMContentLoaded', async function () {
  if (!exigirAuth()) return;

  document.querySelector('a[data-action="sair"]')?.addEventListener('click', function (e) {
    e.preventDefault();
    Api.clearToken();
    window.location.href = 'login.html';
  });

  const imovelId = obterImovelId();
  if (!imovelId) {
    showToast('Imóvel não informado', 'error');
    setTimeout(function () { window.location.href = 'main.html'; }, 1200);
    return;
  }

  const nomeImovel = document.querySelector('.text-4xl.bg-blue-800');
  const infoInquilino = document.getElementById('info-inquilino');
  const tabela = document.getElementById('tabela-gastos');
  const formArquivo = document.getElementById('form-arquivo');
  const listaArquivos = document.getElementById('lista-arquivos');
  let propriedade = null;
  let inquilinosLista = [];

  tabela.querySelectorAll('tr:not(:first-child)').forEach(function (tr) { tr.remove(); });

  const headerRow = tabela.querySelector('tr:first-child');
  if (headerRow && !headerRow.querySelector('th:last-child')?.textContent.includes('Ações')) {
    const th = document.createElement('th');
    th.textContent = 'Ações';
    th.className = 'text-white font-mono font-bold px-2 py-2';
    headerRow.appendChild(th);
  }

  try {
    propriedade = await Api.get('propriedades/' + imovelId);
    inquilinosLista = await Api.get('inquilinos');
    if (!Array.isArray(inquilinosLista)) inquilinosLista = [];

    const registros = await Api.get('gastos?propriedade_id=' + imovelId);
    const arquivos = await Api.get('arquivos?propriedade_id=' + imovelId);

    if (nomeImovel) {
      nomeImovel.textContent = propriedade.descricao || propriedade.endereco || 'Imóvel ' + imovelId;
      configurarEdicaoNome(nomeImovel, propriedade);
    }

    if (infoInquilino && propriedade.inquilinoNome) {
      infoInquilino.textContent = 'Inquilino: ' + propriedade.inquilinoNome;
    }

    if (Array.isArray(registros)) {
      registros.forEach(function (reg) { adicionarLinha(reg); });
    }

    renderArquivos(Array.isArray(arquivos) ? arquivos : []);
  } catch (err) {
    showToast(err.message || 'Erro ao carregar imóvel', 'error');
    return;
  }

  const btnAdicionar = document.createElement('button');
  btnAdicionar.textContent = '+ Adicionar linha';
  Object.assign(btnAdicionar.style, {
    marginTop: '12px', padding: '8px 20px', background: '#1e3a8a', color: '#fff',
    fontFamily: 'monospace', fontWeight: 'bold', fontSize: '14px', border: 'none',
    borderRadius: '6px', cursor: 'pointer',
  });
  btnAdicionar.addEventListener('click', async function () {
    try {
      const novo = await Api.post('gastos', {
        valor: 0,
        data: new Date().toISOString().slice(0, 10),
        total: 0,
        propriedadeId: imovelId,
        descricao: '',
        inquilino: propriedade.inquilinoNome || '',
      });
      adicionarLinha(novo);
      showToast('Linha adicionada!', 'info');
    } catch (err) {
      showToast(err.message || 'Erro ao adicionar', 'error');
    }
  });
  tabela.parentElement.appendChild(btnAdicionar);

  formArquivo.addEventListener('submit', async function (e) {
    e.preventDefault();
    const nome = formArquivo.nome.value.trim();
    const path = formArquivo.path.value.trim();
    if (!nome || !path) {
      showToast('Preencha nome e caminho do arquivo!', 'error');
      return;
    }
    try {
      const novo = await Api.post('arquivos', { nome: nome, path: path, propriedadeId: imovelId });
      formArquivo.reset();
      const atual = await Api.get('arquivos?propriedade_id=' + imovelId);
      renderArquivos(Array.isArray(atual) ? atual : [novo]);
      showToast('Arquivo adicionado!', 'success');
    } catch (err) {
      showToast(err.message || 'Erro ao adicionar arquivo', 'error');
    }
  });

  function renderArquivos(arquivos) {
    listaArquivos.innerHTML = '';
    if (arquivos.length === 0) {
      listaArquivos.innerHTML = '<li class="text-white font-mono text-sm opacity-80">Nenhum arquivo.</li>';
      return;
    }
    arquivos.forEach(function (arq) {
      const li = document.createElement('li');
      li.className = 'flex justify-between items-center bg-blue-900 rounded px-3 py-2';
      li.innerHTML =
        '<span class="text-white font-mono text-sm"><strong>' + arq.nome + '</strong><br><span class="opacity-80">' + arq.path + '</span></span>' +
        '<button class="text-red-300 font-mono font-bold ml-2" data-id="' + arq.id + '">✕</button>';
      li.querySelector('button').addEventListener('click', async function () {
        try {
          await Api.delete('arquivos/' + arq.id);
          li.remove();
          showToast('Arquivo removido.', 'info');
        } catch (err) {
          showToast(err.message || 'Erro ao remover', 'error');
        }
      });
      listaArquivos.appendChild(li);
    });
  }

  function configurarEdicaoNome(el, prop) {
    el.title = 'Clique para editar o nome';
    el.style.cursor = 'pointer';
    el.addEventListener('click', function () {
      const nomeAtual = el.textContent;
      const input = document.createElement('input');
      Object.assign(input.style, {
        fontSize: 'inherit', fontFamily: 'monospace', fontWeight: 'bold',
        background: '#1e3a8a', color: '#fff', border: '2px solid #93c5fd',
        borderRadius: '6px', padding: '2px 6px', width: '100%', maxWidth: '320px',
      });
      input.value = nomeAtual;
      el.replaceWith(input);
      input.focus();
      input.select();

      async function salvarNome() {
        const novoNome = input.value.trim() || nomeAtual;
        el.textContent = novoNome;
        input.replaceWith(el);
        try {
          await Api.put('propriedades/' + prop.id, { descricao: novoNome });
          showToast('Nome salvo!', 'success');
        } catch (err) {
          showToast(err.message || 'Erro ao salvar nome', 'error');
        }
      }

      input.addEventListener('blur', salvarNome);
      input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') input.blur();
        if (e.key === 'Escape') { el.textContent = nomeAtual; input.replaceWith(el); }
      });
    });
  }

  function adicionarLinha(reg) {
    const tr = document.createElement('tr');
    tr.dataset.id = reg.id || '';
    tr.style.height = '100px';
    tr.appendChild(criarCelulaInquilino(reg.inquilino || ''));
    tr.appendChild(criarCelula('date', 'data', reg.data || ''));
    tr.appendChild(criarCelula('textarea', 'descricao', reg.descricao || ''));

    const cellAcao = document.createElement('td');
    cellAcao.className = 'border border-white px-2 py-2 text-center align-middle';
    const btnRemover = document.createElement('button');
    btnRemover.textContent = '✕';
    Object.assign(btnRemover.style, {
      background: '#7f1d1d', color: '#fff', border: 'none', borderRadius: '4px',
      padding: '4px 10px', cursor: 'pointer', fontFamily: 'monospace', fontWeight: 'bold',
    });
    btnRemover.addEventListener('click', async function () {
      if (!reg.id) { tr.remove(); return; }
      try {
        await Api.delete('gastos/' + reg.id);
        tr.remove();
        showToast('Linha removida.', 'info');
      } catch (err) {
        showToast(err.message || 'Erro ao remover', 'error');
      }
    });
    cellAcao.appendChild(btnRemover);
    tr.appendChild(cellAcao);
    tabela.appendChild(tr);
  }

  function criarCelulaInquilino(valor) {
    const td = document.createElement('td');
    td.className = 'border border-white px-2 py-2 align-top';
    const input = document.createElement('input');
    input.type = 'text';
    input.value = valor;
    input.dataset.campo = 'inquilino';
    input.setAttribute('list', 'inquilinos-datalist');
    input.style.cssText = 'width:100%;background:transparent;color:white;font-family:monospace;font-weight:bold;border:none;outline:none;padding:4px;';
    input.addEventListener('change', function () { salvarLinha(trFrom(input)); });
    input.addEventListener('blur', function () { salvarLinha(trFrom(input)); });
    td.appendChild(input);
    return td;
  }

  function criarCelula(tipo, campo, valor) {
    const td = document.createElement('td');
    td.className = 'border border-white px-2 py-2 align-top';
    let el;
    if (tipo === 'textarea') {
      el = document.createElement('textarea');
      el.style.cssText = 'resize:vertical;min-height:80px;width:100%;background:transparent;color:white;font-family:monospace;font-weight:bold;border:none;outline:none;padding:4px;';
    } else {
      el = document.createElement('input');
      el.type = 'date';
      el.style.cssText = 'background:transparent;color:white;font-family:monospace;font-weight:bold;border:none;outline:none;padding:4px;';
    }
    el.value = valor;
    el.dataset.campo = campo;
    el.addEventListener('change', function () { salvarLinha(trFrom(el)); });
    el.addEventListener('blur', function () { salvarLinha(trFrom(el)); });
    td.appendChild(el);
    return td;
  }

  if (!document.getElementById('inquilinos-datalist')) {
    const dl = document.createElement('datalist');
    dl.id = 'inquilinos-datalist';
    dl.innerHTML = inquilinosLista.map(function (i) {
      return '<option value="' + i.nome + '">';
    }).join('');
    document.body.appendChild(dl);
  }

  function trFrom(el) { return el.closest('tr'); }

  async function salvarLinha(tr) {
    if (!tr || !tr.dataset.id) return;
    const payload = { valor: 0, total: 0, propriedadeId: imovelId };
    tr.querySelectorAll('[data-campo]').forEach(function (el) {
      payload[el.dataset.campo] = el.value;
    });
    try {
      await Api.put('gastos/' + tr.dataset.id, payload);
    } catch (err) {
      showToast(err.message || 'Erro ao salvar', 'error');
    }
  }
});
