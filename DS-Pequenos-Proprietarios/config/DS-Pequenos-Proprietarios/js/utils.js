const Storage = {
  get(key) {
    try {
      return JSON.parse(localStorage.getItem(key));
    } catch {
      return null;
    }
  },
  set(key, value) {
    localStorage.setItem(key, JSON.stringify(value));
  },
  remove(key) {
    localStorage.removeItem(key);
  }
};

// notificação temoraraia
function showToast(message, type = "success") {
  const existing = document.getElementById("toast");
  if (existing) existing.remove();

  const toast = document.createElement("div");
  toast.id = "toast";
  toast.textContent = message;

  const colors = {
    success: "#1e3a8a",  
    error: "#991b1b",    
    info: "#1e40af"      
  };

  Object.assign(toast.style, {
    position: "fixed",
    bottom: "24px",
    right: "24px",
    background: colors[type] || colors.success,
    color: "#fff",
    padding: "12px 24px",
    borderRadius: "8px",
    fontFamily: "monospace",
    fontWeight: "bold",
    fontSize: "14px",
    zIndex: "9999",
    boxShadow: "0 4px 12px rgba(0,0,0,0.3)",
    opacity: "0",
    transition: "opacity 0.3s ease"
  });

  document.body.appendChild(toast);
  requestAnimationFrame(() => (toast.style.opacity = "1"));

  setTimeout(() => {
    toast.style.opacity = "0";
    setTimeout(() => toast.remove(), 300);
  }, 2500);
}
