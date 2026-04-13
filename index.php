<?php
// ============================================================
// index.php — Entry point utama BimbelKu
// Semua request masuk lewat sini
// ============================================================

// Load session config dulu sebelum apapun
require_once __DIR__ . '/config/session.php';
initSession();

// Load config & core
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Router.php';

// Ambil halaman dari query string: ?page=login
// Default: index (landing page)
$page = $_GET['page'] ?? 'index';

// Sanitasi input
$page = preg_replace('/[^a-zA-Z0-9_\-]/', '', $page);

// Routing
$router = new Router();
$router->dispatch($page);
