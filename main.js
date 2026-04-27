
document.addEventListener("DOMContentLoaded", function () {

  // sair pelo session?
  const usuario = Storage.get("usuarioLogado");
  if (!usuario) {
    showToast("Faça login primeiro!", "error");
    setTimeout(function () {
      window.location.href = "login.html";
    }, 1200);
    return;
  }


  const header = document.querySelector(".bg-blue-800 span");
  if (header && usuario.usuario) {
    header.textContent = "Olá, " + usuario.usuario + " — Gerenciador de Imóveis";
  }


  const linkVoltar = document.querySelector('a[href="login.html"]');
  if (linkVoltar) {
    linkVoltar.addEventListener("click", function (e) {
      e.preventDefault();
      Storage.remove("usuarioLogado");
      window.location.href = "login.html";
    });
  }

  // id dos imovei
  const cards = document.querySelectorAll('a[href="imovel.html"]');

  cards.forEach(function (card, index) {
    const imovelId = index + 1; 

  
    card.addEventListener("click", function (e) {
      e.preventDefault();
      Storage.set("imovelAtivo", { id: imovelId });
      window.location.href = "imovel.html";
    });


    const img = card.querySelector("img");
    if (img) {
      card.addEventListener("mouseenter", function () {
        img.style.transform = "scale(1.03)";
        img.style.transition = "transform 0.2s ease";
        img.style.boxShadow = "0 8px 24px rgba(0,0,0,0.3)";
      });
      card.addEventListener("mouseleave", function () {
        img.style.transform = "";
        img.style.boxShadow = "";
      });
    }
  });

});
