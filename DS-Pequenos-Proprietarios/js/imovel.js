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

  const nomeEl        = document.querySelector('.imovel-header > div:first-child');
  const infoInquilino = document.getElementById('info-inquilino');
  const fotoImovel    = document.getElementById('foto-imovel');
  const inputFoto     = document.getElementById('input-foto');
  const labelFoto     = document.getElementById('label-foto');
  const tabela        = document.getElementById('tabela-gastos');
  const formArquivo   = document.getElementById('form-arquivo');
  const listaArquivos = document.getElementById('lista-arquivos');
  const pdfViewerArea = document.getElementById('pdf-viewer-area');

  let propriedade     = null;
  let inquilinosLista = [];

  tabela.querySelectorAll('tr:not(:first-child)').forEach(function (tr) { tr.remove(); });
  const headerRow = tabela.querySelector('tr:first-child');
  if (headerRow && !headerRow.querySelector('th:last-child')?.textContent.includes('Ações')) {
    const th = document.createElement('th');
    th.textContent = 'Ações';
    headerRow.appendChild(th);
  }

  try {
    propriedade     = await Api.get('propriedades/' + imovelId);
    inquilinosLista = await Api.get('inquilinos');
    if (!Array.isArray(inquilinosLista)) inquilinosLista = [];

    const registros = await Api.get('gastos?propriedade_id=' + imovelId);
    const arquivos  = await Api.get('arquivos?propriedade_id=' + imovelId);

    if (nomeEl) {
      nomeEl.textContent = propriedade.descricao || propriedade.endereco || 'Imóvel ' + imovelId;
      configurarEdicaoNome(nomeEl, propriedade);
    }
    if (infoInquilino && propriedade.inquilinoNome) {
      infoInquilino.textContent = 'Inquilino: ' + propriedade.inquilinoNome;
    }
    if (fotoImovel) {
      fotoImovel.src = urlFotoImovel(propriedade.fotoPath, imovelId);
    }

    const usuario        = Api.getUsuario();
    const ehProprietario = usuario && usuario.tipo === 'proprietario';
    if (!ehProprietario && labelFoto) {
      labelFoto.style.display = 'none';
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
  btnAdicionar.id = 'btn-add-row';
  btnAdicionar.innerHTML = '+ Adicionar linha';
  tabela.parentElement.appendChild(btnAdicionar);

  btnAdicionar.addEventListener('click', async function () {
    try {
      const novo = await Api.post('gastos', {
        valor: 0, data: new Date().toISOString().slice(0, 10),
        total: 0, propriedadeId: imovelId, descricao: '',
        inquilino: propriedade.inquilinoNome || '',
      });
      adicionarLinha(novo);
      showToast('Linha adicionada!', 'info');
    } catch (err) {
      showToast(err.message || 'Erro ao adicionar', 'error');
    }
  });

  if (inputFoto) {
    inputFoto.addEventListener('change', async function () {
      const arquivo = inputFoto.files[0];
      if (!arquivo) return;
      const formData = new FormData();
      formData.append('foto', arquivo);
      try {
        const atualizada = await Api.upload('propriedades/' + imovelId + '/foto', formData);
        if (fotoImovel) {
          fotoImovel.src = urlFotoImovel(atualizada.fotoPath, imovelId) + '?t=' + Date.now();
        }
        propriedade = atualizada;
        showToast('Foto atualizada!', 'success');
      } catch (err) {
        showToast(err.message || 'Erro ao enviar foto', 'error');
      } finally {
        inputFoto.value = '';
      }
    });
  }

  formArquivo.addEventListener('submit', async function (e) {
    e.preventDefault();
    const nomeInput = formArquivo.querySelector('[name="nome"]');
    const fileInput = formArquivo.querySelector('[name="pdf"]');
    const nome      = nomeInput ? nomeInput.value.trim() : '';
    const pdfFile   = fileInput ? fileInput.files[0] : null;

    if (!nome)    { showToast('Informe um nome para o arquivo!', 'error'); return; }
    if (!pdfFile) { showToast('Selecione um arquivo PDF!', 'error'); return; }
    if (pdfFile.type !== 'application/pdf') { showToast('Apenas PDFs são aceitos!', 'error'); return; }
    if (pdfFile.size > 20 * 1024 * 1024)   { showToast('Máximo 20 MB!', 'error'); return; }

    const fd = new FormData();
    fd.append('nome', nome);
    fd.append('pdf', pdfFile);
    fd.append('propriedadeId', imovelId);

    const btn = formArquivo.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Enviando…'; }

    try {
      await Api.upload('arquivos/upload', fd);
      formArquivo.reset();
      const labelTexto = document.getElementById('label-pdf-texto');
      if (labelTexto) labelTexto.textContent = 'Clique para selecionar…';
      const atual = await Api.get('arquivos?propriedade_id=' + imovelId);
      renderArquivos(Array.isArray(atual) ? atual : []);
      showToast('PDF adicionado com sucesso!', 'success');
    } catch (err) {
      showToast(err.message || 'Erro ao adicionar PDF', 'error');
    } finally {
      if (btn) { btn.disabled = false; btn.innerHTML = '⬆ Adicionar PDF'; }
    }
  });

  function renderArquivos(arquivos) {
    listaArquivos.innerHTML = '';
    if (arquivos.length === 0) {
      listaArquivos.innerHTML = '<li style="font-size:0.8rem;color:var(--muted)">Nenhum arquivo PDF.</li>';
      return;
    }
    arquivos.forEach(function (arq) {
      const li = document.createElement('li');
      li.className = 'pdf-item';

      const cabecalho = document.createElement('div');
      cabecalho.className = 'pdf-item-head';

      const info = document.createElement('span');
      info.className = 'pdf-item-name';
      info.innerHTML = '<span class="icon">📄</span><strong>' + escHtml(arq.nome) + '</strong>';

      const acoes = document.createElement('div');
      acoes.className = 'pdf-actions';

      const btnVer = document.createElement('button');
      btnVer.innerHTML = '👁 Ver';
      btnVer.className = 'btn-pdf';
      btnVer.addEventListener('click', async function () {
        const existente = pdfViewerArea.querySelector('.pdf-preview-container[data-id="' + arq.id + '"]');
        if (existente) {
          const visivel = existente.style.display !== 'none';
          existente.style.display = visivel ? 'none' : 'block';
          if (!visivel) existente.scrollIntoView({ behavior: 'smooth', block: 'start' });
          return;
        }
        btnVer.disabled = true;
        btnVer.textContent = 'Carregando…';
        try {
          const blob = await baixarBlob(arq.id);
          const url  = URL.createObjectURL(blob);
          abrirPreview(arq, url);
          btnVer.innerHTML = '👁 Ver';
        } catch (err) {
          showToast(err.message || 'Erro ao abrir PDF', 'error');
          btnVer.innerHTML = '👁 Ver';
        } finally {
          btnVer.disabled = false;
        }
      });
      acoes.appendChild(btnVer);

      const btnBaixar = document.createElement('button');
      btnBaixar.innerHTML = '⬇ Baixar';
      btnBaixar.className = 'btn-pdf';
      btnBaixar.addEventListener('click', async function () {
        btnBaixar.disabled = true;
        btnBaixar.textContent = 'Preparando…';
        try {
          const blob = await baixarBlob(arq.id);
          const url  = URL.createObjectURL(blob);
          const a    = document.createElement('a');
          a.href     = url;
          a.download = arq.nome.endsWith('.pdf') ? arq.nome : arq.nome + '.pdf';
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
          setTimeout(function () { URL.revokeObjectURL(url); }, 5000);
          showToast('Download iniciado!', 'success');
        } catch (err) {
          showToast(err.message || 'Erro ao baixar PDF', 'error');
        } finally {
          btnBaixar.disabled = false;
          btnBaixar.innerHTML = '⬇ Baixar';
        }
      });
      acoes.appendChild(btnBaixar);

      const btnExcluir = document.createElement('button');
      btnExcluir.textContent = '✕';
      btnExcluir.title = 'Excluir arquivo';
      btnExcluir.className = 'btn-pdf-del';
      btnExcluir.addEventListener('click', async function () {
        if (!confirm('Excluir o arquivo "' + arq.nome + '"?')) return;
        try {
          await Api.delete('arquivos/' + arq.id);
          const prev = pdfViewerArea.querySelector('.pdf-preview-container[data-id="' + arq.id + '"]');
          if (prev) {
            const iframe = prev.querySelector('iframe');
            if (iframe && iframe.src.startsWith('blob:')) URL.revokeObjectURL(iframe.src);
            prev.remove();
          }
          li.remove();
          if (listaArquivos.children.length === 0) {
            listaArquivos.innerHTML = '<li style="font-size:0.8rem;color:var(--muted)">Nenhum arquivo PDF.</li>';
          }
          showToast('Arquivo removido.', 'info');
        } catch (err) {
          showToast(err.message || 'Erro ao remover arquivo', 'error');
        }
      });
      acoes.appendChild(btnExcluir);

      cabecalho.appendChild(info);
      cabecalho.appendChild(acoes);
      li.appendChild(cabecalho);
      listaArquivos.appendChild(li);
    });
  }

  async function baixarBlob(arquivoId) {
    const token = Api.getToken();
    const url   = API_BASE + '/arquivos/' + arquivoId + '/download';
    const resp  = await fetch(url, { headers: { 'Authorization': 'Bearer ' + token } });
    if (!resp.ok) {
      let msg = 'Erro ao buscar PDF (HTTP ' + resp.status + ')';
      try { const j = await resp.clone().json(); msg = j.erro || msg; } catch (_) {}
      throw new Error(msg);
    }
    const ct = resp.headers.get('Content-Type') || '';
    if (!ct.includes('pdf')) {
      throw new Error('Resposta inválida do servidor (esperado PDF, veio: ' + ct + ')');
    }
    return resp.blob();
  }

  function abrirPreview(arq, blobUrl) {
    pdfViewerArea.innerHTML = '';

    const preview = document.createElement('div');
    preview.className = 'pdf-preview-container';
    preview.dataset.id = arq.id;

    const toolbar = document.createElement('div');
    toolbar.className = 'pdf-preview-toolbar';

    const nameSpan = document.createElement('span');
    nameSpan.textContent = arq.nome;

    const closeBtn = document.createElement('button');
    closeBtn.textContent = '✕ Fechar';
    closeBtn.addEventListener('click', function () {
      URL.revokeObjectURL(blobUrl);
      preview.remove();
    });

    toolbar.appendChild(nameSpan);
    toolbar.appendChild(closeBtn);

    const iframe = document.createElement('iframe');
    iframe.src   = blobUrl;
    iframe.title = arq.nome;

    preview.appendChild(toolbar);
    preview.appendChild(iframe);
    pdfViewerArea.appendChild(preview);
    preview.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
  function escHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function configurarEdicaoNome(el, prop) {
    el.title = 'Clique para editar o nome';
    el.style.cursor = 'pointer';
    el.addEventListener('click', function () {
      const nomeAtual = el.textContent;
      const input = document.createElement('input');
      Object.assign(input.style, {
        fontSize: '1.4rem', fontFamily: "'Inter', sans-serif", fontWeight: '700',
        background: 'var(--surface2)', color: 'var(--text)',
        border: '1px solid var(--teal)', borderRadius: '8px',
        padding: '4px 10px', width: '100%', maxWidth: '360px', outline: 'none',
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
    tr.appendChild(criarCelulaInquilino(reg.inquilino || ''));
    tr.appendChild(criarCelula('date', 'data', reg.data || ''));
    tr.appendChild(criarCelula('textarea', 'descricao', reg.descricao || ''));
    const cellAcao = document.createElement('td');
    cellAcao.className = 'cell-action';
    const btnRemover = document.createElement('button');
    btnRemover.textContent = '✕';
    btnRemover.className = 'btn-remove-row';
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
    const input = document.createElement('input');
    input.type = 'text';
    input.value = valor;
    input.dataset.campo = 'inquilino';
    input.setAttribute('list', 'inquilinos-datalist');
    input.addEventListener('change', function () { salvarLinha(trFrom(input)); });
    input.addEventListener('blur',   function () { salvarLinha(trFrom(input)); });
    td.appendChild(input);
    return td;
  }

  function criarCelula(tipo, campo, valor) {
    const td = document.createElement('td');
    let el;
    if (tipo === 'textarea') {
      el = document.createElement('textarea');
    } else {
      el = document.createElement('input');
      el.type = 'date';
    }
    el.value = valor;
    el.dataset.campo = campo;
    el.addEventListener('change', function () { salvarLinha(trFrom(el)); });
    el.addEventListener('blur',   function () { salvarLinha(trFrom(el)); });
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