document.addEventListener('DOMContentLoaded', async function () {
  if (!exigirAuth()) return;
  configurarHeader();

  const tbody = document.querySelector('#tabela-inquilinos tbody');

  try {
    const inquilinos = await Api.get('inquilinos');
    tbody.innerHTML = '';

    if (!Array.isArray(inquilinos) || inquilinos.length === 0) {
      tbody.innerHTML = '<tr><td colspan="2" class="text-center text-white font-mono py-4">Nenhum inquilino cadastrado.</td></tr>';
      return;
    }

    inquilinos.forEach(function (i) {
      const tr = document.createElement('tr');
      tr.className = 'border border-white';
      tr.innerHTML =
        '<td class="text-white font-mono font-bold border border-white px-3 py-2">' + i.nome + '</td>' +
        '<td class="text-white font-mono font-bold border border-white px-3 py-2">' + i.email + '</td>';
      tbody.appendChild(tr);
    });
  } catch (err) {
    showToast(err.message || 'Erro ao carregar inquilinos', 'error');
  }
});
