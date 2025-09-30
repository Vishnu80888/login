// app.js - common helpers
const API_BASE = '/api'; // adjust if api folder is at root

function showToast(message, opts = {}) {
  const title = opts.title || '';
  const autohide = opts.autohide ?? true;
  const delay = opts.delay ?? 4000;
  const toastId = `toast-${Date.now()}`;
  const html = `
    <div id="${toastId}" class="toast align-items-center text-bg-light border shadow-sm" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>`;
  $('#toastContainer').append(html);
  const toastEl = document.getElementById(toastId);
  const bsToast = new bootstrap.Toast(toastEl, { autohide, delay });
  bsToast.show();
  // cleanup after hide
  toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

function getToken() {
  return localStorage.getItem('myapp_token');
}

function setToken(token) {
  if (token) localStorage.setItem('myapp_token', token);
  else localStorage.removeItem('myapp_token');
}

function authHeaders() {
  const t = getToken();
  return t ? { Authorization: 'Bearer ' + t } : {};
}

function ajaxPost(url, data, opts = {}) {
  return $.ajax({
    url,
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify(data),
    headers: authHeaders(),
    dataType: 'json',
    ...opts
  });
}

// auto-check auth on protected pages
function ensureAuthOrRedirect() {
  const t = getToken();
  if (!t) {
    window.location.href = '/login.html';
    return false;
  }
  return true;
}
