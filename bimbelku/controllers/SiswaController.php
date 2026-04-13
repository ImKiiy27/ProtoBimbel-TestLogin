<?php
// ============================================================
// controllers/SiswaController.php
// ============================================================

class SiswaController {

  private function render(string $view, array $data = []): void {
    extract($data);
    require __DIR__ . "/../views/{$view}.php";
  }

  public function dashboard(): void {
    $pageTitle  = 'Dashboard Siswa - BimbelKu';
    $activePage = 'siswa-dashboard';
    $this->render('siswa/dashboard', compact('pageTitle', 'activePage'));
  }

  public function jadwal(): void {
    $pageTitle  = 'Jadwal Les - BimbelKu';
    $activePage = 'siswa-jadwal';
    $this->render('siswa/jadwal', compact('pageTitle', 'activePage'));
  }

  public function absensi(): void {
    $pageTitle  = 'Absensi - BimbelKu';
    $activePage = 'siswa-absensi';
    $this->render('siswa/absensi', compact('pageTitle', 'activePage'));
  }

  public function nilai(): void {
    $pageTitle  = 'Nilai - BimbelKu';
    $activePage = 'siswa-nilai';
    $this->render('siswa/nilai', compact('pageTitle', 'activePage'));
  }

  public function profil(): void {
    $pageTitle  = 'Profil - BimbelKu';
    $activePage = 'siswa-profil';
    $this->render('siswa/profil', compact('pageTitle', 'activePage'));
  }
}
