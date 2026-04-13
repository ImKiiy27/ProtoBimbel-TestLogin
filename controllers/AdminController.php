<?php
// ============================================================
// controllers/AdminController.php
// ============================================================

require_once __DIR__ . '/../models/AdminUserModel.php';
require_once __DIR__ . '/../models/PendaftaranModel.php';

class AdminController {

  private AdminUserModel $adminUserModel;
  private PendaftaranModel $pendaftaranModel;

  public function __construct() {
    $this->adminUserModel = new AdminUserModel();
    $this->pendaftaranModel = new PendaftaranModel();
  }

  private function render(string $view, array $data = []): void {
    extract($data);
    require __DIR__ . "/../views/{$view}.php";
  }

  public function dashboard(): void {
    $pageTitle     = 'Dashboard Admin - BimbelKu';
    $activePage    = 'admin-dashboard';

    // Ambil 10 pendaftaran terbaru
    $pendaftaran = $this->pendaftaranModel->getPendaftaranTerbaru(10);
    $totalPendaftar = $this->pendaftaranModel->countPendaftaran();

    $this->render('admin/dashboard', compact('pageTitle', 'activePage', 'pendaftaran', 'totalPendaftar'));
  }

  public function siswa(): void {
    $pageTitle  = 'Data Siswa - BimbelKu';
    $activePage = 'admin-siswa';
    $this->render('admin/siswa', compact('pageTitle', 'activePage'));
  }

  public function guru(): void {
    $pageTitle  = 'Data Guru - BimbelKu';
    $activePage = 'admin-guru';
    $this->render('admin/guru', compact('pageTitle', 'activePage'));
  }

  public function jadwal(): void {
    $pageTitle  = 'Jadwal - BimbelKu';
    $activePage = 'admin-jadwal';
    $this->render('admin/jadwal', compact('pageTitle', 'activePage'));
  }

  public function absensi(): void {
    $pageTitle  = 'Absensi - BimbelKu';
    $activePage = 'admin-absensi';
    $this->render('admin/absensi', compact('pageTitle', 'activePage'));
  }

  public function nilai(): void {
    $pageTitle  = 'Nilai - BimbelKu';
    $activePage = 'admin-nilai';
    $this->render('admin/nilai', compact('pageTitle', 'activePage'));
  }

  public function user(): void {
    $pageTitle  = 'Kelola User - BimbelKu';
    $activePage = 'admin-user';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!verifyCsrfToken($_POST['_csrf'] ?? null)) {
        $_SESSION['flash_error'] = 'Sesi tidak valid. Muat ulang halaman lalu coba lagi.';
        header('Location: index.php?page=admin-user');
        exit;
      }

      $action = $_POST['action'] ?? '';
      match ($action) {
        'create' => $this->handleCreateUser(),
        'update' => $this->handleUpdateUser(),
        'delete' => $this->handleDeleteUser(),
        'unlock' => $this->handleUnlockUser(),
        default  => $_SESSION['flash_error'] = 'Aksi tidak dikenal.',
      };

      header('Location: index.php?page=admin-user');
      exit;
    }

    $users   = $this->adminUserModel->getAllUsersWithDetail();
    $error   = $_SESSION['flash_error']   ?? null;
    $success = $_SESSION['flash_success'] ?? null;
    unset($_SESSION['flash_error'], $_SESSION['flash_success']);

    $this->render('admin/user', compact('pageTitle', 'activePage', 'users', 'error', 'success'));
  }

  // ----------------------------------------------------------
  // PRIVATE: USER CRUD HELPERS
  // ----------------------------------------------------------
  private function handleCreateUser(): void {
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';
    $role     = trim($_POST['role']     ?? '');
    $nama     = trim($_POST['nama']     ?? '');
    $mapel    = trim($_POST['mapel']    ?? '');
    $kelas    = trim($_POST['kelas']    ?? '');

    $error = $this->validateUserForm($email, $password, $role, $nama, $mapel, $kelas, true);
    if ($error) {
      $_SESSION['flash_error'] = $error;
      return;
    }

    $result = $this->adminUserModel->createUser([
      'email'    => $email,
      'password' => $password,
      'role'     => $role,
      'nama'     => $nama,
      'mapel'    => $mapel,
      'kelas'    => $kelas,
    ]);

    $_SESSION['flash_' . $result['status']] = $result['status'] === 'success'
      ? 'User baru berhasil dibuat.'
      : ($result['message'] ?? 'Gagal membuat user.');
  }

  private function handleUpdateUser(): void {
    $userId   = trim($_POST['user_id']  ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password =      $_POST['password'] ?? '';
    $role     = trim($_POST['role']     ?? '');
    $nama     = trim($_POST['nama']     ?? '');
    $mapel    = trim($_POST['mapel']    ?? '');
    $kelas    = trim($_POST['kelas']    ?? '');

    if (empty($userId)) {
      $_SESSION['flash_error'] = 'User ID tidak valid.';
      return;
    }

    $error = $this->validateUserForm($email, $password, $role, $nama, $mapel, $kelas, false);
    if ($error) {
      $_SESSION['flash_error'] = $error;
      return;
    }

    $result = $this->adminUserModel->updateUser($userId, [
      'email'    => $email,
      'password' => $password,
      'role'     => $role,
      'nama'     => $nama,
      'mapel'    => $mapel,
      'kelas'    => $kelas,
      'nip'      => '', // akan dipertahankan/otomatis jika kosong
      'nis'      => '',
    ]);

    $_SESSION['flash_' . $result['status']] = $result['status'] === 'success'
      ? 'User berhasil diperbarui.'
      : ($result['message'] ?? 'Gagal memperbarui user.');
  }

  private function handleDeleteUser(): void {
    $userId = trim($_POST['user_id'] ?? '');
    if (empty($userId)) {
      $_SESSION['flash_error'] = 'User ID tidak valid.';
      return;
    }

    $result = $this->adminUserModel->deleteUser($userId);
    $_SESSION['flash_' . $result['status']] = $result['status'] === 'success'
      ? 'User berhasil dihapus.'
      : ($result['message'] ?? 'Gagal menghapus user.');
  }

  private function handleUnlockUser(): void {
    $userId = trim($_POST['user_id'] ?? '');
    if (empty($userId)) {
      $_SESSION['flash_error'] = 'User ID tidak valid.';
      return;
    }

    $this->adminUserModel->unlock($userId);
    $_SESSION['flash_success'] = 'Status kunci/percobaan login sudah direset.';
  }

  private function validateUserForm(
    string $email,
    string $password,
    string $role,
    string $nama,
    string $mapel,
    string $kelas,
    bool $isCreate
  ): ?string {
    $allowedRoles = ['admin', 'guru', 'siswa'];

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return 'Email tidak valid.';
    }

    if ($isCreate && strlen($password) < 6) {
      return 'Password minimal 6 karakter.';
    }

    if (!$isCreate && $password !== '' && strlen($password) < 6) {
      return 'Password baru minimal 6 karakter atau kosongkan untuk tetap.';
    }

    if (!in_array($role, $allowedRoles, true)) {
      return 'Role tidak valid.';
    }

    if ($role === 'guru') {
      if (empty($nama)) {
        return 'Nama wajib diisi untuk guru.';
      }
    } elseif ($role === 'siswa') {
      if (empty($nama)) {
        return 'Nama wajib diisi untuk siswa.';
      }
    }

    return null;
  }
}
