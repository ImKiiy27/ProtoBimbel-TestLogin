/* ============================================================
   BimbelKu - main.js
   Gabungan semua JS dari: index, login, pendaftaran, dashboard
   ============================================================ */

/* ------------------------------------------------------------
   1. THEME TOGGLE (semua halaman)
   ------------------------------------------------------------ */
function initTheme() {
  const themeToggle = document.getElementById('themeToggle');
  if (!themeToggle) return;

  const html = document.documentElement;
  const savedTheme = localStorage.getItem('theme') || 'light';
  html.setAttribute('data-theme', savedTheme);
  updateThemeIcon(savedTheme);

  themeToggle.addEventListener('click', function () {
    const current = html.getAttribute('data-theme');
    const next = current === 'light' ? 'dark' : 'light';
    html.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
    updateThemeIcon(next);
  });
}

function updateThemeIcon(theme) {
  const icon = document.querySelector('#themeToggle i');
  if (!icon) return;
  icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
}

/* ------------------------------------------------------------
   2. NAVBAR SCROLL EFFECT (index)
   ------------------------------------------------------------ */
function initNavbarScroll() {
  const navbar = document.getElementById('navbar');
  if (!navbar) return;
  window.addEventListener('scroll', function () {
    navbar.classList.toggle('scrolled', window.scrollY > 50);
  });
}

/* ------------------------------------------------------------
   3. FADE IN ON SCROLL (index)
   ------------------------------------------------------------ */
function initFadeIn() {
  const faders = document.querySelectorAll('.fade-in');
  if (!faders.length) return;

  const observer = new IntersectionObserver(function (entries, obs) {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('show');
      obs.unobserve(entry.target);
    });
  }, { threshold: 0.2, rootMargin: '0px 0px -50px 0px' });

  faders.forEach(el => observer.observe(el));
}

/* ------------------------------------------------------------
   4. PASSWORD TOGGLE (login)
   ------------------------------------------------------------ */
function initPasswordToggle() {
  const toggleBtn = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');
  const eyeIcon = document.getElementById('eyeIcon');
  if (!toggleBtn || !passwordInput) return;

  toggleBtn.addEventListener('click', function () {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    eyeIcon.classList.toggle('fa-eye');
    eyeIcon.classList.toggle('fa-eye-slash');
  });
}

/* ------------------------------------------------------------
   5. LOGIN FORM SUBMIT (login)
   ------------------------------------------------------------ */
function initLoginForm() {
  const form = document.getElementById('loginForm');
  const loginBtn = document.getElementById('loginBtn');
  const loadingOverlay = document.getElementById('loadingOverlay');
  if (!form) return;

  // Floating label: hapus placeholder agar CSS :not(:placeholder-shown) bekerja
  document.querySelectorAll('.input-group input').forEach(input => {
    input.setAttribute('placeholder', ' ');
  });

  form.addEventListener('submit', function () {
    if (loginBtn) {
      loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
      loginBtn.disabled = true;
    }
    if (loadingOverlay) loadingOverlay.classList.add('active');
  });
}

/* ------------------------------------------------------------
   6. PENDAFTARAN FORM (pendaftaran)
   ------------------------------------------------------------ */
function initPendaftaranForm() {
  const form = document.getElementById('pendaftaranForm');
  const submitBtn = document.getElementById('submitBtn');
  if (!form) return;

  form.addEventListener('submit', function () {
    if (submitBtn) {
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengirim...';
      submitBtn.disabled = true;
    }
  });
}

/* ------------------------------------------------------------
   7. SIDEBAR TOGGLE (dashboard - mobile)
   ------------------------------------------------------------ */
function initSidebar() {
  const toggleBtn = document.getElementById('sidebarToggle');
  const sidebar = document.querySelector('.sidebar');
  if (!toggleBtn || !sidebar) return;

  const isDesktop = () => window.innerWidth > 992;

  const setMobileState = (isOpen) => {
    sidebar.classList.toggle('active', isOpen);
    document.body.classList.toggle('sidebar-open', isOpen);
    toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  };

  const setDesktopState = (collapsed) => {
    sidebar.classList.toggle('collapsed', collapsed);
    document.body.classList.toggle('sidebar-collapsed', collapsed);
    toggleBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
  };

  toggleBtn.addEventListener('click', function () {
    if (isDesktop()) {
      const willCollapse = !sidebar.classList.contains('collapsed');
      setDesktopState(willCollapse);
    } else {
      const willOpen = !sidebar.classList.contains('active');
      setMobileState(willOpen);
    }
  });

  // Tutup sidebar kalau klik di luar (mobile)
  document.addEventListener('click', function (e) {
    if (window.innerWidth <= 992 && sidebar.classList.contains('active')) {
      const clickedToggle = toggleBtn.contains(e.target);
      if (!sidebar.contains(e.target) && !clickedToggle) {
        setMobileState(false);
      }
    }
  });

  // Sinkronisasi state saat resize
  window.addEventListener('resize', function () {
    if (isDesktop()) {
      // pastikan mode mobile dimatikan
      setMobileState(false);
    } else {
      // kembali ke mobile: tampilkan sidebar jika sebelumnya tidak collapsed
      sidebar.classList.remove('collapsed');
      document.body.classList.remove('sidebar-collapsed');
    }
  });
}

/* ------------------------------------------------------------
   8. AUTO DISMISS ALERT
   ------------------------------------------------------------ */
function initAlertDismiss() {
  const alerts = document.querySelectorAll('.alert-custom');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.opacity = '0';
      alert.style.transition = 'opacity 0.5s ease';
      setTimeout(() => alert.remove(), 500);
    }, 4000);
  });
}

/* ------------------------------------------------------------
   9. INIT SEMUA
   ------------------------------------------------------------ */
document.addEventListener('DOMContentLoaded', function () {
  initTheme();
  initNavbarScroll();
  initFadeIn();
  initPasswordToggle();
  initLoginForm();
  initPendaftaranForm();
  initSidebar();
  initAlertDismiss();
});
