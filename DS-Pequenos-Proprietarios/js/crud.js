function configurarHeader() {
  const usuario = Api.getUsuario();
  const header = document.querySelector('.bg-blue-800 span');
  if (header && usuario) {
    header.textContent = 'Olá, ' + (usuario.nome || usuario.cpf) + ' — Gerenciador de Imóveis';
  }

  const linkSair = document.querySelector('a[data-action="sair"]');
  if (linkSair) {
    linkSair.addEventListener('click', function (e) {
      e.preventDefault();
      Api.clearToken();
      window.location.href = 'login.html';
    });
  }
}

async function initCrud(config) {
  if (!exigirAuth()) return;
  configurarHeader();

  const form = document.getElementById(config.formId);
  const tbody = document.querySelector('#' + config.tableId + ' tbody');

  async function carregar() {
    try {
      const dados = await Api.get(config.endpoint);
      renderTabela(Array.isArray(dados) ? dados : []);
    } catch (err) {
      showToast(err.message || 'Erro ao carregar dados', 'error');
    }
  }

  function renderTabela(itens) {
    tbody.innerHTML = '';
    if (itens.length === 0) {
      tbody.innerHTML = '<tr><td colspan="' + (config.colunas.length + 1) + '" class="text-center text-white font-mono py-4">Nenhum registro.</td></tr>';
      return;
    }

    itens.forEach(function (item) {
      const tr = document.createElement('tr');
      tr.className = 'border border-white';

      config.colunas.forEach(function (col) {
        const td = document.createElement('td');
        td.className = 'text-white font-mono font-bold border border-white px-3 py-2';
        td.textContent = col.render ? col.render(item) : (item[col.campo] ?? '');
        tr.appendChild(td);
      });

      const tdAcao = document.createElement('td');
      tdAcao.className = 'border border-white px-3 py-2 text-center';
      const btn = document.createElement('button');
      btn.textContent = '✕';
      btn.className = 'bg-red-900 text-white font-mono font-bold px-3 py-1 rounded cursor-pointer';
      btn.addEventListener('click', async function () {
        if (!confirm('Excluir este registro?')) return;
        try {
          await Api.delete(config.endpoint + '/' + item.id);
          showToast('Registro excluído.', 'info');
          carregar();
        } catch (err) {
          showToast(err.message || 'Erro ao excluir', 'error');
        }
      });
      tdAcao.appendChild(btn);
      tr.appendChild(tdAcao);
      tbody.appendChild(tr);
    });
  }

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    const payload = config.lerFormulario(form);

    if (config.validar && !config.validar(payload)) {
      showToast('Preencha todos os campos!', 'error');
      return;
    }

    try {
      await Api.post(config.endpoint, payload);
      form.reset();
      showToast('Registro adicionado!', 'success');
      carregar();
    } catch (err) {
      showToast(err.message || 'Erro ao salvar', 'error');
    }
  });

  await carregar();
}

function lerCampo(form, nome) {
  const el = form.querySelector('[name="' + nome + '"]');
  return el ? el.value.trim() : '';
}
