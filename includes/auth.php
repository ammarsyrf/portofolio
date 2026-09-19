<?php
/**
 * Authentication Guard & Helper
 * Wajib di-require pada baris paling atas setiap file di dalam /admin/ (kecuali login.php dan setup.php)
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/functions.php';

/**
 * Memeriksa apakah admin sedang login
 */
function is_admin_logged_in(): bool {
    return !empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_user_id']);
}

/**
 * Mendaftarkan sesi login admin dengan regenerasi ID sesi untuk mencegah session fixation
 */
function login_admin(array $admin): void {
    session_regenerate_id(true);
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_user_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_login_time'] = time();
}

/**
 * Menghapus sesi admin dan logout
 */
function logout_admin(): void {
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
}

// Langsung lakukan pencegatan jika auth.php di-require di file admin
if (!is_admin_logged_in()) {
    // Simpan pesan flash
    set_flash('danger', 'Sesi Anda telah berakhir atau Anda belum login.');
    redirect(BASE_URL . '/admin/login.php');
}
