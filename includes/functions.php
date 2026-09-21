<?php
/**
 * Helper Functions (Sanitasi, CSRF, File Upload, Database Queries, Visitor Auth)
 */

require_once __DIR__ . '/../config.php';

/**
 * Escape HTML output untuk mencegah XSS
 */
function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Mendapatkan token CSRF saat ini atau membuat baru jika belum ada
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Menghasilkan input hidden untuk CSRF token di form
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Memvalidasi token CSRF dari request POST
 */
function verify_csrf_token(?string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Helper redirect aman
 */
function redirect(string $url): void {
    header("Location: " . $url);
    exit;
}

/**
 * Menyimpan flash message ke session
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

/**
 * Mengambil dan menghapus flash message dari session
 */
function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Menghasilkan URL asset statis dengan cache-buster otomatis
 */
function asset(string $path): string {
    $cleanPath = ltrim($path, '/');
    $filePath = ROOT_PATH . '/assets/' . $cleanPath;
    $v = file_exists($filePath) ? filemtime($filePath) : time();
    return BASE_URL . '/assets/' . $cleanPath . '?v=' . $v;
}

/**
 * Menghasilkan URL file upload
 */
function upload_url(string $folder, ?string $filename): string {
    if (empty($filename)) {
        return '';
    }
    return BASE_URL . '/assets/uploads/' . trim($folder, '/') . '/' . rawurlencode($filename);
}

/**
 * Validasi dan upload file secara aman
 * 
 * @param array $file $_FILES['input_name']
 * @param string $type 'photo' | 'cv' | 'project' | 'achievement' | 'creation'
 * @param string $targetDir Path direktori target di server
 * @return array ['success' => bool, 'filename' => string, 'error' => string]
 */
function upload_file(array $file, string $type, string $targetDir): array {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Parameter upload file tidak valid.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE   => 'Ukuran file melebihi batas upload server (upload_max_filesize).',
            UPLOAD_ERR_FORM_SIZE  => 'Ukuran file melebihi batas form.',
            UPLOAD_ERR_PARTIAL    => 'File hanya terunggah sebagian.',
            UPLOAD_ERR_NO_FILE    => 'Tidak ada file yang diunggah.',
            UPLOAD_ERR_NO_TMP_DIR => 'Direktori sementara tidak ditemukan di server.',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk server.',
            UPLOAD_ERR_EXTENSION  => 'Ekstensi file dihentikan oleh PHP.'
        ];
        return ['success' => false, 'error' => $errors[$file['error']] ?? 'Terjadi kesalahan saat upload file.'];
    }

    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0755, true)) {
            return ['success' => false, 'error' => 'Direktori tujuan upload tidak dapat dibuat.'];
        }
    }

    $originalName = $file['name'];
    $fileSize = $file['size'];
    $tmpPath = $file['tmp_name'];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Gunakan finfo untuk deteksi MIME type sebenarnya
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $tmpPath);
    finfo_close($finfo);

    if ($type === 'cv') {
        // Validasi CV: Khusus PDF, max MAX_CV_SIZE (5MB)
        $allowedMimes = ['application/pdf'];
        $allowedExts = ['pdf'];

        if ($fileSize > MAX_CV_SIZE) {
            return ['success' => false, 'error' => 'Ukuran file CV maksimal 5 MB.'];
        }

        if (!in_array($mimeType, $allowedMimes, true) || !in_array($extension, $allowedExts, true)) {
            return ['success' => false, 'error' => 'File CV harus berformat PDF (.pdf).'];
        }

        $prefix = 'cv_ammar_';
    } else {
        // Validasi Gambar: JPG, PNG, WEBP, max MAX_IMAGE_SIZE (2MB)
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];

        if ($fileSize > MAX_IMAGE_SIZE) {
            return ['success' => false, 'error' => 'Ukuran gambar maksimal 2 MB.'];
        }

        if (!in_array($mimeType, $allowedMimes, true) || !in_array($extension, $allowedExts, true)) {
            return ['success' => false, 'error' => 'Format gambar harus JPG, PNG, atau WebP.'];
        }

        if (@getimagesize($tmpPath) === false) {
            return ['success' => false, 'error' => 'File bukan gambar yang valid.'];
        }

        $prefixMap = [
            'photo'       => 'photo_',
            'project'     => 'proj_',
            'achievement' => 'achieve_',
            'creation'    => 'create_'
        ];
        $prefix = $prefixMap[$type] ?? 'img_';
    }

    // Nama file acak unik untuk mencegah tabrakan dan path traversal
    $newFileName = $prefix . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    $destination = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $newFileName;

    if (!move_uploaded_file($tmpPath, $destination)) {
        return ['success' => false, 'error' => 'Gagal memindahkan file yang diunggah ke folder penyimpanan.'];
    }

    return [
        'success' => true,
        'filename' => $newFileName,
        'error' => null
    ];
}

/**
 * Hapus file upload lama secara aman
 */
function delete_uploaded_file(?string $filename, string $targetDir): bool {
    if (empty($filename)) {
        return false;
    }
    $cleanName = basename($filename);
    $filePath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $cleanName;
    if (file_exists($filePath) && is_file($filePath)) {
        return @unlink($filePath);
    }
    return false;
}

// =========================================================================
// DATABASE QUERY HELPERS
// =========================================================================

/**
 * Ambil data profil tunggal (id = 1)
 */
function get_profile(PDO $pdo): ?array {
    $stmt = $pdo->prepare("SELECT * FROM profile WHERE id = 1 LIMIT 1");
    $stmt->execute();
    $profile = $stmt->fetch();
    return $profile ?: null;
}

/**
 * Ambil proyek yang dipublikasikan untuk halaman depan
 */
function get_published_projects(PDO $pdo): array {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE is_published = 1 ORDER BY sort_order ASC, id DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_all_projects(PDO $pdo): array {
    $stmt = $pdo->prepare("SELECT * FROM projects ORDER BY sort_order ASC, id DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_project_by_id(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $project = $stmt->fetch();
    return $project ?: null;
}

/**
 * Achievements Queries
 */
function get_published_achievements(PDO $pdo): array {
    $stmt = $pdo->prepare("SELECT * FROM achievements WHERE is_published = 1 ORDER BY sort_order ASC, id DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_all_achievements(PDO $pdo): array {
    $stmt = $pdo->prepare("SELECT * FROM achievements ORDER BY sort_order ASC, id DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_achievement_by_id(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM achievements WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $res = $stmt->fetch();
    return $res ?: null;
}

/**
 * Creations Queries
 */
function get_published_creations(PDO $pdo): array {
    $stmt = $pdo->prepare("SELECT * FROM creations WHERE is_published = 1 ORDER BY sort_order ASC, id DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_all_creations(PDO $pdo): array {
    $stmt = $pdo->prepare("SELECT * FROM creations ORDER BY sort_order ASC, id DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_creation_by_id(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM creations WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $res = $stmt->fetch();
    return $res ?: null;
}

/**
 * Links Queries
 */
function get_active_links(PDO $pdo): array {
    $stmt = $pdo->prepare("SELECT * FROM links WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_all_links(PDO $pdo): array {
    $stmt = $pdo->prepare("SELECT * FROM links ORDER BY sort_order ASC, id ASC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_link_by_id(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM links WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $res = $stmt->fetch();
    return $res ?: null;
}

/**
 * Guestbook Queries
 */
function get_guestbook_messages(PDO $pdo, int $limit = 100): array {
    $stmt = $pdo->prepare("SELECT * FROM guestbook ORDER BY id DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Site Settings Queries
 */
function get_setting(PDO $pdo, string $key, string $default = ''): string {
    $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = :k LIMIT 1");
    $stmt->execute(['k' => $key]);
    $val = $stmt->fetchColumn();
    return ($val !== false) ? (string)$val : $default;
}

function set_setting(PDO $pdo, string $key, string $value): void {
    $stmt = $pdo->prepare("
        INSERT INTO site_settings (setting_key, setting_value) 
        VALUES (:k, :v) 
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
    ");
    $stmt->execute(['k' => $key, 'v' => $value]);
}

/**
 * Hire / Work Availability Status Helpers
 */
function get_hire_status(PDO $pdo): array {
    $status = get_setting($pdo, 'hire_status', 'available');
    if ($status === 'busy') {
        return [
            'status' => 'busy',
            'label'  => '● Sedang Sibuk / Proyek Penuh',
            'short'  => 'Busy / Project Focus',
            'color'  => '#F87171',
            'badge'  => '🔴 Busy'
        ];
    }
    return [
        'status' => 'available',
        'label'  => '● Siap Rekrutmen & Freelance',
        'short'  => 'Available for Hire',
        'color'  => '#34D399',
        'badge'  => '🟢 Open to Work'
    ];
}

function set_hire_status(PDO $pdo, string $status): void {
    $val = ($status === 'busy') ? 'busy' : 'available';
    set_setting($pdo, 'hire_status', $val);
}


// =========================================================================
// GUEST VISITOR (GOOGLE OAUTH) SESSION HELPERS
// Terpisah secara ketat dari session admin
// =========================================================================

function is_guest_logged_in(): bool {
    return !empty($_SESSION['guest_user']) && !empty($_SESSION['guest_user']['google_id']);
}

function get_guest_user(): ?array {
    return $_SESSION['guest_user'] ?? null;
}

function set_guest_user(array $user): void {
    $_SESSION['guest_user'] = [
        'google_id' => $user['google_id'] ?? '',
        'name'      => $user['name'] ?? 'Pengunjung',
        'email'     => $user['email'] ?? '',
        'avatar'    => $user['avatar'] ?? ''
    ];
}

function logout_guest(): void {
    unset($_SESSION['guest_user']);
}

// =========================================================================
// VISITOR / PAGE-VIEW TELEMETRY & ANALYTICS
// (IP Unik, Negara, Kota, Device, Browser, OS, Referrer)
// =========================================================================

/**
 * Pastikan tabel page_views memiliki kolom lengkap (Self-healing schema)
 */
function ensure_page_views_schema(PDO $pdo): void {
    static $checked = false;
    if ($checked) return;

    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `page_views` (
            `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `session_id` VARCHAR(128)    NOT NULL,
            `ip_address` VARCHAR(45)     NOT NULL DEFAULT '127.0.0.1',
            `ip_hash`    VARCHAR(64)     NOT NULL,
            `country`    VARCHAR(100)    DEFAULT 'Unknown',
            `city`       VARCHAR(100)    DEFAULT 'Unknown',
            `device`     VARCHAR(50)     DEFAULT 'Desktop',
            `browser`    VARCHAR(50)     DEFAULT 'Unknown',
            `os`         VARCHAR(50)     DEFAULT 'Unknown',
            `page`       VARCHAR(512)    NOT NULL DEFAULT '/',
            `referer`    VARCHAR(512)    DEFAULT NULL,
            `user_agent` TEXT            DEFAULT NULL,
            `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_created_at` (`created_at`),
            INDEX `idx_session`    (`session_id`),
            INDEX `idx_ip`         (`ip_address`),
            INDEX `idx_country`    (`country`),
            INDEX `idx_page`       (`page`(191))
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $cols = $pdo->query("SHOW COLUMNS FROM `page_views`")->fetchAll(PDO::FETCH_COLUMN);
        $expected = [
            'ip_address' => "VARCHAR(45) NOT NULL DEFAULT '127.0.0.1' AFTER `session_id`",
            'country'    => "VARCHAR(100) DEFAULT 'Unknown' AFTER `ip_hash`",
            'city'       => "VARCHAR(100) DEFAULT 'Unknown' AFTER `country`",
            'device'     => "VARCHAR(50) DEFAULT 'Desktop' AFTER `city`",
            'browser'    => "VARCHAR(50) DEFAULT 'Unknown' AFTER `device`",
            'os'         => "VARCHAR(50) DEFAULT 'Unknown' AFTER `browser`",
        ];

        foreach ($expected as $col => $def) {
            if (!in_array($col, $cols, true)) {
                $pdo->exec("ALTER TABLE `page_views` ADD COLUMN `$col` $def");
            }
        }
        $checked = true;
    } catch (\Throwable $e) {
        // Silently ignore
    }
}

/**
 * Dapatkan IP asli pengunjung (support Cloudflare / Reverse Proxy)
 */
if (!function_exists('get_client_ip')) {
    function get_client_ip(): string {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ipList = explode(',', (string)$_SERVER[$header]);
                foreach ($ipList as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        return $ip;
                    }
                }
            }
        }
        return '127.0.0.1';
    }
}

/**
 * Deteksi Device Type (Desktop, Mobile, Tablet)
 */
function detect_device(string $ua): string {
    if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $ua)) {
        return 'Tablet';
    }
    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipod|android|iemobile|mobile)/i', $ua)) {
        return 'Mobile';
    }
    return 'Desktop';
}

/**
 * Deteksi Browser
 */
function detect_browser(string $ua): string {
    if (stripos($ua, 'Edg') !== false) return 'Edge';
    if (stripos($ua, 'OPR') !== false || stripos($ua, 'Opera') !== false) return 'Opera';
    if (stripos($ua, 'Chrome') !== false && stripos($ua, 'Edg') === false) return 'Chrome';
    if (stripos($ua, 'Firefox') !== false) return 'Firefox';
    if (stripos($ua, 'Safari') !== false && stripos($ua, 'Chrome') === false) return 'Safari';
    if (stripos($ua, 'SamsungBrowser') !== false) return 'Samsung Internet';
    if (stripos($ua, 'UCBrowser') !== false) return 'UC Browser';
    return 'Other';
}

/**
 * Deteksi Sistem Operasi (OS)
 */
function detect_os(string $ua): string {
    if (stripos($ua, 'Windows NT 10.0') !== false) return 'Windows 10/11';
    if (stripos($ua, 'Windows') !== false) return 'Windows';
    if (stripos($ua, 'Android') !== false) return 'Android';
    if (stripos($ua, 'iPhone') !== false || stripos($ua, 'iPad') !== false || stripos($ua, 'iPod') !== false) return 'iOS';
    if (stripos($ua, 'Macintosh') !== false || stripos($ua, 'Mac OS X') !== false) return 'macOS';
    if (stripos($ua, 'CrOS') !== false) return 'ChromeOS';
    if (stripos($ua, 'Linux') !== false) return 'Linux';
    return 'Other';
}

/**
 * Deteksi Lokasi Geografis (Negara & Kota) via IP
 * Menggunakan session cache agar tidak overload API eksternal
 */
function detect_geo(string $ip): array {
    // Cek jika Localhost / IP Privat
    if (in_array($ip, ['127.0.0.1', '::1'], true) || 
        str_starts_with($ip, '192.168.') || 
        str_starts_with($ip, '10.') || 
        str_starts_with($ip, '172.16.') ||
        str_starts_with($ip, '172.31.')) {
        return ['country' => 'Indonesia (Local)', 'city' => 'Localhost'];
    }

    $cacheKey = 'geo_' . md5($ip);
    if (!empty($_SESSION[$cacheKey]) && is_array($_SESSION[$cacheKey])) {
        return $_SESSION[$cacheKey];
    }

    $country = 'Unknown';
    $city    = 'Unknown';

    try {
        if (function_exists('curl_init')) {
            $ch = curl_init("http://ip-api.com/json/{$ip}?fields=status,country,city");
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 1,
                CURLOPT_TIMEOUT        => 1,
                CURLOPT_USERAGENT      => 'Porto-Tracker/1.0',
            ]);
            $response = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code === 200 && $response) {
                $data = json_decode($response, true);
                if (is_array($data) && ($data['status'] ?? '') === 'success') {
                    $country = !empty($data['country']) ? (string)$data['country'] : 'Unknown';
                    $city    = !empty($data['city']) ? (string)$data['city'] : 'Unknown';
                }
            }
        }
    } catch (\Throwable $e) {
        // Fallback silently
    }

    $result = ['country' => $country, 'city' => $city];
    $_SESSION[$cacheKey] = $result;
    return $result;
}

/**
 * Samarkan IP untuk tampilan privasi
 */
function mask_ip(string $ip): string {
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $parts = explode('.', $ip);
        if (count($parts) === 4) {
            return $parts[0] . '.' . $parts[1] . '.xxx.' . $parts[3];
        }
    }
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        $parts = explode(':', $ip);
        return $parts[0] . ':' . ($parts[1] ?? '0') . ':xxxx::';
    }
    return $ip;
}

/**
 * Catat page view dengan IP unik, Geo, Device, Browser & OS
 */
function track_page_view(PDO $pdo): void {
    try {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $page = $_SERVER['REQUEST_URI'] ?? '/';

        // Filter bot/crawler agar statistik akurat
        $bots = ['googlebot', 'bingbot', 'slurp', 'duckduckgo', 'yandexbot', 'baidu', 'crawler', 'spider', 'robot', 'bot', 'curl', 'wget', 'ahrefs', 'semrush', 'python', 'php'];
        foreach ($bots as $bot) {
            if (stripos($ua, $bot) !== false) return;
        }

        // Jangan hitung admin dan api
        $path = parse_url($page, PHP_URL_PATH) ?: '';
        if (preg_match('#/(admin|api|auth)(/|$)#i', $path)) return;

        // Ensure schema exists
        ensure_page_views_schema($pdo);

        $sessionId = session_id() ?: bin2hex(random_bytes(8));
        $rawIp     = get_client_ip();
        $ipHash    = hash('sha256', $rawIp . 'salt_porto_2025');
        $geo       = detect_geo($rawIp);
        $device    = detect_device($ua);
        $browser   = detect_browser($ua);
        $os        = detect_os($ua);
        $referer   = $_SERVER['HTTP_REFERER'] ?? null;

        $stmt = $pdo->prepare("
            INSERT INTO page_views (session_id, ip_address, ip_hash, country, city, device, browser, os, page, referer, user_agent)
            VALUES (:sid, :ip_addr, :ip_hash, :country, :city, :device, :browser, :os, :page, :ref, :ua)
        ");
        $stmt->execute([
            'sid'      => substr($sessionId, 0, 128),
            'ip_addr'  => substr($rawIp, 0, 45),
            'ip_hash'  => $ipHash,
            'country'  => substr($geo['country'], 0, 100),
            'city'     => substr($geo['city'], 0, 100),
            'device'   => $device,
            'browser'  => $browser,
            'os'       => $os,
            'page'     => substr($page, 0, 512),
            'ref'      => $referer ? substr($referer, 0, 512) : null,
            'ua'       => $ua ? substr($ua, 0, 500) : null,
        ]);
    } catch (\Throwable $e) {
        // Silently fail agar halaman publik tidak pernah crash
    }
}

/**
 * Ambil ringkasan statistik komprehensif pengunjung untuk dashboard admin.
 */
function get_visitor_stats(PDO $pdo): array {
    $stats = [
        'today'             => 0,
        'yesterday'         => 0,
        'this_week'         => 0,
        'last_week'         => 0,
        'total'             => 0,
        'unique_today'      => 0,
        'unique_total'      => 0,
        'trend'             => [],
        'top_countries'     => [],
        'top_cities'        => [],
        'device_breakdown'  => [],
        'browser_breakdown' => [],
        'os_breakdown'      => [],
        'top_pages'         => [],
        'recent_visitors'   => [],
        'table_exists'      => false,
    ];

    try {
        ensure_page_views_schema($pdo);
        $check = $pdo->query("SHOW TABLES LIKE 'page_views'")->fetchColumn();
        if (!$check) return $stats;
        $stats['table_exists'] = true;

        // Total views & total unique IP
        $stats['total'] = (int)$pdo->query("SELECT COUNT(*) FROM page_views")->fetchColumn();
        $stats['unique_total'] = (int)$pdo->query("SELECT COUNT(DISTINCT ip_address) FROM page_views")->fetchColumn();

        // Hari ini (Views & Unique IP)
        $stats['today'] = (int)$pdo->query(
            "SELECT COUNT(*) FROM page_views WHERE DATE(created_at) = CURDATE()"
        )->fetchColumn();

        $stats['unique_today'] = (int)$pdo->query(
            "SELECT COUNT(DISTINCT ip_address) FROM page_views WHERE DATE(created_at) = CURDATE()"
        )->fetchColumn();

        // Kemarin
        $stats['yesterday'] = (int)$pdo->query(
            "SELECT COUNT(*) FROM page_views WHERE DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)"
        )->fetchColumn();

        // Minggu ini
        $stats['this_week'] = (int)$pdo->query(
            "SELECT COUNT(*) FROM page_views WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)"
        )->fetchColumn();

        // Trend 7 hari
        $trend = $pdo->query("
            SELECT
                DATE(created_at)                   AS day,
                COUNT(*)                           AS views,
                COUNT(DISTINCT ip_address)         AS unique_ips
            FROM page_views
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(created_at)
            ORDER BY day ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $dayMap = [];
        foreach ($trend as $row) $dayMap[$row['day']] = $row;

        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $d   = date('Y-m-d', strtotime("-{$i} days"));
            $lbl = date('D', strtotime($d));
            $days[] = [
                'label'   => $lbl,
                'views'   => (int)($dayMap[$d]['views'] ?? 0),
                'unique'  => (int)($dayMap[$d]['unique_ips'] ?? 0),
            ];
        }
        $stats['trend'] = $days;

        // Top Negara
        $stats['top_countries'] = $pdo->query("
            SELECT country, COUNT(*) as hits, COUNT(DISTINCT ip_address) as unique_ips
            FROM page_views
            WHERE country IS NOT NULL AND country != ''
            GROUP BY country
            ORDER BY hits DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Top Kota
        $stats['top_cities'] = $pdo->query("
            SELECT city, country, COUNT(*) as hits
            FROM page_views
            WHERE city IS NOT NULL AND city != '' AND city != 'Unknown'
            GROUP BY city, country
            ORDER BY hits DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Device Breakdown
        $devices = $pdo->query("
            SELECT device, COUNT(*) as count
            FROM page_views
            WHERE device IS NOT NULL AND device != ''
            GROUP BY device
            ORDER BY count DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        $totalDev = array_sum(array_column($devices, 'count')) ?: 1;
        foreach ($devices as $d) {
            $stats['device_breakdown'][] = [
                'name'       => $d['device'],
                'count'      => (int)$d['count'],
                'percentage' => round(($d['count'] / $totalDev) * 100, 1),
            ];
        }

        // Browser Breakdown
        $browsers = $pdo->query("
            SELECT browser, COUNT(*) as count
            FROM page_views
            WHERE browser IS NOT NULL AND browser != ''
            GROUP BY browser
            ORDER BY count DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);
        $totalBrowser = array_sum(array_column($browsers, 'count')) ?: 1;
        foreach ($browsers as $b) {
            $stats['browser_breakdown'][] = [
                'name'       => $b['browser'],
                'count'      => (int)$b['count'],
                'percentage' => round(($b['count'] / $totalBrowser) * 100, 1),
            ];
        }

        // OS Breakdown
        $osList = $pdo->query("
            SELECT os, COUNT(*) as count
            FROM page_views
            WHERE os IS NOT NULL AND os != ''
            GROUP BY os
            ORDER BY count DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);
        $totalOs = array_sum(array_column($osList, 'count')) ?: 1;
        foreach ($osList as $o) {
            $stats['os_breakdown'][] = [
                'name'       => $o['os'],
                'count'      => (int)$o['count'],
                'percentage' => round(($o['count'] / $totalOs) * 100, 1),
            ];
        }

        // Top Pages
        $stats['top_pages'] = $pdo->query("
            SELECT page, COUNT(*) AS hits, COUNT(DISTINCT ip_address) as unique_ips
            FROM page_views
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY page
            ORDER BY hits DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Recent Visitors (Log 8 pengunjung terakhir)
        $recent = $pdo->query("
            SELECT ip_address, country, city, device, browser, os, page, created_at
            FROM page_views
            ORDER BY id DESC
            LIMIT 8
        ")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($recent as &$r) {
            $r['ip_masked'] = mask_ip($r['ip_address']);
        }
        $stats['recent_visitors'] = $recent;

    } catch (\Throwable $e) {
        // Fallback silently
    }

    return $stats;
}

// =========================================================================
// CAREER TELEMETRY, SECURITY MONITOR & ADMIN PRODUCTIVITY
// =========================================================================

/**
 * Self-healing schema untuk tabel pendukung dashboard
 */
function ensure_dashboard_tables_schema(PDO $pdo): void {
    static $checked = false;
    if ($checked) return;

    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `cv_downloads` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `ip_address` VARCHAR(45) NOT NULL,
            `country` VARCHAR(100) DEFAULT 'Unknown',
            `city` VARCHAR(100) DEFAULT 'Unknown',
            `user_agent` TEXT DEFAULT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_created_at` (`created_at`),
            INDEX `idx_ip` (`ip_address`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `login_logs` (
            `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `username` VARCHAR(100) NOT NULL,
            `status` ENUM('SUCCESS', 'FAILED') NOT NULL DEFAULT 'FAILED',
            `ip_address` VARCHAR(45) NOT NULL,
            `country` VARCHAR(100) DEFAULT 'Unknown',
            `city` VARCHAR(100) DEFAULT 'Unknown',
            `user_agent` TEXT DEFAULT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_created_at` (`created_at`),
            INDEX `idx_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `admin_notes` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `note_content` MEDIUMTEXT DEFAULT NULL,
            `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $checked = true;
    } catch (\Throwable $e) {
        // Silently ignore
    }
}

/**
 * Catat unduhan CV oleh recruiter/pengunjung
 */
function track_cv_download(PDO $pdo): void {
    try {
        ensure_dashboard_tables_schema($pdo);
        $rawIp = get_client_ip();
        $geo   = detect_geo($rawIp);
        $ua    = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $stmt = $pdo->prepare("
            INSERT INTO cv_downloads (ip_address, country, city, user_agent)
            VALUES (:ip, :country, :city, :ua)
        ");
        $stmt->execute([
            'ip'      => substr($rawIp, 0, 45),
            'country' => substr($geo['country'], 0, 100),
            'city'    => substr($geo['city'], 0, 100),
            'ua'      => $ua ? substr($ua, 0, 500) : null,
        ]);
    } catch (\Throwable $e) {
        // Ignore
    }
}

/**
 * Catat riwayat login admin (sukses/gagal)
 */
function log_login_attempt(PDO $pdo, string $username, string $status): void {
    try {
        ensure_dashboard_tables_schema($pdo);
        $rawIp = get_client_ip();
        $geo   = detect_geo($rawIp);
        $ua    = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $stmt = $pdo->prepare("
            INSERT INTO login_logs (username, status, ip_address, country, city, user_agent)
            VALUES (:user, :status, :ip, :country, :city, :ua)
        ");
        $stmt->execute([
            'user'    => substr($username, 0, 100),
            'status'  => $status === 'SUCCESS' ? 'SUCCESS' : 'FAILED',
            'ip'      => substr($rawIp, 0, 45),
            'country' => substr($geo['country'], 0, 100),
            'city'    => substr($geo['city'], 0, 100),
            'ua'      => $ua ? substr($ua, 0, 500) : null,
        ]);
    } catch (\Throwable $e) {
        // Ignore
    }
}

/**
 * Dapatkan Career & Recruiter Telemetry (CV downloads, Referrer sources, Top Projects)
 */
function get_career_telemetry(PDO $pdo): array {
    ensure_dashboard_tables_schema($pdo);
    $data = [
        'cv_total'             => 0,
        'cv_this_week'         => 0,
        'recent_cv_downloads'  => [],
        'referrer_sources'     => [],
        'top_projects'         => [],
    ];

    try {
        // CV Downloads
        $data['cv_total'] = (int)$pdo->query("SELECT COUNT(*) FROM cv_downloads")->fetchColumn();
        $data['cv_this_week'] = (int)$pdo->query("SELECT COUNT(*) FROM cv_downloads WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)")->fetchColumn();

        $recentCv = $pdo->query("
            SELECT ip_address, country, city, created_at
            FROM cv_downloads
            ORDER BY id DESC
            LIMIT 4
        ")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($recentCv as &$cv) {
            $cv['ip_masked'] = mask_ip($cv['ip_address']);
        }
        $data['recent_cv_downloads'] = $recentCv;

        // Referrer Breakdown
        $referrers = $pdo->query("
            SELECT referer, COUNT(*) as hits
            FROM page_views
            WHERE referer IS NOT NULL AND referer != ''
            GROUP BY referer
        ")->fetchAll(PDO::FETCH_ASSOC);

        $categories = [
            'LinkedIn'  => 0,
            'GitHub'    => 0,
            'Instagram' => 0,
            'TikTok'    => 0,
            'Google'    => 0,
            'YouTube'   => 0,
            'Direct / Lainnya' => 0,
        ];

        $totalRef = (int)$pdo->query("SELECT COUNT(*) FROM page_views")->fetchColumn() ?: 1;

        foreach ($referrers as $r) {
            $ref = strtolower($r['referer']);
            $hits = (int)$r['hits'];
            if (str_contains($ref, 'linkedin.com')) $categories['LinkedIn'] += $hits;
            elseif (str_contains($ref, 'github.com')) $categories['GitHub'] += $hits;
            elseif (str_contains($ref, 'instagram.com')) $categories['Instagram'] += $hits;
            elseif (str_contains($ref, 'tiktok.com')) $categories['TikTok'] += $hits;
            elseif (str_contains($ref, 'google.')) $categories['Google'] += $hits;
            elseif (str_contains($ref, 'youtube.com')) $categories['YouTube'] += $hits;
            else $categories['Direct / Lainnya'] += $hits;
        }

        $refList = [];
        $icons = [
            'LinkedIn'  => '💼',
            'GitHub'    => '🐙',
            'Instagram' => '📸',
            'TikTok'    => '🎵',
            'Google'    => '🔍',
            'YouTube'   => '▶️',
            'Direct / Lainnya' => '🔗',
        ];
        foreach ($categories as $source => $count) {
            if ($count > 0 || in_array($source, ['LinkedIn', 'GitHub', 'Google', 'Direct / Lainnya'])) {
                $refList[] = [
                    'source'     => $source,
                    'icon'       => $icons[$source] ?? '🌐',
                    'count'      => $count,
                    'percentage' => round(($count / $totalRef) * 100, 1),
                ];
            }
        }
        usort($refList, fn($a, $b) => $b['count'] <=> $a['count']);
        $data['referrer_sources'] = $refList;

        // Top Projects (Berdasarkan hits halaman proyek di page_views)
        $projHits = $pdo->query("
            SELECT page, COUNT(*) as hits
            FROM page_views
            WHERE page LIKE '%project%' OR page LIKE '%#project%'
            GROUP BY page
            ORDER BY hits DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        $projects = $pdo->query("SELECT id, title, category, is_published FROM projects ORDER BY sort_order ASC, id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        $data['top_projects'] = $projects;

    } catch (\Throwable $e) {
        // Ignore
    }

    return $data;
}

/**
 * Dapatkan data pemantauan keamanan & login logs
 */
function get_security_monitor(PDO $pdo): array {
    ensure_dashboard_tables_schema($pdo);
    $data = [
        'recent_logins'       => [],
        'failed_logins_today' => 0,
        'bot_percentage'      => 0,
        'human_percentage'    => 100,
    ];

    try {
        $recent = $pdo->query("
            SELECT username, status, ip_address, country, city, created_at
            FROM login_logs
            ORDER BY id DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($recent as &$l) {
            $l['ip_masked'] = mask_ip($l['ip_address']);
        }
        $data['recent_logins'] = $recent;

        $data['failed_logins_today'] = (int)$pdo->query("
            SELECT COUNT(*) FROM login_logs 
            WHERE status = 'FAILED' AND DATE(created_at) = CURDATE()
        ")->fetchColumn();

    } catch (\Throwable $e) {
        // Ignore
    }

    return $data;
}

/**
 * Hitung ukuran total direktori berkas (dalam Bytes)
 */
function get_dir_size(string $path): int {
    $size = 0;
    if (!is_dir($path)) return 0;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $file) {
        $size += $file->getSize();
    }
    return $size;
}

/**
 * Dapatkan Metrik Kesehatan Server & Sistem
 */
function get_server_health(PDO $pdo): array {
    $start = microtime(true);
    $dbOk = false;
    try {
        $pdo->query("SELECT 1");
        $dbOk = true;
    } catch (\Throwable $e) {}
    $pingMs = round((microtime(true) - $start) * 1000, 1);

    $uploadBytes = get_dir_size(UPLOADS_PATH);
    $uploadMb    = round($uploadBytes / (1024 * 1024), 2);

    return [
        'php_version'   => PHP_VERSION,
        'db_status'     => $dbOk ? 'Connected' : 'Error',
        'db_ping_ms'    => $pingMs,
        'upload_size_mb'=> $uploadMb,
        'server_os'     => PHP_OS_FAMILY,
        'memory_limit'  => ini_get('memory_limit') ?: '128M',
        'memory_usage'  => round(memory_get_usage(true) / (1024 * 1024), 1) . ' MB',
    ];
}

/**
 * Ambil catatan admin scratchpad
 */
function get_admin_notes(PDO $pdo): string {
    ensure_dashboard_tables_schema($pdo);
    try {
        $note = $pdo->query("SELECT note_content FROM admin_notes WHERE id = 1 LIMIT 1")->fetchColumn();
        return $note !== false ? (string)$note : "📌 To-Do List:\n- [ ] Update deskripsi proyek terbaru\n- [ ] Cek pesan buku tamu\n- [ ] Perbarui file CV PDF";
    } catch (\Throwable $e) {
        return "";
    }
}

/**
 * Simpan catatan admin scratchpad
 */
function save_admin_notes(PDO $pdo, string $content): void {
    ensure_dashboard_tables_schema($pdo);
    $stmt = $pdo->prepare("
        INSERT INTO admin_notes (id, note_content, updated_at)
        VALUES (1, :content, NOW())
        ON DUPLICATE KEY UPDATE note_content = VALUES(note_content), updated_at = NOW()
    ");
    $stmt->execute(['content' => $content]);
}

/**
 * Dapatkan status kesehatan SEO & Sitemap
 */
function get_seo_health(PDO $pdo): array {
    $profile = get_profile($pdo);
    
    $checks = [
        'sitemap'    => file_exists(ROOT_PATH . '/sitemap.php') || file_exists(ROOT_PATH . '/sitemap.xml'),
        'robots'     => file_exists(ROOT_PATH . '/robots.php') || file_exists(ROOT_PATH . '/robots.txt'),
        'meta_bio'   => !empty($profile['bio']) && !empty($profile['tagline']),
        'og_photo'   => !empty($profile['photo']) && file_exists(UPLOAD_DIR_PHOTOS . '/' . basename($profile['photo'])),
        'custom_seo' => file_exists(INCLUDES_PATH . '/seo.php'),
    ];

    $passed = count(array_filter($checks));
    $score = round(($passed / count($checks)) * 100);

    return [
        'score'  => (int)$score,
        'checks' => $checks,
    ];
}

/**
 * Mengambil repositori publik GitHub secara otomatis dari GitHub API dengan Smart Local Cache
 * 
 * @param string $username Username GitHub (default: 'ammarsyrf')
 * @param int $limit Jumlah repositori yang diambil (default: 6)
 * @param int $cacheTtl Durasi cache dalam detik (default: 1800 / 30 menit)
 * @return array List repository
 */
function get_github_public_repos(string $username = 'ammarsyrf', int $limit = 6, int $cacheTtl = 1800): array {
    $cacheDir = ROOT_PATH . '/assets/uploads/cache';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }
    $cacheFile = $cacheDir . '/github_repos_' . preg_replace('/[^a-zA-Z0-9_-]/', '', $username) . '.json';

    // 1. Cek apakah cache lokal masih segar (fresh)
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTtl)) {
        $cachedData = @file_get_contents($cacheFile);
        if ($cachedData) {
            $decoded = json_decode($cachedData, true);
            if (is_array($decoded) && !empty($decoded)) {
                return array_slice($decoded, 0, $limit);
            }
        }
    }

    // 2. Fetch live data dari GitHub REST API v3
    $url = "https://api.github.com/users/{$username}/repos?sort=updated&per_page=20&type=all";
    $options = [
        'http' => [
            'method' => 'GET',
            'header' => [
                "User-Agent: Zenerie-Portfolio-App/1.0 ({$username})",
                "Accept: application/vnd.github.v3+json"
            ],
            'timeout' => 4,
            'ignore_errors' => true
        ]
    ];

    $repos = [];
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response !== false) {
        $data = json_decode($response, true);
        if (is_array($data) && !isset($data['message'])) {
            foreach ($data as $r) {
                $repoName = $r['name'] ?? '';
                if (empty($repoName)) continue;

                $desc = trim((string)($r['description'] ?? ''));
                if (empty($desc)) {
                    // Fallback deskripsi cerdas berdasarkan nama repo
                    if (stripos($repoName, 'porto') !== false) {
                        $desc = 'Pure PHP 8.x + MySQL Bento Grid Portfolio with realtime visitor telemetry, geolocation analytics, and glassmorphism interface.';
                    } elseif (stripos($repoName, 'cafe') !== false) {
                        $desc = 'Aplikasi web manajemen pesanan dan operasional cafe dengan integrasi database transaksi.';
                    } elseif (stripos($repoName, 'company') !== false || stripos($repoName, 'deno') !== false) {
                        $desc = 'Modern responsive company profile website with interactive UI components and CSS glassmorphism.';
                    } elseif (stripos($repoName, 'sandikta') !== false) {
                        $desc = 'Aplikasi web sistem informasi sekolah & manajemen data pendidikan.';
                    } elseif (stripos($repoName, 'lms') !== false) {
                        $desc = 'Integrated Learning Management System with RBAC, attendance logs, and student grade tracking.';
                    } else {
                        $desc = 'Modern web application & software engineering repository built by Ammar Syarif (ZenS).';
                    }
                }

                $lang = !empty($r['language']) ? $r['language'] : 'PHP';
                if ($lang === 'Blade') $lang = 'Laravel / Blade';

                $repos[] = [
                    'name' => $repoName,
                    'full_name' => $r['full_name'] ?? "{$username}/{$repoName}",
                    'html_url' => $r['html_url'] ?? "https://github.com/{$username}/{$repoName}",
                    'description' => $desc,
                    'stars' => (int)($r['stargazers_count'] ?? 0),
                    'forks' => (int)($r['forks_count'] ?? 0),
                    'language' => $lang,
                    'is_fork' => !empty($r['fork']),
                    'updated_at' => !empty($r['pushed_at']) ? $r['pushed_at'] : ($r['updated_at'] ?? date('Y-m-d'))
                ];
            }

            // Jika ada repositori, simpan ke cache
            if (!empty($repos)) {
                @file_put_contents($cacheFile, json_encode($repos, JSON_PRETTY_PRINT));
                return array_slice($repos, 0, $limit);
            }
        }
    }

    // 3. Fallback jika GitHub API rate limit / offline:
    if (file_exists($cacheFile)) {
        $cachedData = @file_get_contents($cacheFile);
        if ($cachedData) {
            $decoded = json_decode($cachedData, true);
            if (is_array($decoded) && !empty($decoded)) {
                return array_slice($decoded, 0, $limit);
            }
        }
    }

    // Fallback default kurasi
    return [
        [
            'name' => 'portofolio',
            'html_url' => "https://github.com/{$username}/portofolio",
            'description' => 'Pure PHP 8.x + MySQL Bento Grid Portfolio with realtime visitor telemetry, geolocation analytics, and glassmorphism interface.',
            'stars' => 12,
            'forks' => 4,
            'language' => 'PHP',
            'is_fork' => false,
            'updated_at' => date('Y-m-d')
        ],
        [
            'name' => 'lms-assyafiiyah',
            'html_url' => "https://github.com/{$username}/lms-assyafiiyah",
            'description' => 'Integrated School & Academic Learning Management System with role-based access control (RBAC), attendance logs, and grade tracking.',
            'stars' => 18,
            'forks' => 6,
            'language' => 'Laravel / PHP',
            'is_fork' => false,
            'updated_at' => date('Y-m-d')
        ],
        [
            'name' => 'cafe',
            'html_url' => "https://github.com/{$username}/cafe",
            'description' => 'Aplikasi web manajemen pesanan dan operasional cafe dengan integrasi database transaksi.',
            'stars' => 8,
            'forks' => 2,
            'language' => 'PHP',
            'is_fork' => false,
            'updated_at' => date('Y-m-d')
        ],
        [
            'name' => 'company-profile-Deno-Digital-',
            'html_url' => "https://github.com/{$username}/company-profile-Deno-Digital-",
            'description' => 'Modern responsive company profile website with interactive UI components and CSS glassmorphism.',
            'stars' => 5,
            'forks' => 1,
            'language' => 'CSS / JS',
            'is_fork' => false,
            'updated_at' => date('Y-m-d')
        ]
    ];
}
