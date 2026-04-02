<?php
$pageTitle = 'Login - BimbelKu';
require __DIR__ . '/../layouts/header.php';
?>

<div class="bg-shapes">
  <div class="shape shape-1"></div>
  <div class="shape shape-2"></div>
  <div class="shape shape-3"></div>
</div>

<div class="spinner-overlay" id="loadingOverlay">
  <div class="custom-spinner"></div>
</div>

<div class="login-container">
  <div class="login-card">
    <div class="login-card-inner">

      <div class="login-illustration">
        <div class="icon-wrapper"><i class="fas fa-graduation-cap"></i></div>
        <h3>Selamat Datang Di Bimbel Orion</h3>
        <p>Platform pembelajaran terbaik untuk masa depan cerah Anda</p>
      </div>

      <div class="login-form-section">
        <div class="brand-logo">
          <div class="logo-wrapper">
            <div class="logo-icon"><i class="fas fa-book-open"></i></div>
            <span class="logo-text">BimbelKu</span>
          </div>
          <button class="theme-toggle" id="themeToggle" title="Toggle Dark Mode">
            <i class="fas fa-moon"></i>
          </button>
        </div>

        <h2 class="login-title">Masuk Akun</h2>
        <p class="login-subtitle">Silakan masuk untuk mengakses pembelajaran</p>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
          <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=login" id="loginForm">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars(getCsrfToken()) ?>">
          <div class="input-group">
            <input type="email" name="email" id="email" placeholder=" " required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <label for="email">Email Address</label>
            <i class="fas fa-envelope input-icon"></i>
          </div>

          <div class="input-group">
            <input type="password" name="password" id="password" placeholder=" " required>
            <label for="password">Password</label>
            <i class="fas fa-lock input-icon"></i>
            <button type="button" class="password-toggle" id="togglePassword">
              <i class="fas fa-eye" id="eyeIcon"></i>
            </button>
          </div>

          <div class="form-options">
            <label class="remember-me">
              <input type="checkbox" name="remember">
              <span>Ingat saya</span>
            </label>
            <a href="#" class="forgot-password">Lupa password?</a>
          </div>

          <button type="submit" class="btn-submit-login" id="loginBtn">
            Masuk Sekarang
          </button>

          <div class="social-login">
            <button type="button" class="social-btn google">
              <i class="fab fa-google"></i> Google
            </button>
          </div>

          <div class="register-link">
            <p>Belum punya akun? <a href="index.php?page=pendaftaran">Daftar Sekarang</a></p>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="public/js/main.js"></script>
</body>
</html>
