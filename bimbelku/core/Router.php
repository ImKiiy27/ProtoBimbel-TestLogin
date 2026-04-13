<?php
// ============================================================
// core/Router.php
// Mengarahkan request ke controller yang tepat
// ============================================================

require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AdminController.php';
require_once __DIR__ . '/../controllers/GuruController.php';
require_once __DIR__ . '/../controllers/SiswaController.php';

class Router {

  // Halaman yang bisa diakses tanpa login
  private array $publicPages = ['index', 'login', 'pendaftaran', 'logout'];

  public function dispatch(string $page): void {
    // Cek apakah halaman butuh login
    if (!in_array($page, $this->publicPages)) {
      $this->requireLogin();
    }

    // Routing berdasarkan $page
    match ($page) {

      // --- Public ---
      'index'       => (new AuthController())->index(),
      'login'       => (new AuthController())->login(),
      'logout'      => (new AuthController())->logout(),
      'pendaftaran' => (new AuthController())->pendaftaran(),

      // --- Admin ---
      'admin-dashboard' => $this->requireRole('admin', fn() => (new AdminController())->dashboard()),
      'admin-siswa'     => $this->requireRole('admin', fn() => (new AdminController())->siswa()),
      'admin-guru'      => $this->requireRole('admin', fn() => (new AdminController())->guru()),
      'admin-jadwal'    => $this->requireRole('admin', fn() => (new AdminController())->jadwal()),
      'admin-absensi'   => $this->requireRole('admin', fn() => (new AdminController())->absensi()),
      'admin-nilai'     => $this->requireRole('admin', fn() => (new AdminController())->nilai()),
      'admin-user'      => $this->requireRole('admin', fn() => (new AdminController())->user()),

      // --- Guru ---
      'guru-dashboard' => $this->requireRole('guru', fn() => (new GuruController())->dashboard()),
      'guru-jadwal'    => $this->requireRole('guru', fn() => (new GuruController())->jadwal()),
      'guru-absensi'   => $this->requireRole('guru', fn() => (new GuruController())->absensi()),
      'guru-nilai'     => $this->requireRole('guru', fn() => (new GuruController())->nilai()),
      'guru-profil'    => $this->requireRole('guru', fn() => (new GuruController())->profil()),

      // --- Siswa ---
      'siswa-dashboard' => $this->requireRole('siswa', fn() => (new SiswaController())->dashboard()),
      'siswa-jadwal'    => $this->requireRole('siswa', fn() => (new SiswaController())->jadwal()),
      'siswa-absensi'   => $this->requireRole('siswa', fn() => (new SiswaController())->absensi()),
      'siswa-nilai'     => $this->requireRole('siswa', fn() => (new SiswaController())->nilai()),
      'siswa-profil'    => $this->requireRole('siswa', fn() => (new SiswaController())->profil()),

      // --- 404 ---
      default => $this->notFound(),
    };
  }

  // Pastikan user sudah login
  private function requireLogin(): void {
    if (!isset($_SESSION['user_id'])) {
      header('Location: index.php?page=login');
      exit;
    }
  }

  // Pastikan role sesuai
  private function requireRole(string $role, callable $callback): void {
    $this->requireLogin();

    if ($_SESSION['role'] !== $role) {
      // Redirect ke dashboard sesuai role masing-masing
      $redirect = match ($_SESSION['role']) {
        'admin' => 'admin-dashboard',
        'guru'  => 'guru-dashboard',
        'siswa' => 'siswa-dashboard',
        default => 'login',
      };
      header("Location: index.php?page={$redirect}");
      exit;
    }

    $callback();
  }

  private function notFound(): void {
    http_response_code(404);
    echo "<h1>404 - Halaman tidak ditemukan</h1>";
    echo "<a href='index.php'>Kembali ke beranda</a>";
  }
}
