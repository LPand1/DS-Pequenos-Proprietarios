document.addEventListener('DOMContentLoaded', function () {
  if (Api.getToken()) {
    window.location.href = 'main.html';
    return;
  }

  const painelLogin = document.getElementById('painel-login');
  const painelCadastro = document.getElementById('painel-cadastro');
  const formLogin = document.getElementById('form-login');
  const formCadastro = document.getElementById('form-cadastro');
  const campoEmail = document.getElementById('campo-email');
  const radiosTipo = document.querySelectorAll('input[name="tipo"]');

  document.querySelectorAll('.tab-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const tab = btn.dataset.tab;
      document.querySelectorAll('.tab-btn').forEach(function (b) {
        b.classList.remove('bg-blue-800');
        b.classList.add('bg-blue-600');
      });
      btn.classList.remove('bg-blue-600');
      btn.classList.add('bg-blue-800');

      if (tab === 'login') {
        painelLogin.classList.remove('hidden');
        painelCadastro.classList.add('hidden');
      } else {
        painelLogin.classList.add('hidden');
        painelCadastro.classList.remove('hidden');
      }
    });
  });

  radiosTipo.forEach(function (radio) {
    radio.addEventListener('change', function () {
      if (radio.value === 'inquilino' && radio.checked) {
        campoEmail.classList.remove('hidden');
      } else if (radio.value === 'proprietario' && radio.checked) {
        campoEmail.classList.add('hidden');
      }
    });
  });

  formLogin.addEventListener('submit', async function (e) {
    e.preventDefault();
    const cpf = formLogin.cpf.value.trim().replace(/\D/g, '');
    const senha = formLogin.senha.value.trim();

    if (!cpf || !senha) {
      showToast('Preencha CPF e senha!', 'error');
      return;
    }

    try {
      const res = await Api.post('login', { cpf: cpf, senha: senha });
      salvarSessao(res);
    } catch (err) {
      showToast(err.message || 'Erro ao entrar', 'error');
    }
  });

  formCadastro.addEventListener('submit', async function (e) {
    e.preventDefault();
    const tipo = document.querySelector('input[name="tipo"]:checked').value;
    const nome = formCadastro.nome.value.trim();
    const cpf = formCadastro.cpf.value.trim().replace(/\D/g, '');
    const senha = formCadastro.senha.value.trim();
    const email = formCadastro.email.value.trim();

    if (!nome || !cpf || !senha) {
      showToast('Preencha todos os campos obrigatórios!', 'error');
      return;
    }

    if (tipo === 'inquilino' && !email) {
      showToast('E-mail é obrigatório para inquilino!', 'error');
      return;
    }

    try {
      const res = await Api.post('cadastro', { tipo: tipo, nome: nome, cpf: cpf, senha: senha, email: email });
      showToast(res.mensagem || 'Cadastro realizado!', 'success');
      salvarSessao(res);
    } catch (err) {
      showToast(err.message || 'Erro ao cadastrar', 'error');
    }
  });

  function salvarSessao(res) {
    Api.setToken(res.token);
    Api.setUsuario(res.usuario);
    showToast('Redirecionando...', 'success');
    setTimeout(function () {
      window.location.href = 'main.html';
    }, 800);
  }
});
