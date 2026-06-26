document.addEventListener('DOMContentLoaded', async function () {
  if (!exigirAuth()) return;

  configurarHeader();

  const grid = document.querySelector('.grid');

  try {
    const propriedades = await Api.get('propriedades');
    renderizarImoveis(Array.isArray(propriedades) ? propriedades : []);
  } catch (err) {
    showToast(err.message || 'Erro ao carregar imóveis', 'error');
  }

  function renderizarImoveis(propriedades) {
    if (!grid) return;
    grid.innerHTML = '';
    if (propriedades.length === 0) {
      grid.innerHTML = '<p style="color:var(--muted);font-size:0.9rem;">Nenhum imóvel cadastrado.</p>';
      return;
    }
    propriedades.forEach(function (prop, index) {
      const nome   = prop.descricao || prop.endereco || 'Imóvel ' + prop.id;
      const imgSrc = urlFotoImovel(prop.fotoPath, index);

      const a = document.createElement('a');
      a.href      = 'imovel.html?id=' + prop.id;
      a.className = 'imovel-card';

      const img = document.createElement('img');
      img.src = imgSrc;
      img.alt = nome;

      const label = document.createElement('div');
      label.className = 'card-label';
      label.textContent = nome;

      a.appendChild(img);
      a.appendChild(label);
      grid.appendChild(a);
    });
  }
});