const toggle = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
if (toggle && sidebar) {
  toggle.addEventListener('click', () => sidebar.classList.toggle('show'));
}
document.addEventListener('click', (event) => {
  if (sidebar && sidebar.classList.contains('show') &&
      !sidebar.contains(event.target) && toggle && !toggle.contains(event.target)) {
    sidebar.classList.remove('show');
  }
});

// Toggle password visibility (eye icon)
document.querySelectorAll('.password-toggle').forEach((btn) => {
  btn.addEventListener('click', () => {
    const targetId = btn.getAttribute('data-target');
    const input = document.getElementById(targetId);
    if (!input) return;
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    const eye = btn.querySelector('.icon-eye');
    const eyeOff = btn.querySelector('.icon-eye-off');
    if (eye && eyeOff) {
      eye.style.display = isHidden ? 'none' : 'block';
      eyeOff.style.display = isHidden ? 'block' : 'none';
    }
    btn.setAttribute('title', isHidden ? 'Sembunyikan password' : 'Lihat password');
  });
});

// Copy URL button on Saved Links
document.querySelectorAll('.copy-link-btn').forEach((btn) => {
  btn.addEventListener('click', async () => {
    const url = btn.getAttribute('data-url');
    if (!url) return;
    const orig = btn.textContent;
    try {
      await navigator.clipboard.writeText(url);
      btn.textContent = 'Tersalin ✓';
      setTimeout(() => { btn.textContent = orig; }, 1600);
    } catch (err) {
      prompt('Salin link berikut:', url);
    }
  });
});
