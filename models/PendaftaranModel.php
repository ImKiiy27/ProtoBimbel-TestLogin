<?php
// ============================================================
// models/PendaftaranModel.php
// Tugasnya: query yang berkaitan dengan pendaftaran calon siswa
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/IdCounterModel.php';

class PendaftaranModel {

  private PDO $db;
  private IdCounterModel $idCounterModel;

  public function __construct(?PDO $db = null, ?IdCounterModel $idCounterModel = null) {
    $this->db = $db ?? getDB();
    $this->idCounterModel = $idCounterModel ?? new IdCounterModel($this->db);
  }

  // Cek apakah email sudah terdaftar sebagai user aktif
  public function emailExistsInUsers(string $email): bool {
    $stmt = $this->db->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    return (bool)$stmt->fetchColumn();
  }

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

    if (!$row) {
      return 0;
    }

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

    $id = $this->idCounterModel->generateId('pendaftaran', 'PND');

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
}
