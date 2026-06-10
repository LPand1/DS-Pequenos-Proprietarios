document.addEventListener('DOMContentLoaded', async function () {
  if (!exigirAuth()) return;
  configurarHeader();

  const usuario = Api.getUsuario();
  const form = document.getElementById('form-propriedade');
  const tbody = document.querySelector('#tabela-propriedades tbody');
  const aviso = document.getElementById('aviso-inquilino');
  const datalist = document.getElementById('lista-inquilinos');

  if (usuario && usuario.tipo === 'inquilino') {
    form.classList.add('hidden');
    aviso.classList.remove('hidden');
  }

  try {
    const inquilinos = await Api.get('inquilinos');
    if (Array.isArray(inquilinos)) {
      datalist.innerHTML = inquilinos.map(function (i) {
        return '<option value="' + i.nome + '">';
      }).join('');
    }
  } catch (_) {}

  async function carregar() {
    try {
      const dados = await Api.get('propriedades');
      renderTabela(Array.isArray(dados) ? dados : []);
    } catch (err) {
      showToast(err.message || 'Erro ao carregar propriedades', 'error');
    }
  }

  function renderTabela(itens) {
    tbody.innerHTML = '';
    if (itens.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" class="text-center text-white font-mono py-4">Nenhuma propriedade.</td></tr>';
      return;
    }

    itens.forEach(function (item) {
      const tr = document.createElement('tr');
      tr.className = 'border border-white';
      tr.innerHTML =
        '<td class="text-white font-mono font-bold border border-white px-3 py-2">' + item.id + '</td>' +
        '<td class="text-white font-mono font-bold border border-white px-3 py-2">' + (item.descricao || '') + '</td>' +
        '<td class="text-white font-mono font-bold border border-white px-3 py-2">' + (item.inquilinoNome || '') + '</td>' +
        '<td class="text-white font-mono font-bold border border-white px-3 py-2">' + (item.endereco || '') + '</td>' +
        '<td class="text-white font-mono font-bold border border-white px-3 py-2">R$ ' + (item.aluguel || 0) + '</td>' +
        '<td class="border border-white px-3 py-2 text-center">' +
        '<a href="imovel.html?id=' + item.id + '" class="text-white font-mono font-bold underline mr-2">Abrir</a>' +
        (usuario && usuario.tipo === 'proprietario'
          ? '<button data-id="' + item.id + '" class="btn-excluir bg-red-900 text-white font-mono px-2 py-1 rounded">✕</button>'
          : '') +
        '</td>';
      tbody.appendChild(tr);
    });

    tbody.querySelectorAll('.btn-excluir').forEach(function (btn) {
      btn.addEventListener('click', async function () {
        if (!confirm('Excluir propriedade?')) return;
        try {
          await Api.delete('propriedades/' + btn.dataset.id);
          showToast('Propriedade excluída.', 'info');
          carregar();
        } catch (err) {
          showToast(err.message || 'Erro ao excluir', 'error');
        }
      });
    });
  }

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    const payload = {
      descricao: lerCampo(form, 'descricao'),
      endereco: lerCampo(form, 'endereco'),
      aluguel: parseFloat(lerCampo(form, 'aluguel')) || 0,
      inquilinoNome: lerCampo(form, 'inquilinoNome'),
      tipo: parseInt(lerCampo(form, 'tipo'), 10) || 1,
    };

    if (!payload.endereco || !payload.inquilinoNome) {
      showToast('Endereço e nome do inquilino são obrigatórios!', 'error');
      return;
    }

    try {
      await Api.post('propriedades', payload);
      form.reset();
      showToast('Propriedade adicionada!', 'success');
      carregar();
    } catch (err) {
      showToast(err.message || 'Erro ao salvar', 'error');
    }
  });

  await carregar();
});
