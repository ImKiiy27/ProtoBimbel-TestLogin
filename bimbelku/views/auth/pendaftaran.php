<?php
$pageTitle = 'Pendaftaran - BimbelKu';
require __DIR__ . '/../layouts/header.php';
?>

<div class="bg-animation">
  <div class="floating-shape shape-1"></div>
  <div class="floating-shape shape-2"></div>
</div>

<nav class="navbar navbar-expand-lg navbar-light fixed-top" id="navbar">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <div class="logo-icon" style="width:40px;height:40px;font-size:18px;"><i class="fas fa-book-open"></i></div>
      <span class="brand-text">BimbelKu</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
        <li class="nav-item ms-3">
          <button class="theme-toggle" id="themeToggle" style="width:40px;height:40px;font-size:18px;">
            <i class="fas fa-moon"></i>
          </button>
        </li>
        <li class="nav-item ms-3"><a href="index.php?page=login" class="btn btn-login px-4">Login</a></li>
      </ul>
    </div>
  </div>
</nav>

<div style="padding-top: 100px; padding-bottom: 60px; position: relative; z-index: 1;">
  <div class="container">

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger alert-custom alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success alert-custom alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="glass-card animate-fade-in">
      <div class="row g-0">

        <!-- Kiri: Form -->
        <div class="col-lg-7">
          <div class="card-header-custom">
            <h2><i class="fas fa-user-plus me-3"></i>Daftar Sekarang</h2>
            <p>Isi formulir di bawah ini dan tim kami akan segera menghubungi Anda</p>
          </div>
          <div class="card-body-custom">
            <form method="POST" action="index.php?page=pendaftaran" id="pendaftaranForm">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars(getCsrfToken()) ?>">

              <div class="form-group">
                <label for="nama"><i class="fas fa-user me-2" style="color:#0d6efd;"></i>Nama Lengkap</label>
                <input type="text" name="nama" id="nama" class="form-control-custom"
                       placeholder="Masukkan nama lengkap"
                       value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
              </div>

              <div class="form-group">
                <label for="email"><i class="fas fa-envelope me-2" style="color:#0d6efd;"></i>Email</label>
                <input type="email" name="email" id="email" class="form-control-custom"
                       placeholder="Masukkan alamat email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
              </div>

              <div class="form-group">
                <label for="telepon"><i class="fas fa-phone me-2" style="color:#0d6efd;"></i>Nomor Telepon / WhatsApp</label>
                <input type="text" name="telepon" id="telepon" class="form-control-custom"
                       placeholder="Contoh: 08123456789"
                       value="<?= htmlspecialchars($_POST['telepon'] ?? '') ?>" required>
              </div>

              <div class="row g-3 mt-2">
                <div class="col-6">
                  <a href="index.php" class="btn-back d-block text-center text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                  </a>
                </div>
                <div class="col-6">
                  <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-paper-plane me-2"></i>Kirim Pendaftaran
                  </button>
                </div>
              </div>

            </form>
          </div>
        </div>

        <!-- Kanan: Info & Benefit -->
        <div class="col-lg-5">
          <div class="benefits-section">
            <h5 class="fw-bold mb-4" style="color:var(--text-color);">
              <i class="fas fa-graduation-cap me-2" style="color:#0d6efd;"></i>
              Keuntungan Bergabung
            </h5>

            <div class="benefit-item">
              <div class="icon"><i class="fas fa-chalkboard-user"></i></div>
              <div class="info">
                <h6>Guru Berpengalaman</h6>
                <span>Pengajar profesional dan tersertifikasi di bidangnya</span>
              </div>
            </div>

            <div class="benefit-item">
              <div class="icon"><i class="fas fa-calendar-days"></i></div>
              <div class="info">
                <h6>Jadwal Fleksibel</h6>
                <span>Atur jadwal belajar sesuai waktu yang kamu punya</span>
              </div>
            </div>

            <div class="benefit-item">
              <div class="icon"><i class="fas fa-chart-line"></i></div>
              <div class="info">
                <h6>Tracking Progress</h6>
                <span>Pantau perkembangan nilai dan absensi secara real-time</span>
              </div>
            </div>

            <div class="benefit-item">
              <div class="icon"><i class="fas fa-gift"></i></div>
              <div class="info">
                <h6>Konsultasi Gratis</h6>
                <span>Sesi konsultasi awal gratis sebelum mulai belajar</span>
              </div>
            </div>

            <!-- Kontak -->
            <div class="contact-card mt-4">
              <h6><i class="fas fa-headset me-2" style="color:#0d6efd;"></i>Hubungi Kami</h6>
              <a href="https://wa.me/6281234567890" class="whatsapp" target="_blank">
                <i class="fab fa-whatsapp" style="color:#25d366;"></i>
                +62 812-3456-7890
              </a>
              <a href="https://instagram.com/bimbelku" class="instagram" target="_blank">
                <i class="fab fa-instagram" style="color:#e1306c;"></i>
                @bimbelku
              </a>
              <a href="mailto:info@bimbelku.com">
                <i class="fas fa-envelope" style="color:#0d6efd;"></i>
                info@bimbelku.com
              </a>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<footer id="kontak" class="footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="footer-brand">
          <div class="logo-icon" style="width:45px;height:45px;font-size:20px;"><i class="fas fa-book-open"></i></div>
          <span class="logo-text">BimbelKu</span>
        </div>
        <p style="opacity:0.7;line-height:1.8;">Platform bimbel modern terbaik untuk masa depan cerah anak Anda.</p>
        <div class="social-links">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-youtube"></i></a>
        </div>
      </div>
      <div class="col-lg-2">
        <h5>Menu</h5>
        <ul class="footer-links">
          <li><a href="index.php">Beranda</a></li>
          <li><a href="index.php#fitur">Fitur</a></li>
          <li><a href="index.php#testimoni">Testimoni</a></li>
          <li><a href="index.php?page=login">Login</a></li>
        </ul>
      </div>
      <div class="col-lg-3">
        <h5>Kontak</h5>
        <ul class="footer-links">
          <li><a href="#"><i class="fas fa-map-marker-alt me-2"></i>Jember, Indonesia</a></li>
          <li><a href="#"><i class="fas fa-phone me-2"></i>+62 812-3456-7890</a></li>
          <li><a href="#"><i class="fas fa-envelope me-2"></i>info@bimbelku.com</a></li>
        </ul>
      </div>
      <div class="col-lg-3">
        <h5>Jam Operasional</h5>
        <ul class="footer-links">
          <li><a href="#">Senin - Jumat: 08.00 - 21.00</a></li>
          <li><a href="#">Sabtu: 09.00 - 17.00</a></li>
          <li><a href="#">Minggu: 10.00 - 15.00</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2026 BimbelKu. All rights reserved. Made with <i class="fas fa-heart text-danger"></i> in Indonesia</p>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="public/js/main.js"></script>
</body>
</html>
