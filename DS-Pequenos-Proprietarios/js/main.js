document.addEventListener("DOMContentLoaded", function () {

  // Verifica sessão
  const usuario = Storage.get("usuarioLogado");
  if (!usuario) {
    showToast("Faça login primeiro!", "error");
    setTimeout(function () {
      window.location.href = "login.html";
    }, 1200);
    return;
  }

  // Atualiza nome do usuário no header
  const headerSpan = document.getElementById("header-usuario");
  if (headerSpan && usuario.usuario) {
    headerSpan.textContent = "Olá, " + usuario.usuario + " — Gerenciador de Imóveis";
  }

  // Atualiza nome nos badges dos cards com nomes salvos no Storage
  const cards = document.querySelectorAll(".prop-card");
  cards.forEach(function (card, index) {
    const imovelId = index + 1;
    const STORAGE_KEY = "imovel_" + imovelId + "_registros";
    const dadosSalvos = Storage.get(STORAGE_KEY);

    // Atualiza o nome exibido no card caso tenha sido renomeado
    const nomeEl = card.querySelector(".prop-nome");
    if (nomeEl && dadosSalvos && dadosSalvos.nome) {
      nomeEl.textContent = dadosSalvos.nome;
    }

    // Salva qual imóvel foi clicado e navega
    card.addEventListener("click", function (e) {
      e.preventDefault();
      Storage.set("imovelAtivo", { id: imovelId });
      window.location.href = "imovel.html";
    });

    // Hover na imagem
    const img = card.querySelector("img");
    if (img) {
      card.addEventListener("mouseenter", function () {
        img.style.transform = "scale(1.03)";
        img.style.transition = "transform 0.2s ease";
      });
      card.addEventListener("mouseleave", function () {
        img.style.transform = "";
      });
    }
  });

  // Botão sair — limpa sessão
  const linkSair = document.getElementById("btn-sair");
  if (linkSair) {
    linkSair.addEventListener("click", function (e) {
      e.preventDefault();
      Storage.remove("usuarioLogado");
      window.location.href = "login.html";
    });
  }

});
