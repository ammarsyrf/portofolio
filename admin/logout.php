<?php
/**
 * Logout Administrator
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';

// Hapus sesi
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
session_destroy();

// Start session baru untuk flash message
session_start();
set_flash('success', 'Anda telah berhasil keluar dari sesi administrator.');
redirect(BASE_URL . '/admin/login.php');
