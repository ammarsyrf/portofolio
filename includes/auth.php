<?php
/**
 * Authentication Guard & Helper
 * Wajib di-require pada baris paling atas setiap file di dalam /admin/ (kecuali login.php dan setup.php)
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/functions.php';

if (!defined('ADMIN_SESSION_LIFETIME')) {
    define('ADMIN_SESSION_LIFETIME', 7200); // 2 jam batas inaktivitas (dalam detik)
}

/**
 * Memeriksa apakah admin sedang login, valid, dan belum kedaluwarsa
 */
function is_admin_logged_in(): bool {
    if (empty($_SESSION['admin_logged_in']) || empty($_SESSION['admin_user_id'])) {
        return false;
    }

    // 1. Pemeriksaan Inactivity Timeout (2 jam tanpa interaksi)
    $lastActivity = (int)($_SESSION['admin_last_activity'] ?? 0);
    if ($lastActivity > 0 && (time() - $lastActivity) > ADMIN_SESSION_LIFETIME) {
        logout_admin();
        return false;
    }

    // 2. Proteksi Session Hijacking (Validasi User-Agent)
    $currentUa = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $storedUa = $_SESSION['admin_user_agent'] ?? '';
    if (!empty($storedUa) && !hash_equals($storedUa, $currentUa)) {
        logout_admin();
        return false;
    }

    // Perbarui waktu aktivitas terakhir
    $_SESSION['admin_last_activity'] = time();

    return true;
}

/**
 * Mendaftarkan sesi login admin dengan regenerasi ID sesi dan fingerprint perangkat
 */
function login_admin(array $admin): void {
    session_regenerate_id(true);
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_user_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_login_time'] = time();
    $_SESSION['admin_last_activity'] = time();
    $_SESSION['admin_user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
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
    redirect(BASE_URL . '/admin/login');
}
