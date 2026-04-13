<?php
$pageTitle  = $pageTitle  ?? 'Kelola User - BimbelKu';
$activePage = $activePage ?? 'admin-user';
require __DIR__ . '/../layouts/header.php';
?>

<div class="bg-shapes">
  <div class="shape shape-1"></div>
  <div class="shape shape-2"></div>
</div>

<div class="dashboard-container">
  <?php require __DIR__ . '/../layouts/sidebar.php'; ?>
  <main class="main-content">
    <?php
      $rawTitle    = $pageTitle ?? 'Dashboard';
      $cleanTitle  = preg_replace('/\\s*-\\s*BimbelKu$/i', '', $rawTitle);
      $pageHeading = trim($cleanTitle ?: 'Dashboard');
    ?>
    <div class="dashboard-navbar">
      <div class="navbar-left">
        <button class="burger-btn" id="sidebarToggle" aria-label="Tampilkan/sembunyikan sidebar" aria-expanded="false">
          <i class="fas fa-bars"></i>
        </button>
        <div class="navbar-title">
          <span class="navbar-label">Halaman</span>
          <h2 title="<?= htmlspecialchars($pageHeading) ?>"><?= htmlspecialchars($pageHeading) ?></h2>
        </div>
      </div>
      <div class="navbar-right">
        <button class="theme-toggle" id="themeToggle" title="Toggle Dark Mode">
          <i class="fas fa-moon"></i>
        </button>
      </div>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger alert-custom mt-3" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
      <div class="alert alert-success alert-custom mt-3" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <div class="row g-4 mt-2">
      <div class="col-12">
        <div class="content-card animate-fade-in w-100">
          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
              <p class="text-muted mb-1">Form</p>
              <h5 class="mb-0">Tambah User</h5>
            </div>
            <i class="fas fa-user-plus text-primary fs-4"></i>
          </div>
          <form method="POST" action="index.php?page=admin-user" autocomplete="off">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(getCsrfToken()) ?>">
            <input type="hidden" name="action" value="create">
            <div class="row g-3" id="createRoleFields">
              <div class="col-md-6 col-lg-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
              </div>
              <div class="col-md-6 col-lg-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" minlength="6" required>
                <small class="text-muted">Minimal 6 karakter</small>
              </div>
              <div class="col-md-6 col-lg-4">
                <label class="form-label">Role</label>
                <select class="form-select" name="role" id="createRole" required>
                  <option value="admin">Admin</option>
                  <option value="guru">Guru</option>
                  <option value="siswa">Siswa</option>
                </select>
              </div>
              <div class="col-md-6 d-none" data-roles="guru,siswa">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" maxlength="100">
              </div>
              <div class="col-md-6 d-none" data-roles="guru">
                <label class="form-label">Mata Pelajaran</label>
                <input type="text" name="mapel" class="form-control" maxlength="100" placeholder="Contoh: Matematika, Fisika">
                <small class="text-muted">Boleh dikosongkan, akan diset ke "Privat".</small>
              </div>
              <div class="col-md-6 d-none" data-roles="siswa">
                <label class="form-label">Kelas/Program</label>
                <input type="text" name="kelas" class="form-control" maxlength="20" placeholder="Contoh: Privat">
                <small class="text-muted">Boleh dikosongkan, akan diset ke "Privat".</small>
              </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
              <button type="submit" class="btn btn-primary px-4">
                <i class="fas fa-save me-1"></i> Simpan
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="col-12">
        <div class="content-card animate-fade-in w-100">
          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
              <p class="text-muted mb-1">Daftar</p>
              <h5 class="mb-0">User Terdaftar</h5>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <span class="badge bg-primary-subtle text-primary">Total <?= count($users ?? []) ?></span>
              <select id="roleFilter" class="form-select form-select-sm" style="min-width:180px;">
                <option value="all">Semua Role</option>
                <option value="admin">Admin</option>
                <option value="guru">Guru</option>
                <option value="siswa">Siswa</option>
              </select>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table align-middle table-hover" id="userTable">
              <thead>
                <tr>
                  <th>Email</th>
                  <th>Nama</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Data Detail</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($users)): ?>
                  <?php foreach ($users as $user): ?>
                    <?php
                      $displayName = $user['guru_nama'] ?? $user['siswa_nama'] ?? '-';
                      $hasDetail   = ($user['role'] === 'guru' && !empty($user['guru_nama']))
                                  || ($user['role'] === 'siswa' && !empty($user['siswa_nama']));
                      $subInfo     = $user['role'] === 'guru'
                        ? ($user['mapel'] ? 'Mapel: ' . $user['mapel'] : 'Mapel: Privat')
                        : ($user['role'] === 'siswa'
                            ? ('Program: ' . ($user['kelas'] ?: 'Privat'))
                            : '');
                      $created     = date('d M Y', strtotime($user['created_at']));
                      $locked      = (int)$user['is_locked'] === 1;
                    ?>
                    <tr data-role="<?= htmlspecialchars($user['role']) ?>">
                      <td>
                        <div class="fw-semibold"><?= htmlspecialchars($user['email']) ?></div>
                        <div class="small text-muted">ID: <?= htmlspecialchars($user['id']) ?></div>
                      </td>
                      <td>
                        <div><?= htmlspecialchars($displayName) ?></div>
                        <?php if ($subInfo): ?>
                          <div class="small text-muted"><?= htmlspecialchars($subInfo) ?></div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($user['role'])) ?></span><br>
                        <span class="small text-muted"><?= $created ?></span>
                      </td>
                      <td>
                        <?php if ($locked): ?>
                          <span class="badge bg-danger">Terkunci</span>
                        <?php else: ?>
                          <span class="badge bg-success">Aktif</span>
                        <?php endif; ?>
                        <div class="small text-muted">Attempt: <?= (int)$user['attempts'] ?></div>
                      </td>
                      <td>
                        <?php if ($user['role'] === 'admin'): ?>
                          <span class="badge bg-info text-dark">Admin</span>
                        <?php elseif ($hasDetail): ?>
                          <span class="badge bg-success">Data lengkap</span>
                        <?php else: ?>
                          <span class="badge bg-warning text-dark">Belum ada data</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-nowrap">
                        <button
                          type="button"
                          class="btn btn-sm btn-outline-primary me-1 edit-user-btn"
                          data-bs-toggle="modal"
                          data-bs-target="#editUserModal"
                          data-id="<?= htmlspecialchars($user['id']) ?>"
                          data-email="<?= htmlspecialchars($user['email']) ?>"
                          data-role="<?= htmlspecialchars($user['role']) ?>"
                          data-nama="<?= htmlspecialchars($displayName) ?>"
                          data-mapel="<?= htmlspecialchars($user['mapel'] ?? '') ?>"
                          data-kelas="<?= htmlspecialchars($user['kelas'] ?? '') ?>"
                        >
                          <i class="fas fa-pen"></i>
                        </button>

                        <form method="POST" action="index.php?page=admin-user" class="d-inline">
                          <input type="hidden" name="_csrf" value="<?= htmlspecialchars(getCsrfToken()) ?>">
                          <input type="hidden" name="action" value="unlock">
                          <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>">
                          <button type="submit" class="btn btn-sm btn-outline-secondary me-1" <?= $locked ? '' : 'disabled' ?>>
                            <i class="fas fa-unlock"></i>
                          </button>
                        </form>

                        <form method="POST" action="index.php?page=admin-user" class="d-inline" onsubmit="return confirm('Hapus user ini?');">
                          <input type="hidden" name="_csrf" value="<?= htmlspecialchars(getCsrfToken()) ?>">
                          <input type="hidden" name="action" value="delete">
                          <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>">
                          <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada data user.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
            <div class="small text-muted" id="paginationInfo"></div>
            <nav aria-label="Pagination">
              <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="index.php?page=admin-user" autocomplete="off" id="editUserForm">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars(getCsrfToken()) ?>">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="user_id" id="editUserId">
        <input type="hidden" name="role" id="editRoleHidden">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" id="editEmail" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Password Baru</label>
              <input type="password" name="password" id="editPassword" class="form-control" minlength="6" placeholder="Kosongkan jika tidak ganti">
            </div>
            <div class="col-md-6">
              <label class="form-label">Role</label>
              <select class="form-select" id="editRole" disabled>
                <option value="admin">Admin</option>
                <option value="guru">Guru</option>
                <option value="siswa">Siswa</option>
              </select>
              <small class="text-muted">Role tidak dapat diubah di sini.</small>
            </div>
            <div class="col-12 d-none" id="editNamaGroup" data-roles="guru,siswa">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" name="nama" id="editNama" class="form-control" maxlength="100">
            </div>
            <div class="col-md-6 d-none" data-roles="guru">
              <label class="form-label">Mata Pelajaran</label>
              <input type="text" name="mapel" id="editMapel" class="form-control" maxlength="100" placeholder="Contoh: Matematika">
              <small class="text-muted">Kosongkan untuk tetap/auto "Privat".</small>
            </div>
            <div class="col-md-6 d-none" data-roles="siswa">
              <label class="form-label">Kelas/Program</label>
              <input type="text" name="kelas" id="editKelas" class="form-control" maxlength="20" placeholder="Contoh: Privat">
              <small class="text-muted">Kosongkan untuk tetap/auto "Privat".</small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(() => {
  const createRole = document.getElementById('createRole');
  const createFields = document.getElementById('createRoleFields');
  const editModal = document.getElementById('editUserModal');
  const editRoleSelect = document.getElementById('editRole');
  const editRoleHidden = document.getElementById('editRoleHidden');
  const roleFilter = document.getElementById('roleFilter');
  const pagination = document.getElementById('pagination');
  const paginationInfo = document.getElementById('paginationInfo');
  const tableBody = document.querySelector('#userTable tbody');
  const pageSize = 8;
  let currentPage = 1;

  const toggleFields = (container, role) => {
    if (!container) return;
    container.querySelectorAll('[data-roles]').forEach(el => {
      const roles = (el.dataset.roles || '').split(',').map(r => r.trim());
      const show  = roles.includes(role);
      el.classList.toggle('d-none', !show);
      el.querySelectorAll('input,select,textarea').forEach(input => { input.disabled = !show; });
    });
  };

  if (createRole) {
    createRole.addEventListener('change', (e) => toggleFields(createFields, e.target.value));
    toggleFields(createFields, createRole.value);
  }

  if (editModal) {
    editModal.addEventListener('show.bs.modal', (event) => {
      const button = event.relatedTarget;
      const role   = button.getAttribute('data-role');

      document.getElementById('editUserId').value = button.getAttribute('data-id');
      document.getElementById('editEmail').value  = button.getAttribute('data-email');
      document.getElementById('editNama').value   = button.getAttribute('data-nama');
      document.getElementById('editMapel').value  = button.getAttribute('data-mapel');
      document.getElementById('editKelas').value  = button.getAttribute('data-kelas');
      document.getElementById('editPassword').value = '';

      editRoleSelect.value  = role;
      editRoleHidden.value  = role;
      toggleFields(editModal, role);
    });
  }

  const renderPagination = (totalPages) => {
    if (!pagination) return;
    pagination.innerHTML = '';
    if (totalPages <= 1) return;

    const addItem = (label, page, disabled = false, active = false) => {
      const li = document.createElement('li');
      li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
      const a = document.createElement('a');
      a.className = 'page-link';
      a.href = '#';
      a.dataset.page = page;
      a.textContent = label;
      li.appendChild(a);
      pagination.appendChild(li);
    };

    addItem('«', Math.max(1, currentPage - 1), currentPage === 1);
    for (let i = 1; i <= totalPages; i++) {
      addItem(i, i, false, i === currentPage);
    }
    addItem('»', Math.min(totalPages, currentPage + 1), currentPage === totalPages);
  };

  const renderTable = () => {
    if (!tableBody) return;

    const filterVal = roleFilter ? roleFilter.value : 'all';
    const allRows = Array.from(tableBody.querySelectorAll('tr[data-role]'));
    const placeholders = Array.from(tableBody.querySelectorAll('tr:not([data-role]):not([data-empty])'));
    placeholders.forEach(r => r.style.display = 'none');
    let emptyRow = tableBody.querySelector('tr[data-empty]');

    const filtered = allRows.filter(row => filterVal === 'all' ? true : row.dataset.role === filterVal);
    const total = filtered.length;
    const totalPages = Math.max(1, Math.ceil(total / pageSize));
    if (currentPage > totalPages) currentPage = totalPages;

    allRows.forEach(row => row.style.display = 'none');

    if (total === 0) {
      if (!emptyRow) {
        emptyRow = document.createElement('tr');
        emptyRow.setAttribute('data-empty', 'true');
        const td = document.createElement('td');
        td.colSpan = 6;
        td.className = 'text-center text-muted py-4';
        td.textContent = 'Tidak ada data untuk filter ini.';
        emptyRow.appendChild(td);
        tableBody.appendChild(emptyRow);
      }
      emptyRow.style.display = '';
      if (pagination) pagination.innerHTML = '';
      if (paginationInfo) paginationInfo.textContent = '0 data';
      return;
    }

    if (emptyRow) emptyRow.style.display = 'none';

    const start = (currentPage - 1) * pageSize;
    const end = start + pageSize;
    filtered.slice(start, end).forEach(row => row.style.display = '');

    renderPagination(totalPages);
    if (paginationInfo) {
      paginationInfo.textContent = `Menampilkan ${start + 1}-${Math.min(end, total)} dari ${total} data`;
    }
  };

  if (roleFilter) {
    roleFilter.addEventListener('change', () => { currentPage = 1; renderTable(); });
  }

  if (pagination) {
    pagination.addEventListener('click', (e) => {
      const target = e.target;
      if (target.tagName.toLowerCase() === 'a' && target.dataset.page) {
        e.preventDefault();
        currentPage = parseInt(target.dataset.page, 10);
        renderTable();
      }
    });
  }

  renderTable();
})();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
