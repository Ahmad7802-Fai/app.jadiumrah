window.toast = {
  show(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `toast toast--${type}`;
    el.innerText = msg;

    document.body.appendChild(el);

    setTimeout(() => el.remove(), 3000);
  },
  success(msg) { this.show(msg, 'success'); },
  error(msg) { this.show(msg, 'error'); }
};
