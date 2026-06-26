const API_BASE = '../index.php';

const Api = {
  getToken() {
    return sessionStorage.getItem('jwt');
  },
  setToken(token) {
    sessionStorage.setItem('jwt', token);
  },
  clearToken() {
    sessionStorage.removeItem('jwt');
    sessionStorage.removeItem('usuario');
  },
  getUsuario() {
    try {
      return JSON.parse(sessionStorage.getItem('usuario'));
    } catch {
      return null;
    }
  },
  setUsuario(usuario) {
    sessionStorage.setItem('usuario', JSON.stringify(usuario));
  },

  async request(path, options = {}) {
    const headers = {
      'Content-Type': 'application/json',
      ...(options.headers || {}),
    };
    const token = this.getToken();
    if (token) {
      headers['Authorization'] = 'Bearer ' + token;
    }

    const res = await fetch(API_BASE + '/' + path, { ...options, headers });
    let data = {};
    try {
      data = await res.json();
    } catch (_) {}

    if (res.status === 401) {
      this.clearToken();
      if (!window.location.pathname.includes('login.html')) {
        window.location.href = 'login.html';
      }
      throw new Error(data.erro || 'Não autenticado');
    }

    if (!res.ok) {
      throw new Error(data.erro || data.detalhe || 'Erro na requisição');
    }

    return data;
  },

  get(path) {
    return this.request(path);
  },
  post(path, body) {
    return this.request(path, { method: 'POST', body: JSON.stringify(body) });
  },
  put(path, body) {
    return this.request(path, { method: 'PUT', body: JSON.stringify(body) });
  },
  delete(path) {
    return this.request(path, { method: 'DELETE' });
  },

  upload(path, formData) {
    const headers = {};
    const token = this.getToken();
    if (token) {
      headers['Authorization'] = 'Bearer ' + token;
    }

    return fetch(API_BASE + '/' + path, { method: 'POST', headers, body: formData })
      .then(async function (res) {
        let data = {};
        try {
          data = await res.json();
        } catch (_) {}

        if (res.status === 401) {
          Api.clearToken();
          if (!window.location.pathname.includes('login.html')) {
            window.location.href = 'login.html';
          }
          throw new Error(data.erro || 'Não autenticado');
        }

        if (!res.ok) {
          throw new Error(data.erro || data.detalhe || 'Erro na requisição');
        }

        return data;
      });
  },
};

function showToast(message, type = 'success') {
  const existing = document.getElementById('toast');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.id = 'toast';
  toast.textContent = message;

  const colors = {
    success: '#00D5BE',
    error: '#991b1b',
    info: '#00ffe5ff',
  };

  Object.assign(toast.style, {
    position: 'fixed',
    bottom: '24px',
    right: '24px',
    background: colors[type] || colors.success,
    color: '#fff',
    padding: '12px 24px',
    borderRadius: '8px',
    fontFamily: 'monospace',
    fontWeight: 'bold',
    fontSize: '14px',
    zIndex: '9999',
    boxShadow: '0 4px 12px rgba(0,0,0,0.3)',
    opacity: '0',
    transition: 'opacity 0.3s ease',
  });

  document.body.appendChild(toast);
  requestAnimationFrame(function () {
    toast.style.opacity = '1';
  });

  setTimeout(function () {
    toast.style.opacity = '0';
    setTimeout(function () {
      toast.remove();
    }, 300);
  }, 2500);
}

function exigirAuth() {
  if (!Api.getToken()) {
    showToast('Faça login primeiro!', 'error');
    setTimeout(function () {
      window.location.href = 'login.html';
    }, 1200);
    return false;
  }
  return true;
}

function obterImovelId() {
  const params = new URLSearchParams(window.location.search);
  const id = parseInt(params.get('id'), 10);
  return isNaN(id) ? null : id;
}

const FOTOS_PADRAO = ['images/vector1.jpg', 'images/vector2.jpg', 'vector3.jpg', 'vector4.jpg'];

function urlFotoImovel(fotoPath, indice) {
  if (fotoPath) {
    return '../' + fotoPath;
  }
  return FOTOS_PADRAO[indice % FOTOS_PADRAO.length];
}
