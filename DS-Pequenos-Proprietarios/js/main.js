document.addEventListener('DOMContentLoaded', async function () {
  if (!exigirAuth()) return;

  const usuario = Api.getUsuario();
  const header = document.querySelector('.bg-blue-800 span');
  const grid = document.querySelector('.grid');

  if (header && usuario) {
    header.textContent = 'Olá, ' + (usuario.nome || usuario.cpf) + ' — Gerenciador de Imóveis';
  }

  const linkVoltar = document.querySelector('a[data-action="sair"]');
  if (linkVoltar) {
    linkVoltar.addEventListener('click', function (e) {
      e.preventDefault();
      Api.clearToken();
      window.location.href = 'login.html';
    });
  }

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
      grid.innerHTML = '<p class="text-blue-900 font-mono font-bold col-span-2">Nenhum imóvel cadastrado.</p>';
      return;
    }

    const imagens = ['images/vector1.jpg', 'images/vector2.jpg', 'vector3.jpg', 'vector4.jpg'];

    propriedades.forEach(function (prop, index) {
      const div = document.createElement('div');
      const imgSrc = imagens[index % imagens.length];
      const nome = prop.descricao || prop.endereco || 'Imóvel ' + prop.id;

      div.innerHTML =
        '<a href="imovel.html?id=' + prop.id + '" class="block">' +
        '<img src="' + imgSrc + '" class="w-90 h-65 rounded-lg hover:grayscale-20" alt="' + nome + '">' +
        '<span class="text-white bg-blue-800 rounded-lg pl-1 pr-1 font-mono font-bold text-lg">' + nome + '</span>' +
        '</a>';

      const link = div.querySelector('a');
      const img = div.querySelector('img');

      link.addEventListener('mouseenter', function () {
        img.style.transform = 'scale(1.03)';
        img.style.transition = 'transform 0.2s ease';
        img.style.boxShadow = '0 8px 24px rgba(0,0,0,0.3)';
      });
      link.addEventListener('mouseleave', function () {
        img.style.transform = '';
        img.style.boxShadow = '';
      });

      grid.appendChild(div);
    });
  }
});
