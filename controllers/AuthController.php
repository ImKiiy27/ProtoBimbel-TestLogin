<?php
// ============================================================
// controllers/AuthController.php
// Tugasnya: terima input → minta Model proses → kirim ke View
// ============================================================

require_once __DIR__ . '/../models/AuthModel.php';
require_once __DIR__ . '/../models/PendaftaranModel.php';

class AuthController {

  private AuthModel $authModel;
  private PendaftaranModel $pendaftaranModel;

  public function __construct() {
    $this->authModel = new AuthModel();
    $this->pendaftaranModel = new PendaftaranModel();
  }

  // ----------------------------------------------------------
  // LANDING PAGE
  // ----------------------------------------------------------
  public function index(): void {
    $this->render('auth/index');
  }

  // ----------------------------------------------------------
  // LOGIN
  // ----------------------------------------------------------
  public function login(): void {
    // Kalau sudah login, langsung redirect ke dashboard
    if (isset($_SESSION['user_id'])) {
      $this->redirectByRole($_SESSION['role']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!verifyCsrfToken($_POST['_csrf'] ?? null)) {
        $_SESSION['flash_error'] = 'Sesi tidak valid. Muat ulang halaman lalu coba lagi.';
        header('Location: index.php?page=login');
        exit;
      }

      $email    = trim($_POST['email']    ?? '');
      $password =      $_POST['password'] ?? '';

      // --- Validasi input kosong ---
      if (empty($email) || empty($password)) {
        $_SESSION['flash_error'] = 'Email dan password wajib diisi.';

      // --- Validasi format email ---
      } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_error'] = 'Format email tidak valid.';

      } else {
        // Minta Model untuk cek ke database
        $result = $this->authModel->login($email, $password);

        switch ($result['status']) {
          case 'success':
            $user = $result['user'];

            // Regenerate session ID saat login (anti session fixation)
            session_regenerate_id(true);

            // Set data sesi
            $_SESSION['user_id']        = $user['id'];
            $_SESSION['nama']           = $user['nama'] ?? $email;
            $_SESSION['email']          = $user['email'];
            $_SESSION['role']           = $user['role'];
            $_SESSION['last_activity']  = time();
            $_SESSION['created_at']     = time();
            $_SESSION['regenerated_at'] = time();

            $this->redirectByRole($user['role']);
            break;

          case 'locked':
            $_SESSION['flash_error'] = 'Akun Anda terkunci karena terlalu banyak percobaan login. Silakan hubungi admin.';
            break;

          case 'not_found':
            $_SESSION['flash_error'] = 'Email tidak terdaftar.';
            break;

          default:
            $sisa  = $result['sisa'] ?? null;
            // Kalau sisa tidak ada, pakai pesan umum supaya tetap tampil
            $_SESSION['flash_error'] = is_null($sisa)
              ? 'Email atau password salah.'
              : ($sisa > 0
                  ? "Email atau password salah. Sisa percobaan: {$sisa}x."
                  : 'Akun Anda terkunci. Silakan hubungi admin.');
        }
      }

      // Semua jalur POST non-berhasil berakhir di sini: redirect agar PRG
      header('Location: index.php?page=login');
      exit;
    }

    // Ambil flash message dari sesi sebelumnya (misal: sesi timeout)
    $error   = $_SESSION['flash_error']   ?? null;
    $success = $_SESSION['flash_success'] ?? null;
    unset($_SESSION['flash_error'], $_SESSION['flash_success']);

    $this->render('auth/login', compact('error', 'success'));
  }

  // ----------------------------------------------------------
  // LOGOUT
  // ----------------------------------------------------------
  public function logout(): void {
    // Pakai destroySession dari config/session.php
    // tapi tanpa redirect otomatis — kita set flash dulu
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(), '',
        time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
      );
    }

    session_destroy();

    // Mulai sesi baru hanya untuk flash message
    session_start();
    $_SESSION['flash_success'] = 'Anda berhasil logout.';

    header('Location: index.php?page=login');
    exit;
  }

  // ----------------------------------------------------------
  // PENDAFTARAN
  // ----------------------------------------------------------
  public function pendaftaran(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!verifyCsrfToken($_POST['_csrf'] ?? null)) {
        $_SESSION['flash_error'] = 'Sesi tidak valid. Muat ulang halaman lalu coba lagi.';
        header('Location: index.php?page=pendaftaran');
        exit;
      }

      $nama    = trim($_POST['nama']    ?? '');
      $email   = trim($_POST['email']   ?? '');
      $telepon = trim($_POST['telepon'] ?? '');

      // --- Lapis 1: Validasi input di Controller ---
      if (empty($nama) || empty($email) || empty($telepon)) {
        $_SESSION['flash_error'] = 'Semua field wajib diisi.';

      } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_error'] = 'Format email tidak valid.';

      } elseif (!preg_match('/^[0-9+\-\s]{8,15}$/', $telepon)) {
        $_SESSION['flash_error'] = 'Format nomor telepon tidak valid.';

      } else {
        // --- Lapis 2: Cek cooldown 24 jam di Model ---
        $sisaDetik = $this->pendaftaranModel->cekCooldownPendaftaran($email);

        if ($sisaDetik > 0) {
          $sisaJam   = ceil($sisaDetik / 3600);
          $_SESSION['flash_error'] = "Email ini sudah pernah mendaftar. "
                                   . "Silakan tunggu sekitar {$sisaJam} jam lagi sebelum mendaftar ulang.";

        } else {
          // --- Lapis 3: Simpan ke database lewat Model ---
          $berhasil = $this->pendaftaranModel->daftar($nama, $email, $telepon);

          if ($berhasil) {
            $_SESSION['flash_success'] = 'Pendaftaran berhasil dikirim! '
                                       . 'Admin akan memverifikasi dan menghubungi Anda.';
          } else {
            $_SESSION['flash_error'] = 'Email ini sudah terdaftar atau terjadi kesalahan. Jika sudah pernah jadi pengguna, silakan login.';
          }
        }
      }

      // Tutup siklus POST dengan redirect (PRG)
      header('Location: index.php?page=pendaftaran');
      exit;
    }

    $error   = $_SESSION['flash_error']   ?? null;
    $success = $_SESSION['flash_success'] ?? null;
    unset($_SESSION['flash_error'], $_SESSION['flash_success']);

    $this->render('auth/pendaftaran', compact('error', 'success'));
  }

  // ----------------------------------------------------------
  // PRIVATE HELPERS
  // ----------------------------------------------------------

  // Redirect ke dashboard sesuai role
  private function redirectByRole(string $role): void {
    $page = match ($role) {
      'admin' => 'admin-dashboard',
      'guru'  => 'guru-dashboard',
      'siswa' => 'siswa-dashboard',
      default => 'login',
    };
    header("Location: index.php?page={$page}");
    exit;
  }

  // Render view dengan data
  private function render(string $view, array $data = []): void {
    extract($data);
    require __DIR__ . "/../views/{$view}.php";
  }
}
