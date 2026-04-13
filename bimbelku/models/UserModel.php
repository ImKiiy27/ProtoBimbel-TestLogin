<?php
// ============================================================
// models/UserModel.php
// Tugasnya HANYA urusan query database
// ============================================================

require_once __DIR__ . '/../config/database.php';

class UserModel {

  private PDO $db;
  private int $maxAttempts = 5;

  public function __construct() {
    $this->db = getDB();
  }

  // Cek apakah email sudah terdaftar sebagai user aktif
  public function emailExistsInUsers(string $email): bool {
    $stmt = $this->db->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    return (bool)$stmt->fetchColumn();
  }

  // Generate ID 3 huruf prefix + 3 digit (misal PND001)
  private function generateId(string $tabel, string $prefix): string {
    $this->db->beginTransaction();
    try {
      $stmt = $this->db->prepare("SELECT last_id FROM id_counter WHERE tabel = ? FOR UPDATE");
      $stmt->execute([$tabel]);
      $row = $stmt->fetch();

      if ($row) {
        $next   = (int)$row['last_id'] + 1;
        $prefix = $row['prefix'];
        $stmt   = $this->db->prepare("UPDATE id_counter SET last_id = ? WHERE tabel = ?");
        $stmt->execute([$next, $tabel]);
      } else {
        $next = 1;
        $stmt = $this->db->prepare("INSERT INTO id_counter (tabel, prefix, last_id) VALUES (?, ?, ?)");
        $stmt->execute([$tabel, $prefix, $next]);
      }

      $this->db->commit();
      return $prefix . str_pad((string)$next, 3, '0', STR_PAD_LEFT);
    } catch (Throwable $e) {
      $this->db->rollBack();
      throw $e;
    }
  }

  // ----------------------------------------------------------
  // LOGIN
  // ----------------------------------------------------------

  // Cek email, password, locked status, dan attempts
  public function login(string $email, string $password): array {
    $stmt = $this->db->prepare("
      SELECT u.*,
             COALESCE(g.nama, s.nama) AS nama
      FROM users u
      LEFT JOIN guru  g ON g.user_id = u.id
      LEFT JOIN siswa s ON s.user_id = u.id
      WHERE u.email = ?
      LIMIT 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Email tidak ditemukan â€” jangan tampilkan info percobaan
    if (!$user) {
      return ['status' => 'not_found'];
    }

    // Akun terkunci
    if ($user['is_locked']) {
      return ['status' => 'locked'];
    }

    // Password salah
    if (!password_verify($password, $user['password'])) {
      $this->tambahAttempt($user['id'], $user['attempts']);
      $sisa = $this->maxAttempts - ($user['attempts'] + 1);
      return ['status' => 'failed', 'sisa' => max(0, $sisa)];
    }

    // Login berhasil — reset attempts
    $this->resetAttempt($user['id']);

    return ['status' => 'success', 'user' => $user];
  }

  // Tambah attempt, kunci akun jika sudah melebihi batas
  private function tambahAttempt(string $userId, int $currentAttempts): void {
    $newAttempts = $currentAttempts + 1;
    $locked      = $newAttempts >= $this->maxAttempts ? 1 : 0;

    $stmt = $this->db->prepare(
      "UPDATE users SET attempts = ?, is_locked = ? WHERE id = ?"
    );
    $stmt->execute([$newAttempts, $locked, $userId]);
  }

  // Reset attempt setelah login berhasil
  private function resetAttempt(string $userId): void {
    $stmt = $this->db->prepare(
      "UPDATE users SET attempts = 0, is_locked = 0 WHERE id = ?"
    );
    $stmt->execute([$userId]);
  }

  // ----------------------------------------------------------
  // PENDAFTARAN
  // ----------------------------------------------------------

  public function countPendaftaran(): int {
    $stmt = $this->db->query("SELECT COUNT(*) FROM pendaftaran");
    return (int)$stmt->fetchColumn();
  }

  // Cek apakah email sudah mendaftar dalam 24 jam terakhir
  // Mengembalikan sisa detik jika masih dalam cooldown, 0 jika bebas
  public function cekCooldownPendaftaran(string $email): int {
    $stmt = $this->db->prepare("
      SELECT created_at FROM pendaftaran
      WHERE email = ?
      ORDER BY created_at DESC
      LIMIT 1
    ");
    $stmt->execute([$email]);
    $row = $stmt->fetch();

    if (!$row) return 0;

    $selisih  = time() - strtotime($row['created_at']);
    $cooldown = 24 * 60 * 60; // 24 jam dalam detik

    // Kalau masih dalam 24 jam, kembalikan sisa detik
    if ($selisih < $cooldown) {
      return $cooldown - $selisih;
    }

    return 0;
  }

  // Simpan data pendaftaran baru
  public function daftar(string $nama, string $email, string $telepon): bool {
    // Tolak jika sudah jadi user aktif
    if ($this->emailExistsInUsers($email)) {
      return false;
    }

    $id = $this->generateId('pendaftaran', 'PND');

    $stmt = $this->db->prepare("
      INSERT INTO pendaftaran (id, nama, email, telepon, status)
      VALUES (?, ?, ?, ?, 'pending')
    ");
    return $stmt->execute([$id, $nama, $email, $telepon]);
  }

  public function getPendaftaranTerbaru(int $limit = 10): array {
    $stmt = $this->db->prepare("
      SELECT id, nama, email, telepon, status, created_at
      FROM pendaftaran
      ORDER BY created_at DESC
      LIMIT ?
    ");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  // ----------------------------------------------------------
  // UTILITY
  // ----------------------------------------------------------

  // Ambil data user by ID
  public function findById(string $id): array|false {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
  }

  // Unlock akun (oleh admin)
  public function unlock(string $userId): bool {
    $stmt = $this->db->prepare(
      "UPDATE users SET is_locked = 0, attempts = 0 WHERE id = ?"
    );
    return $stmt->execute([$userId]);
  }

  // ----------------------------------------------------------
  // ADMIN: USER CRUD
  // ----------------------------------------------------------

  // Ambil semua user beserta detail guru/siswa (jika ada)
  public function getAllUsersWithDetail(): array {
    $stmt = $this->db->query("
      SELECT
        u.id,
        u.email,
        u.role,
        u.is_locked,
        u.attempts,
        u.created_at,
        g.id   AS guru_id,
        g.nama AS guru_nama,
        g.nip,
        g.mapel,
        s.id   AS siswa_id,
        s.nama AS siswa_nama,
        s.nis,
        s.kelas
      FROM users u
      LEFT JOIN guru  g ON g.user_id = u.id
      LEFT JOIN siswa s ON s.user_id = u.id
      ORDER BY u.created_at DESC
    ");
    return $stmt->fetchAll();
  }

  // Ambil satu user + detail
  public function getUserWithDetail(string $userId): array|false {
    $stmt = $this->db->prepare("
      SELECT
        u.*,
        g.id   AS guru_id,
        g.nama AS guru_nama,
        g.nip,
        g.mapel,
        s.id   AS siswa_id,
        s.nama AS siswa_nama,
        s.nis,
        s.kelas
      FROM users u
      LEFT JOIN guru  g ON g.user_id = u.id
      LEFT JOIN siswa s ON s.user_id = u.id
      WHERE u.id = ?
      LIMIT 1
    ");
    $stmt->execute([$userId]);
    return $stmt->fetch();
  }

  public function createUser(array $data): array {
    $this->db->beginTransaction();
    try {
      $userId = $this->generateId('users', 'USR');
      $stmt   = $this->db->prepare("
        INSERT INTO users (id, email, password, role, is_locked, attempts)
        VALUES (?, ?, ?, ?, 0, 0)
      ");
      $stmt->execute([
        $userId,
        $data['email'],
        password_hash($data['password'], PASSWORD_DEFAULT),
        $data['role'],
      ]);

      if ($data['role'] === 'guru') {
        $guruId = $this->generateId('guru', 'GRU');
        $nip    = $data['nip'] !== '' ? $data['nip'] : $this->generateGuruNip($userId);
        $mapel  = $data['mapel'] !== '' ? $data['mapel'] : 'Privat';
        $stmt = $this->db->prepare("
          INSERT INTO guru (id, user_id, nip, nama, mapel)
          VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
          $guruId,
          $userId,
          $nip,
          $data['nama'],
          $mapel,
        ]);
      } elseif ($data['role'] === 'siswa') {
        $siswaId = $this->generateId('siswa', 'SSW');
        $nis     = $data['nis'] !== '' ? $data['nis'] : $this->generateSiswaNis($userId);
        $kelas   = $data['kelas'] !== '' ? $data['kelas'] : 'Privat';
        $stmt = $this->db->prepare("
          INSERT INTO siswa (id, user_id, nis, nama, kelas)
          VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
          $siswaId,
          $userId,
          $nis,
          $data['nama'],
          $kelas,
        ]);
      }

      $this->db->commit();
      return ['status' => 'success', 'id' => $userId];
    } catch (PDOException $e) {
      $this->db->rollBack();
      return ['status' => 'error', 'message' => $this->friendlyDuplicateMessage($e)];
    } catch (Throwable $e) {
      $this->db->rollBack();
      return ['status' => 'error', 'message' => $e->getMessage()];
    }
  }

  public function updateUser(string $userId, array $data): array {
    $current = $this->getUserWithDetail($userId);
    if (!$current) {
      return ['status' => 'error', 'message' => 'User tidak ditemukan'];
    }

    if ($current['role'] !== $data['role']) {
      return ['status' => 'error', 'message' => 'Role tidak boleh diubah. Hapus dan buat ulang bila ingin pindah role.'];
    }

    $this->db->beginTransaction();
    try {
      $params = [$data['email']];
      $sql    = "UPDATE users SET email = ?";

      if (!empty($data['password'])) {
        $sql    .= ", password = ?";
        $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
      }

      $sql    .= " WHERE id = ?";
      $params[] = $userId;

      $stmt = $this->db->prepare($sql);
      $stmt->execute($params);

      if ($data['role'] === 'guru') {
        if ($current['guru_id']) {
          $nip   = $data['nip'] !== '' ? $data['nip'] : ($current['nip'] ?? $this->generateGuruNip($userId));
          $mapel = $data['mapel'] !== '' ? $data['mapel'] : ($current['mapel'] ?? 'Privat');
          $stmt = $this->db->prepare("
            UPDATE guru SET nip = ?, nama = ?, mapel = ?
            WHERE id = ?
          ");
          $stmt->execute([
            $nip,
            $data['nama'] ?: ($current['guru_nama'] ?? ''),
            $mapel,
            $current['guru_id'],
          ]);
        } else {
          $guruId = $this->generateId('guru', 'GRU');
          $nip    = $data['nip'] !== '' ? $data['nip'] : $this->generateGuruNip($userId);
          $mapel  = $data['mapel'] !== '' ? $data['mapel'] : 'Privat';
          $stmt = $this->db->prepare("
            INSERT INTO guru (id, user_id, nip, nama, mapel)
            VALUES (?, ?, ?, ?, ?)
          ");
          $stmt->execute([
            $guruId,
            $userId,
            $nip,
            $data['nama'],
            $mapel,
          ]);
        }
      } elseif ($data['role'] === 'siswa') {
        if ($current['siswa_id']) {
          $nis   = $data['nis'] !== '' ? $data['nis'] : ($current['nis'] ?? $this->generateSiswaNis($userId));
          $kelas = $data['kelas'] !== '' ? $data['kelas'] : ($current['kelas'] ?? 'Privat');
          $stmt = $this->db->prepare("
            UPDATE siswa SET nis = ?, nama = ?, kelas = ?
            WHERE id = ?
          ");
          $stmt->execute([
            $nis,
            $data['nama'] ?: ($current['siswa_nama'] ?? ''),
            $kelas,
            $current['siswa_id'],
          ]);
        } else {
          $siswaId = $this->generateId('siswa', 'SSW');
          $nis     = $data['nis'] !== '' ? $data['nis'] : $this->generateSiswaNis($userId);
          $kelas   = $data['kelas'] !== '' ? $data['kelas'] : 'Privat';
          $stmt = $this->db->prepare("
            INSERT INTO siswa (id, user_id, nis, nama, kelas)
            VALUES (?, ?, ?, ?, ?)
          ");
          $stmt->execute([
            $siswaId,
            $userId,
            $nis,
            $data['nama'],
            $kelas,
          ]);
        }
      }

      $this->db->commit();
      return ['status' => 'success'];
    } catch (PDOException $e) {
      $this->db->rollBack();
      return ['status' => 'error', 'message' => $this->friendlyDuplicateMessage($e)];
    } catch (Throwable $e) {
      $this->db->rollBack();
      return ['status' => 'error', 'message' => $e->getMessage()];
    }
  }

  public function deleteUser(string $userId): array {
    $current = $this->getUserWithDetail($userId);
    if (!$current) {
      return ['status' => 'error', 'message' => 'User tidak ditemukan'];
    }

    if ($current['role'] === 'guru' && $this->hasRelasiGuru($current['guru_id'])) {
      return ['status' => 'error', 'message' => 'Guru masih terhubung dengan jadwal/mapel. Putuskan relasi terlebih dahulu.'];
    }

    if ($current['role'] === 'siswa' && $this->hasRelasiSiswa($current['siswa_id'] ?? '', $current['nis'] ?? '')) {
      return ['status' => 'error', 'message' => 'Siswa masih terhubung dengan jadwal/absensi. Putuskan relasi terlebih dahulu.'];
    }

    $this->db->beginTransaction();
    try {
      $this->db->prepare("DELETE FROM profil WHERE user_id = ?")->execute([$userId]);
      $this->db->prepare("DELETE FROM guru   WHERE user_id = ?")->execute([$userId]);
      $this->db->prepare("DELETE FROM siswa  WHERE user_id = ?")->execute([$userId]);
      $this->db->prepare("DELETE FROM users  WHERE id = ?")->execute([$userId]);
      $this->db->commit();
      return ['status' => 'success'];
    } catch (Throwable $e) {
      $this->db->rollBack();
      return ['status' => 'error', 'message' => $e->getMessage()];
    }
  }

  private function hasRelasiGuru(?string $guruId): bool {
    if (empty($guruId)) return false;
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM siswa_mapel WHERE guru_id = ?");
    $stmt->execute([$guruId]);
    return (int)$stmt->fetchColumn() > 0;
  }

  private function hasRelasiSiswa(?string $siswaId, ?string $nis): bool {
    if (empty($siswaId) && empty($nis)) return false;

    if (!empty($siswaId)) {
      $stmt = $this->db->prepare("SELECT COUNT(*) FROM siswa_mapel WHERE siswa_id = ?");
      $stmt->execute([$siswaId]);
      if ((int)$stmt->fetchColumn() > 0) return true;
    }

    if (!empty($nis)) {
      $stmt = $this->db->prepare("SELECT COUNT(*) FROM absensi WHERE nis_siswa = ?");
      $stmt->execute([$nis]);
      if ((int)$stmt->fetchColumn() > 0) return true;
    }

    return false;
  }

  private function friendlyDuplicateMessage(PDOException $e): string {
    $code = $e->errorInfo[1] ?? null;
    if ($code === 1062) {
      return 'Email/NIP/NIS sudah digunakan.';
    }
    return $e->getMessage();
  }

  private function generateGuruNip(string $userId): string {
    return 'NIP-' . $userId;
  }

  private function generateSiswaNis(string $userId): string {
    return 'NIS-' . $userId;
  }
}
