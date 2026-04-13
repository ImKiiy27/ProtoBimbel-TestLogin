<?php
// views/layouts/sidebar.php
// Dipanggil dari view dashboard dengan variabel $role & $activePage
$role       = $_SESSION['role']  ?? '';
$nama       = $_SESSION['nama']  ?? 'User';
$activePage = $activePage        ?? '';
$initial    = strtoupper(substr($nama, 0, 1));

$menus = match ($role) {
  'admin' => [
    ['page' => 'admin-dashboard', 'icon' => 'fa-gauge',          'label' => 'Dashboard'],
    ['page' => 'admin-user',      'icon' => 'fa-users-gear',     'label' => 'Kelola User'],
    ['page' => 'admin-siswa',     'icon' => 'fa-user-graduate',  'label' => 'Data Siswa'],
    ['page' => 'admin-guru',      'icon' => 'fa-chalkboard-user','label' => 'Data Guru'],
    ['page' => 'admin-jadwal',    'icon' => 'fa-calendar-days',  'label' => 'Jadwal'],
    ['page' => 'admin-absensi',   'icon' => 'fa-clipboard-list', 'label' => 'Absensi'],
    ['page' => 'admin-nilai',     'icon' => 'fa-chart-bar',      'label' => 'Nilai'],
  ],
  'guru' => [
    ['page' => 'guru-dashboard', 'icon' => 'fa-gauge',          'label' => 'Dashboard'],
    ['page' => 'guru-jadwal',    'icon' => 'fa-calendar-days',  'label' => 'Jadwal Mengajar'],
    ['page' => 'guru-absensi',   'icon' => 'fa-clipboard-list', 'label' => 'Input Absensi'],
    ['page' => 'guru-nilai',     'icon' => 'fa-chart-bar',      'label' => 'Input Nilai'],
    ['page' => 'guru-profil',    'icon' => 'fa-circle-user',    'label' => 'Profil'],
  ],
  'siswa' => [
    ['page' => 'siswa-dashboard', 'icon' => 'fa-gauge',         'label' => 'Dashboard'],
    ['page' => 'siswa-jadwal',    'icon' => 'fa-calendar-days', 'label' => 'Jadwal Les'],
    ['page' => 'siswa-absensi',   'icon' => 'fa-clipboard-list','label' => 'Absensi'],
    ['page' => 'siswa-nilai',     'icon' => 'fa-chart-bar',     'label' => 'Nilai'],
    ['page' => 'siswa-profil',    'icon' => 'fa-circle-user',   'label' => 'Profil'],
  ],
  default => [],
};

$roleLabel = match ($role) {
  'admin' => 'Administrator',
  'guru'  => 'Pengajar',
  'siswa' => 'Siswa',
  default => '',
};
?>

<div class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="logo-icon">
      <i class="fas fa-book-open"></i>
    </div>
    <span class="logo-text">Bimbel Orion</span>
  </div>

  <div class="sidebar-menu">
    <h6>Menu Utama</h6>
    <?php foreach ($menus as $menu): ?>
      <a href="index.php?page=<?= $menu['page'] ?>"
         class="<?= $activePage === $menu['page'] ? 'active' : '' ?>">
        <i class="fas <?= $menu['icon'] ?>"></i>
        <?= $menu['label'] ?>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="sidebar-profile">
   
    <div class="info">
      <div class="name"><?= htmlspecialchars($nama) ?></div>
      <div class="role"><?= $roleLabel ?></div>
    </div>
  </div>

  <a href="index.php?page=logout" class="mt-3" style="color: #ff6b6b; font-size: 0.9rem;">
    <i class="fas fa-right-from-bracket"></i> Logout
  </a>
</div>
