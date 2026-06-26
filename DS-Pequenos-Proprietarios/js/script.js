// Substituído por imovel.js (API + JWT).
// Mantido apenas por compatibilidade — redireciona para a versão atual.
document.addEventListener('DOMContentLoaded', function () {
  const params = new URLSearchParams(window.location.search);
  const id = params.get('id') || '1';
  window.location.replace('imovel.html?id=' + id);
});
