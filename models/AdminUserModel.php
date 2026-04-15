<?php
// ============================================================
// models/AdminUserModel.php
// Tugasnya: query CRUD user untuk area admin
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/IdCounterModel.php';

class AdminUserModel {

  private PDO $db;
  private IdCounterModel $idCounterModel;

  public function __construct(?PDO $db = null, ?IdCounterModel $idCounterModel = null) {
    $this->db = $db ?? getDB();
    $this->idCounterModel = $idCounterModel ?? new IdCounterModel($this->db);
  }

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
        g.user_id AS guru_id,
        g.nama    AS guru_nama,
        g.mapel,
        s.user_id AS siswa_id,
        s.nama    AS siswa_nama,
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
        g.user_id AS guru_id,
        g.nama    AS guru_nama,
        g.mapel,
        s.user_id AS siswa_id,
        s.nama    AS siswa_nama,
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
      // Generate user ID sesuai role
      [$tabel, $prefix] = match($data['role']) {
        'guru'  => ['guru',  'GRU'],
        'siswa' => ['siswa', 'SSW'],
        'admin' => ['users', 'ADM'],
        default => ['users', 'USR'],
      };

      $userId = $this->idCounterModel->generateId($tabel, $prefix);

      $stmt = $this->db->prepare("
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
        $mapel = ($data['mapel'] ?? '') !== '' ? $data['mapel'] : 'Privat';
        $stmt  = $this->db->prepare("
          INSERT INTO guru (user_id, nama, mapel)
          VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $data['nama'], $mapel]);

      } elseif ($data['role'] === 'siswa') {
        $kelas = ($data['kelas'] ?? '') !== '' ? $data['kelas'] : 'Privat';
        $stmt  = $this->db->prepare("
          INSERT INTO siswa (user_id, nama, kelas)
          VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $data['nama'], $kelas]);
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
        $sql      .= ", password = ?";
        $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
      }

      $sql      .= " WHERE id = ?";
      $params[] = $userId;

      $stmt = $this->db->prepare($sql);
      $stmt->execute($params);

      if ($data['role'] === 'guru') {
        if ($current['guru_id']) {
          $mapel = ($data['mapel'] ?? '') !== '' ? $data['mapel'] : ($current['mapel'] ?? 'Privat');
          $stmt  = $this->db->prepare("
            UPDATE guru SET nama = ?, mapel = ?
            WHERE user_id = ?
          ");
          $stmt->execute([$data['nama'] ?: ($current['guru_nama'] ?? ''), $mapel, $current['guru_id']]);
        } else {
          $mapel = ($data['mapel'] ?? '') !== '' ? $data['mapel'] : 'Privat';
          $stmt  = $this->db->prepare("
            INSERT INTO guru (user_id, nama, mapel)
            VALUES (?, ?, ?)
          ");
          $stmt->execute([$userId, $data['nama'], $mapel]);
        }
      } elseif ($data['role'] === 'siswa') {
        if ($current['siswa_id']) {
          $kelas = ($data['kelas'] ?? '') !== '' ? $data['kelas'] : ($current['kelas'] ?? 'Privat');
          $stmt  = $this->db->prepare("
            UPDATE siswa SET nama = ?, kelas = ?
            WHERE user_id = ?
          ");
          $stmt->execute([$data['nama'] ?: ($current['siswa_nama'] ?? ''), $kelas, $current['siswa_id']]);
        } else {
          $kelas = ($data['kelas'] ?? '') !== '' ? $data['kelas'] : 'Privat';
          $stmt  = $this->db->prepare("
            INSERT INTO siswa (user_id, nama, kelas)
            VALUES (?, ?, ?)
          ");
          $stmt->execute([$userId, $data['nama'], $kelas]);
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

    if ($current['role'] === 'siswa' && $this->hasRelasiSiswa($current['siswa_id'] ?? '')) {
      return ['status' => 'error', 'message' => 'Siswa masih terhubung dengan jadwal/mapel. Putuskan relasi terlebih dahulu.'];
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

  private function hasRelasiSiswa(?string $siswaId): bool {
    if (empty($siswaId)) return false;
    if (!empty($siswaId)) {
      $stmt = $this->db->prepare("SELECT COUNT(*) FROM siswa_mapel WHERE siswa_id = ?");
      $stmt->execute([$siswaId]);
      if ((int)$stmt->fetchColumn() > 0) return true;
    }
    return false;
  }

  private function friendlyDuplicateMessage(PDOException $e): string {
    $code = $e->errorInfo[1] ?? null;
    if ($code === 1062) return 'Email sudah digunakan.';
    return $e->getMessage();
  }
}
