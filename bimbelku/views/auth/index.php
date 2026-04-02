<?php
$pageTitle = 'BimbelKu - Platform Pembelajaran Terbaik';
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
        <li class="nav-item"><a class="nav-link" href="#fitur">Fitur</a></li>
        <li class="nav-item"><a class="nav-link" href="#testimoni">Testimoni</a></li>
        <li class="nav-item ms-3">
          <button class="theme-toggle" id="themeToggle" style="width:40px;height:40px;font-size:18px;">
            <i class="fas fa-moon"></i>
          </button>
        </li>
        <li class="nav-item ms-3"><a href="index.php?page=login" class="btn btn-login px-4">Login</a></li>
        <li class="nav-item ms-2"><a href="index.php?page=pendaftaran" class="btn btn-daftar px-4">Daftar</a></li>
      </ul>
    </div>
  </div>
</nav>

<section class="hero d-flex align-items-center">
  <div class="container hero-content text-center">
    <h1 class="fade-in">Belajar Lebih Mudah dengan BimbelKu</h1>
    <p class="fade-in">Platform manajemen bimbel modern untuk siswa dan guru dengan fitur terlengkap</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap fade-in">
      <a href="index.php?page=pendaftaran" class="btn btn-hero btn-hero-primary">
        <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
      </a>
      <a href="#fitur" class="btn btn-hero btn-hero-outline">
        <i class="fas fa-info-circle me-2"></i> Pelajari Lebih Lanjut
      </a>
    </div>
  </div>
</section>

<section id="fitur" class="container my-5 py-5">
  <div class="text-center mb-5">
    <h2 class="fw-bold" style="font-size:2.5rem;color:var(--text-color);">Mengapa Memilih Kami?</h2>
    <p style="color:var(--text-muted);font-size:1.1rem;">Fitur terlengkap untuk pengalaman belajar yang maksimal</p>
  </div>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="feature-card fade-in">
        <div class="feature-icon"><i class="fas fa-calendar-days"></i></div>
        <h5>Manajemen Jadwal</h5>
        <p>Kelola jadwal les privat dengan mudah dan terorganisir.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card fade-in">
        <div class="feature-icon"><i class="fas fa-chalkboard-user"></i></div>
        <h5>Guru Berkualitas</h5>
        <p>Pengajar berpengalaman dan tersertifikasi di bidangnya.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card fade-in">
        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
        <h5>Tracking Progress</h5>
        <p>Pantau perkembangan belajar dengan laporan yang detail.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card fade-in">
        <div class="feature-icon"><i class="fas fa-clipboard-list"></i></div>
        <h5>Rekap Absensi</h5>
        <p>Monitoring kehadiran siswa secara otomatis dan akurat.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card fade-in">
        <div class="feature-icon"><i class="fas fa-laptop"></i></div>
        <h5>Akses Online</h5>
        <p>Siswa dapat login dan memantau progres kapan saja.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card fade-in">
        <div class="feature-icon"><i class="fas fa-headset"></i></div>
        <h5>Support 24/7</h5>
        <p>Tim support siap membantu kapan saja dengan respons cepat.</p>
      </div>
    </div>
  </div>
</section>

<section id="testimoni" class="testimonial-section py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold" style="font-size:2.5rem;color:white;">Apa Kata Mereka?</h2>
      <p style="opacity:0.9;font-size:1.1rem;">Testimoni dari siswa, orang tua, dan guru</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="testimonial-card fade-in"><i class="fas fa-quote-left quote-icon"></i>
          <p>"Nilai saya meningkat drastis sejak join BimbelKu!"</p>
          <div class="testimonial-author">
            <div class="avatar">A</div>
            <div class="info">
              <h6>Ahmad Fauzi</h6><span>Siswa Kelas 12</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial-card fade-in"><i class="fas fa-quote-left quote-icon"></i>
          <p>"Sistem ini sangat memudahkan jadwal mengajar saya."</p>
          <div class="testimonial-author">
            <div class="avatar">S</div>
            <div class="info">
              <h6>Sari Dewi, S.Pd</h6><span>Guru Bahasa Inggris</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testimonial-card fade-in"><i class="fas fa-quote-left quote-icon"></i>
          <p>"Saya bisa memantau progres belajar anak dengan mudah."</p>
          <div class="testimonial-author">
            <div class="avatar">D</div>
            <div class="info">
              <h6>Dewi Lestari</h6><span>Wali Murid</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="py-5 cta-section">
  <div class="container text-center py-4">
    <h2 class="fw-bold mb-3" style="font-size: 2rem; color: var(--text-color);">Siap Mulai Belajar?</h2>
    <p class="mb-4" style="font-size: 1.05rem; color: var(--text-muted);">
      Bergabunglah dengan ribuan siswa lainnya yang sudah merasakan manfaat BimbelKu
    </p>
    <a href="index.php?page=pendaftaran" class="btn btn-login btn-lg">
      <i class="fas fa-user-plus me-2"></i> Daftar Sekarang Gratis
    </a>
  </div>
</section>


<footer id="kontak" class="footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="footer-brand">
          <div class="logo-icon"><i class="fas fa-book-open"></i></div><span class="logo-text">Bimbel Orion</span>
        </div>
        <p style="opacity: 0.7; line-height: 1.8;">Platform bimbel modern terbaik untuk masa depan cerah anak Anda.</p>
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
          <li><a href="#">Beranda</a></li>
          <li><a href="#fitur">Fitur</a></li>
          <li><a href="#testimoni">Testimoni</a></li>
          <li><a href="login.php">Login</a></li>
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
