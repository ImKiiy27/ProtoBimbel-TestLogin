<?php
// ============================================================
// controllers/GuruController.php
// ============================================================

class GuruController {

  private function render(string $view, array $data = []): void {
    extract($data);
    require __DIR__ . "/../views/{$view}.php";
  }

  public function dashboard(): void {
    $pageTitle  = 'Dashboard Guru - BimbelKu';
    $activePage = 'guru-dashboard';
    $this->render('guru/dashboard', compact('pageTitle', 'activePage'));
  }

  public function jadwal(): void {
    $pageTitle  = 'Jadwal Mengajar - BimbelKu';
    $activePage = 'guru-jadwal';
    $this->render('guru/jadwal', compact('pageTitle', 'activePage'));
  }

  public function absensi(): void {
    $pageTitle  = 'Input Absensi - BimbelKu';
    $activePage = 'guru-absensi';
    $this->render('guru/absensi', compact('pageTitle', 'activePage'));
  }

  public function nilai(): void {
    $pageTitle  = 'Input Nilai - BimbelKu';
    $activePage = 'guru-nilai';
    $this->render('guru/nilai', compact('pageTitle', 'activePage'));
  }

  public function profil(): void {
    $pageTitle  = 'Profil - BimbelKu';
    $activePage = 'guru-profil';
    $this->render('guru/profil', compact('pageTitle', 'activePage'));
  }
}
