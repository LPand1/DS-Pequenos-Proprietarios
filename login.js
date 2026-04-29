
document.addEventListener("DOMContentLoaded", function () {

  const form = document.querySelector("form");
  const inputUsuario = document.querySelector('input[name="usuario"]');
  const inputSenha = document.querySelector('input[name="senha"]');
  const btnEntrar = document.querySelector('button[type="submit"]');

  // redireciona
  if (Storage.get("usuarioLogado")) {
    window.location.href = "main.html";
    return;
  }

  // estilo
  [inputUsuario, inputSenha].forEach(function (input) {
    input.addEventListener("focus", function () {
      input.style.outline = "2px solid #60a5fa";
      input.style.outlineOffset = "2px";
    });
    input.addEventListener("blur", function () {
      input.style.outline = "";
    });
  });

  //limpar erro
  [inputUsuario, inputSenha].forEach(function (input) {
    input.addEventListener("input", function () {
      input.style.border = "";
    });
  });

  // enviar sem reload
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const usuario = inputUsuario.value.trim();
    const senha = inputSenha.value.trim();

    // validação
    if (!usuario || !senha) {
      if (!usuario) destacarErro(inputUsuario);
      if (!senha) destacarErro(inputSenha);
      showToast("Preencha todos os campos!", "error");
      return;
    }


    btnEntrar.textContent = "Entrando...";
    btnEntrar.disabled = true;

    // tirar quand o login for pelo php
    setTimeout(function () {
      Storage.set("usuarioLogado", { usuario: usuario });
      showToast("Login realizado! Redirecionando...", "success");

      setTimeout(function () {
        window.location.href = "main.html";
      }, 1000);
    }, 800);
    
  });

  // usar enter no login
  [inputUsuario, inputSenha].forEach(function (input) {
    input.addEventListener("keydown", function (e) {
      if (e.key === "Enter") form.dispatchEvent(new Event("submit"));
    });
  });

  // erro brilha
  function destacarErro(input) {
    input.style.border = "2px solid #f87171";
    input.focus();
  }

});
