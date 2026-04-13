# BimbelKu — Struktur Proyek MVC

## Cara Install di XAMPP

1. Copy folder `bimbelku/` ke `C:/xampp/htdocs/`
2. Import `bimbelku.sql` ke phpMyAdmin
3. Buka browser: `http://localhost/bimbelku/`

---

## Struktur Folder

```
bimbelku/
├── index.php                  ← Entry point utama (semua request lewat sini)
├── config/
│   └── database.php           ← Koneksi PDO ke MySQL
├── core/
│   └── Router.php             ← Routing ?page=xxx ke controller
├── controllers/
│   ├── AuthController.php     ← Login, logout, pendaftaran
│   ├── AdminController.php    ← Semua fitur admin
│   ├── GuruController.php     ← Semua fitur guru
│   └── SiswaController.php    ← Semua fitur siswa
├── models/
│   └── UserModel.php          ← Query users, login, pendaftaran
├── views/
│   ├── layouts/
│   │   ├── header.php         ← HTML head + CSS + font
│   │   ├── footer.php         ← JS scripts + closing tags
│   │   └── sidebar.php        ← Sidebar dinamis per role
│   ├── auth/
│   │   ├── index.php          ← Landing page
│   │   ├── login.php          ← Halaman login
│   │   └── pendaftaran.php    ← Halaman pendaftaran
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── siswa.php
│   │   ├── guru.php
│   │   ├── jadwal.php
│   │   ├── absensi.php
│   │   ├── nilai.php
│   │   └── user.php
│   ├── guru/
│   │   ├── dashboard.php
│   │   ├── jadwal.php
│   │   ├── absensi.php
│   │   ├── nilai.php
│   │   └── profil.php
│   └── siswa/
│       ├── dashboard.php
│       ├── jadwal.php
│       ├── absensi.php
│       ├── nilai.php
│       └── profil.php
└── public/
    ├── css/
    │   └── main.css           ← Semua CSS digabung (tidak ada inline style)
    └── js/
        └── main.js            ← Semua JS digabung

```

---

## Cara Kerja Routing

Semua URL berbentuk `index.php?page=nama-halaman`

| URL | Halaman |
|-----|---------|
| `index.php` | Landing page |
| `index.php?page=login` | Login |
| `index.php?page=pendaftaran` | Pendaftaran |
| `index.php?page=admin-dashboard` | Dashboard Admin |
| `index.php?page=admin-siswa` | Kelola Siswa |
| `index.php?page=guru-dashboard` | Dashboard Guru |
| `index.php?page=siswa-dashboard` | Dashboard Siswa |
| `index.php?page=logout` | Logout |

---

## Akun Default

| Role  | Email                  | Password |
|-------|------------------------|----------|
| Admin | admin@bimbelku.com     | password |
| Guru  | budi@bimbelku.com      | password |
| Guru  | sari@bimbelku.com      | password |
| Siswa | andi@gmail.com         | password |
| Siswa | rina@gmail.com         | password |

> **Penting:** Ganti semua password setelah pertama kali login!

---

## Keamanan yang Sudah Diterapkan

- PDO prepared statement (anti SQL Injection)
- `password_hash()` & `password_verify()` (anti brute force)
- Login attempt limiter (kunci akun setelah 5x salah)
- Session-based authentication
- Role-based access control (admin/guru/siswa tidak bisa akses halaman role lain)
- `htmlspecialchars()` di semua output (anti XSS)
