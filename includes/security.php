<?php
/**
 * Security Hardening Helper
 * Menyediakan proteksi Brute Force, Rate Limiting, Pengambilan IP Aman, dan Sanitasi URL
 */

// Konfigurasi Rate Limiting Login
if (!defined('MAX_LOGIN_ATTEMPTS')) {
    define('MAX_LOGIN_ATTEMPTS', 5);
}
if (!defined('LOGIN_LOCKOUT_TIME')) {
    define('LOGIN_LOCKOUT_TIME', 900); // 15 menit dalam detik
}

/**
 * Mendapatkan IP klien pengunjung secara aman
 */
function get_client_ip(): string {
    $ipKeys = [
        'HTTP_CF_CONNECTING_IP', // Cloudflare
        'HTTP_X_FORWARDED_FOR',  // Proxy / Load Balancer
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR'
    ];

    foreach ($ipKeys as $key) {
        if (!empty($_SERVER[$key])) {
            $ipList = explode(',', $_SERVER[$key]);
            foreach ($ipList as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
                // Fallback jika valid IP (termasuk private IP saat dev lokal)
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }

    return '127.0.0.1';
}

/**
 * Mendapatkan path berkas cache throttle login untuk IP tertentu
 */
function get_throttle_filepath(string $ip): string {
    $safeHash = hash('sha256', 'porto_auth_salt_' . $ip);
    return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'porto_auth_throttle_' . $safeHash . '.json';
}

/**
 * Memeriksa apakah IP sedang terkunci akibat terlalu banyak percobaan login gagal
 * 
 * @return array ['locked' => bool, 'remaining_seconds' => int, 'attempts' => int]
 */
function is_login_locked(string $ip): array {
    $file = get_throttle_filepath($ip);
    if (!file_exists($file)) {
        return ['locked' => false, 'remaining_seconds' => 0, 'attempts' => 0];
    }

    $raw = @file_get_contents($file);
    if (!$raw) {
        return ['locked' => false, 'remaining_seconds' => 0, 'attempts' => 0];
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return ['locked' => false, 'remaining_seconds' => 0, 'attempts' => 0];
    }

    $attempts = (int)($data['attempts'] ?? 0);
    $lockedUntil = (int)($data['locked_until'] ?? 0);
    $lastAttempt = (int)($data['last_attempt'] ?? 0);
    $now = time();

    // Jika sedang terkunci
    if ($lockedUntil > $now) {
        return [
            'locked' => true,
            'remaining_seconds' => $lockedUntil - $now,
            'attempts' => $attempts
        ];
    }

    // Jika window waktu lockout telah terlewati, reset otomatis
    if (($now - $lastAttempt) > LOGIN_LOCKOUT_TIME) {
        @unlink($file);
        return ['locked' => false, 'remaining_seconds' => 0, 'attempts' => 0];
    }

    return [
        'locked' => false,
        'remaining_seconds' => 0,
        'attempts' => $attempts
    ];
}

/**
 * Mencatat percobaan login gagal dan mengunci jika mencapai batas maksimal
 * 
 * @return array ['attempts' => int, 'locked' => bool, 'remaining_seconds' => int]
 */
function record_failed_login(string $ip): array {
    $file = get_throttle_filepath($ip);
    $now = time();
    $attempts = 1;
    $lockedUntil = 0;

    if (file_exists($file)) {
        $raw = @file_get_contents($file);
        $data = json_decode((string)$raw, true);
        if (is_array($data)) {
            $lastAttempt = (int)($data['last_attempt'] ?? 0);
            if (($now - $lastAttempt) <= LOGIN_LOCKOUT_TIME) {
                $attempts = ((int)($data['attempts'] ?? 0)) + 1;
            }
        }
    }

    $isLocked = false;
    $remainingSeconds = 0;

    if ($attempts >= MAX_LOGIN_ATTEMPTS) {
        $isLocked = true;
        $lockedUntil = $now + LOGIN_LOCKOUT_TIME;
        $remainingSeconds = LOGIN_LOCKOUT_TIME;
    }

    $payload = [
        'ip' => $ip,
        'attempts' => $attempts,
        'last_attempt' => $now,
        'locked_until' => $lockedUntil
    ];

    @file_put_contents($file, json_encode($payload), LOCK_EX);

    return [
        'attempts' => $attempts,
        'locked' => $isLocked,
        'remaining_seconds' => $remainingSeconds
    ];
}

/**
 * Menghapus catatan kegagalan login saat admin berhasil masuk
 */
function reset_login_attempts(string $ip): void {
    $file = get_throttle_filepath($ip);
    if (file_exists($file)) {
        @unlink($file);
    }
}

/**
 * Sanitasi URL eksternal (misal avatar atau tautan) untuk mencegah eksekusi javascript:
 */
function sanitize_safe_url(?string $url): string {
    if (empty($url)) {
        return '';
    }
    $url = trim($url);
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return '';
    }
    $scheme = parse_url($url, PHP_URL_SCHEME);
    if (!in_array(strtolower((string)$scheme), ['http', 'https'], true)) {
        return '';
    }
    return $url;
}
