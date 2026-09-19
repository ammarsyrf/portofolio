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
