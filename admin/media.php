<?php
/**
 * Media & Assets Gallery Manager
 * Features:
 * - Drag & Drop Upload Zone (supports JPG, PNG, WEBP, SVG, GIF, PDF)
 * - Auto-convert & compress to WebP for 100/100 performance
 * - Filter by category (Semua, Proyek, Sertifikat, Kreasi, Profil, Umum)
 * - 1-Click Copy URL to Clipboard (with toast alert)
 * - Image zoom modal preview
 * - Delete asset with confirmation & activity logging
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Galeri Media & Assets';

// Target upload directories
$mediaDirs = [
    'projects'     => ['name' => 'Proyek Portofolio', 'dir' => UPLOAD_DIR_PROJECTS, 'badge' => 'badge-projects'],
    'achievements' => ['name' => 'Pencapaian & Sertifikat', 'dir' => UPLOAD_DIR_ACHIEVEMENTS, 'badge' => 'badge-achieve'],
    'creations'    => ['name' => 'Kreasi TikTok/IG', 'dir' => UPLOAD_DIR_CREATIONS, 'badge' => 'badge-creation'],
    'photos'       => ['name' => 'Foto Profil', 'dir' => UPLOAD_DIR_PHOTOS, 'badge' => 'badge-photo'],
    'general'      => ['name' => 'Aset Umum & Banner', 'dir' => ASSETS_PATH . '/uploads/general', 'badge' => 'badge-general'],
];

// Ensure general dir exists
if (!is_dir(ASSETS_PATH . '/uploads/general')) {
    @mkdir(ASSETS_PATH . '/uploads/general', 0755, true);
}

// ── Handle AJAX Actions (Upload / Delete) ───────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'upload') {
        header('Content-Type: application/json');
        
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'error' => 'Token CSRF tidak valid. Silakan muat ulang halaman.']);
            exit;
        }

        $targetFolder = $_POST['target_folder'] ?? 'general';
        if (!isset($mediaDirs[$targetFolder])) {
            $targetFolder = 'general';
        }

        $targetDir = $mediaDirs[$targetFolder]['dir'];
        $convertToWebp = (!empty($_POST['convert_webp']) && $_POST['convert_webp'] === '1');
        $uploadedFiles = [];
        $errors = [];

        if (empty($_FILES['files']['name']) || !is_array($_FILES['files']['name'])) {
            echo json_encode(['success' => false, 'error' => 'Tidak ada file yang dipilih untuk diunggah.']);
            exit;
        }

        $fileCount = count($_FILES['files']['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $singleFile = [
                'name'     => $_FILES['files']['name'][$i],
                'type'     => $_FILES['files']['type'][$i],
                'tmp_name' => $_FILES['files']['tmp_name'][$i],
                'error'    => $_FILES['files']['error'][$i],
                'size'     => $_FILES['files']['size'][$i],
            ];

            $ext = strtolower(pathinfo($singleFile['name'], PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true);
            $cleanPrefix = $targetFolder . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3));

            if ($isImage && $convertToWebp && function_exists('imagewebp')) {
                $finalFilename = $cleanPrefix . '.webp';
                $destPath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $finalFilename;
                
                $converted = convert_image_to_webp($singleFile['tmp_name'], $destPath, 85);
                if ($converted) {
                    $uploadedFiles[] = $finalFilename;
                    log_admin_activity($pdo, 'Upload Media (Auto-WebP)', "Folder: {$targetFolder}, File: {$finalFilename}");
                } else {
                    // Fallback to normal upload
                    $finalFilename = $cleanPrefix . '.' . $ext;
                    $destPath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $finalFilename;
                    if (move_uploaded_file($singleFile['tmp_name'], $destPath)) {
                        $uploadedFiles[] = $finalFilename;
                        log_admin_activity($pdo, 'Upload Media', "Folder: {$targetFolder}, File: {$finalFilename}");
                    }
                }
            } else {
                $finalFilename = $cleanPrefix . '.' . $ext;
                $destPath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $finalFilename;
                if (move_uploaded_file($singleFile['tmp_name'], $destPath)) {
                    $uploadedFiles[] = $finalFilename;
                    log_admin_activity($pdo, 'Upload Media', "Folder: {$targetFolder}, File: {$finalFilename}");
                }
            }
        }

        if (!empty($uploadedFiles)) {
            echo json_encode([
                'success' => true,
                'message' => count($uploadedFiles) . ' file berhasil diunggah & dioptimasi!',
                'files'   => $uploadedFiles
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error'   => 'Gagal mengunggah file. Pastikan format didukung (JPG, PNG, WebP, PDF).'
            ]);
        }
        exit;
    }

    if ($action === 'delete') {
        header('Content-Type: application/json');
        
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'error' => 'Token CSRF tidak valid.']);
            exit;
        }

        $folder = $_POST['folder'] ?? '';
        $filename = basename($_POST['filename'] ?? '');

        if (!isset($mediaDirs[$folder]) || empty($filename)) {
            echo json_encode(['success' => false, 'error' => 'Parameter tidak valid.']);
            exit;
        }

        $filePath = rtrim($mediaDirs[$folder]['dir'], '/\\') . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($filePath) && is_file($filePath)) {
            if (@unlink($filePath)) {
                log_admin_activity($pdo, 'Hapus Media', "Folder: {$folder}, File: {$filename}");
                echo json_encode(['success' => true, 'message' => 'File berhasil dihapus.']);
                exit;
            }
        }

        echo json_encode(['success' => false, 'error' => 'File tidak ditemukan atau gagal dihapus dari server.']);
        exit;
    }
}

// ── Scan All Files in Upload Directories ────────────────────────────────────
$allMedia = [];
foreach ($mediaDirs as $key => $meta) {
    if (!is_dir($meta['dir'])) continue;

    $files = scandir($meta['dir']);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || $file === '.gitkeep' || $file === '.htaccess') continue;
        
        $fullPath = $meta['dir'] . '/' . $file;
        if (!is_file($fullPath)) continue;

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $size = filesize($fullPath);
        $mtime = filemtime($fullPath);
        $url = upload_url($key, $file);
        if ($key === 'general') {
            $url = BASE_URL . '/assets/uploads/general/' . rawurlencode($file);
        }

        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
        $dimensions = '';
        if ($isImage && in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $imgInfo = @getimagesize($fullPath);
            if ($imgInfo) {
                $dimensions = $imgInfo[0] . 'x' . $imgInfo[1];
            }
        }

        $allMedia[] = [
            'folder'     => $key,
            'folder_name'=> $meta['name'],
            'badge'      => $meta['badge'],
            'filename'   => $file,
            'ext'        => $ext,
            'size'       => $size,
            'size_human' => ($size > 1048576) ? round($size / 1048576, 2) . ' MB' : round($size / 1024, 1) . ' KB',
            'mtime'      => $mtime,
            'date_human' => date('d M Y, H:i', $mtime),
            'url'        => $url,
            'is_image'   => $isImage,
            'dimensions' => $dimensions,
        ];
    }
}

// Sort newest first
usort($allMedia, function ($a, $b) {
    return $b['mtime'] <=> $a['mtime'];
});

$totalFiles = count($allMedia);
$totalSize = array_sum(array_column($allMedia, 'size'));
$totalSizeHuman = ($totalSize > 1048576) ? round($totalSize / 1048576, 2) . ' MB' : round($totalSize / 1024, 1) . ' KB';
$webpCount = count(array_filter($allMedia, fn($m) => $m['ext'] === 'webp'));

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<style>
  .media-summary-bar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
  }
  .media-stat-box {
    background: var(--adm-gradient-card);
    border: 1px solid var(--adm-border);
    border-radius: 14px;
    padding: 1.25rem 1.45rem;
    display: flex;
    align-items: center;
    gap: 1.15rem;
    backdrop-filter: blur(16px);
    box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.05);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .media-stat-box:hover {
    border-color: rgba(59, 130, 246, 0.4);
    transform: translateY(-2px);
  }
  .media-stat-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.15));
    border: 1px solid rgba(59, 130, 246, 0.35);
    color: var(--adm-accent-bright);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.2);
  }
  .media-stat-val {
    font-size: 1.55rem;
    font-weight: 800;
    color: #ffffff;
    font-family: var(--font-display);
    line-height: 1.2;
    letter-spacing: -0.02em;
  }
  .media-stat-lbl {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--adm-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.06em;
  }

  /* Upload Dropzone */
  .dropzone-container {
    background: var(--adm-gradient-card);
    border: 2px dashed rgba(59, 130, 246, 0.35);
    border-radius: 16px;
    padding: 2.5rem 1.5rem;
    text-align: center;
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    margin-bottom: 2.25rem;
    position: relative;
    cursor: pointer;
    backdrop-filter: blur(16px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
  }
  .dropzone-container:hover, .dropzone-container.drag-over {
    border-color: var(--adm-accent-bright);
    background: rgba(59, 130, 246, 0.08);
    box-shadow: 0 16px 40px rgba(59, 130, 246, 0.15);
    transform: translateY(-2px);
  }
  .dropzone-icon {
    font-size: 2.75rem;
    margin-bottom: 0.75rem;
    display: inline-block;
    transition: transform 0.2s ease;
  }
  .dropzone-container:hover .dropzone-icon {
    transform: scale(1.1);
  }
  .dropzone-title {
    font-family: var(--font-display);
    font-size: 1.15rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 0.4rem;
  }
  .dropzone-sub {
    font-size: 0.82rem;
    color: var(--adm-text-secondary);
    margin-bottom: 1.35rem;
  }
  .dropzone-options {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
    margin-top: 1.15rem;
    font-size: 0.84rem;
    color: var(--adm-text-secondary);
  }

  /* Controls & Filter Bar */
  .media-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.75rem;
  }
  .media-filter-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
  }
  .media-tab-btn {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid var(--adm-border);
    color: var(--adm-text-secondary);
    padding: 0.45rem 0.95rem;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.18s ease;
  }
  .media-tab-btn:hover {
    color: #ffffff;
    border-color: rgba(59, 130, 246, 0.4);
    background: rgba(255, 255, 255, 0.08);
  }
  .media-tab-btn.is-active {
    background: var(--adm-gradient-primary);
    color: #fff;
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.35);
  }
  .media-search-box {
    position: relative;
    min-width: 260px;
  }
  .media-search-input {
    width: 100%;
    background: rgba(15, 22, 36, 0.8);
    border: 1px solid var(--adm-border);
    color: #ffffff;
    padding: 0.55rem 0.95rem 0.55rem 2.3rem;
    border-radius: 9px;
    font-size: 0.84rem;
    outline: none;
    transition: all 0.2s ease;
  }
  .media-search-input:focus {
    border-color: var(--adm-accent);
    box-shadow: 0 0 16px rgba(59, 130, 246, 0.2);
  }
  .media-search-icon {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--adm-text-muted);
    pointer-events: none;
  }

  /* Gallery Grid */
  .media-gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1.25rem;
  }
  .media-card {
    background: var(--adm-gradient-card);
    border: 1px solid var(--adm-border);
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    backdrop-filter: blur(12px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
  }
  .media-card:hover {
    border-color: rgba(59, 130, 246, 0.5);
    transform: translateY(-3px);
    box-shadow: 0 14px 32px rgba(0, 0, 0, 0.5), 0 0 20px rgba(59, 130, 246, 0.2);
  }
  .media-thumb-wrap {
    height: 150px;
    background: #080b11;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
    cursor: pointer;
  }
  .media-thumb-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .media-card:hover .media-thumb-img {
    transform: scale(1.08);
  }
  .media-file-icon {
    font-size: 3.25rem;
    opacity: 0.75;
  }
  .media-ext-badge {
    position: absolute;
    top: 0.6rem;
    left: 0.6rem;
    font-size: 0.65rem;
    font-family: var(--font-mono);
    font-weight: 700;
    padding: 0.15rem 0.5rem;
    border-radius: 5px;
    text-transform: uppercase;
    background: rgba(10, 15, 25, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.18);
    color: #fff;
    backdrop-filter: blur(8px);
  }
  .ext-webp {
    background: rgba(16, 185, 129, 0.85);
    color: #fff;
    border-color: #34d399;
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.35);
  }
  .media-card-body {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    flex-grow: 1;
  }
  .media-card-name {
    font-size: 0.82rem;
    font-weight: 600;
    color: #ffffff;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .media-card-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.72rem;
    color: var(--adm-text-muted);
    font-family: var(--font-mono);
  }
  .media-card-actions {
    display: flex;
    gap: 0.45rem;
    margin-top: 0.6rem;
    padding-top: 0.6rem;
    border-top: 1px solid var(--adm-border);
  }
  .btn-copy-url, .btn-del-media {
    flex: 1;
    padding: 0.45rem 0.65rem;
    font-size: 0.76rem;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    border: 1px solid transparent;
    transition: all 0.18s ease;
  }
  .btn-copy-url {
    background: rgba(255, 255, 255, 0.05);
    color: var(--adm-text-secondary);
    border-color: var(--adm-border);
  }
  .btn-copy-url:hover {
    background: var(--adm-accent);
    color: #fff;
    border-color: var(--adm-accent);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
  }
  .btn-del-media {
    background: rgba(244, 63, 94, 0.12);
    color: #fda4af;
    border-color: rgba(244, 63, 94, 0.3);
    max-width: 38px;
    flex: 0 0 38px;
  }
  .btn-del-media:hover {
    background: #f43f5e;
    color: #fff;
    box-shadow: 0 4px 12px rgba(244, 63, 94, 0.3);
  }

  /* Image Modal */
  .media-modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(3, 6, 12, 0.88);
    backdrop-filter: blur(16px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
  }
  .media-modal-backdrop.is-open {
    display: flex;
  }
  .media-modal-content {
    max-width: 90vw;
    max-height: 85vh;
    background: #0d131f;
    border: 1px solid var(--adm-border);
    border-radius: 16px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 25px 60px rgba(0,0,0,0.7), 0 0 40px rgba(59, 130, 246, 0.2);
  }
  .media-thumb-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
  }
  .media-card:hover .media-thumb-img {
    transform: scale(1.05);
  }
  .media-file-icon {
    font-size: 3rem;
    opacity: 0.7;
  }
  .media-ext-badge {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.15rem 0.45rem;
    border-radius: 4px;
    text-transform: uppercase;
    background: rgba(10, 15, 25, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #fff;
  }
  .ext-webp {
    background: rgba(16, 185, 129, 0.85);
    color: #fff;
    border-color: #34d399;
  }
  .media-card-body {
    padding: 0.85rem;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    flex-grow: 1;
  }
  .media-card-name {
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--color-text);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .media-card-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.7rem;
    color: var(--color-text-faint);
  }
  .media-card-actions {
    display: flex;
    gap: 0.4rem;
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid var(--color-line);
  }
  .btn-copy-url, .btn-del-media {
    flex: 1;
    padding: 0.35rem 0.5rem;
    font-size: 0.72rem;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    border: 1px solid transparent;
    transition: all 0.15s;
  }
  .btn-copy-url {
    background: var(--color-bg-surface);
    color: var(--color-text-dim);
    border-color: var(--color-line);
  }
  .btn-copy-url:hover {
    background: var(--color-accent);
    color: #fff;
    border-color: var(--color-accent);
  }
  .btn-del-media {
    background: rgba(239, 68, 68, 0.1);
    color: #f87171;
    border-color: rgba(239, 68, 68, 0.25);
    max-width: 36px;
    flex: 0 0 36px;
  }
  .btn-del-media:hover {
    background: #ef4444;
    color: #fff;
  }

  /* Image Modal */
  .media-modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(8px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
  }
  .media-modal-backdrop.is-open {
    display: flex;
  }
  .media-modal-content {
    max-width: 90vw;
    max-height: 85vh;
    background: var(--color-bg-alt);
    border: 1px solid var(--color-line);
    border-radius: 10px;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.85rem;
    position: relative;
  }
  .media-modal-img {
    max-width: 100%;
    max-height: 65vh;
    object-fit: contain;
    border-radius: 6px;
  }
</style>

<!-- Summary Cards -->
<div class="media-summary-bar">
  <div class="media-stat-box">
    <div class="media-stat-icon">📁</div>
    <div>
      <div class="media-stat-val" id="statTotalCount"><?= $totalFiles ?></div>
      <div class="media-stat-lbl">Total File Media</div>
    </div>
  </div>
  <div class="media-stat-box">
    <div class="media-stat-icon">💾</div>
    <div>
      <div class="media-stat-val"><?= $totalSizeHuman ?></div>
      <div class="media-stat-lbl">Kapasitas Terpakai</div>
    </div>
  </div>
  <div class="media-stat-box">
    <div class="media-stat-icon" style="background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.3); color: #34d399;">⚡</div>
    <div>
      <div class="media-stat-val"><?= $webpCount ?> File</div>
      <div class="media-stat-lbl">Format WebP Cepat (100% Score)</div>
    </div>
  </div>
</div>

<!-- Drag & Drop Upload Zone -->
<div class="dropzone-container" id="dropzoneBox">
  <input type="file" id="fileUploadInput" multiple accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml,application/pdf" style="display: none;">
  
  <div class="dropzone-icon">☁️</div>
  <div class="dropzone-title">Tarik &amp; Lepaskan File ke Sini atau Klik untuk Memilih</div>
  <div class="dropzone-sub">Mendukung file JPG, PNG, WEBP, GIF, SVG, dan PDF hingga 5 MB per file</div>

  <div class="dropzone-options" onclick="event.stopPropagation();">
    <label style="display: flex; align-items: center; gap: 0.4rem; font-weight: 500;">
      <span>Simpan ke Folder:</span>
      <select id="targetFolderSelect" class="form-select" style="width: auto; padding: 0.35rem 0.65rem; font-size: 0.8rem;">
        <?php foreach ($mediaDirs as $fKey => $fMeta): ?>
          <option value="<?= $fKey ?>"><?= e($fMeta['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label style="display: flex; align-items: center; gap: 0.45rem; cursor: pointer; color: #34d399; font-weight: 600;">
      <input type="checkbox" id="convertWebpCheck" checked style="accent-color: #10b981; width: 16px; height: 16px;">
      <span>⚡ Otomatis Kompresi ke format WebP</span>
    </label>

    <button type="button" class="btn btn-primary btn-sm" id="btnTriggerBrowse" style="padding: 0.4rem 1rem;">
      Browse File 📂
    </button>
  </div>

  <div id="uploadProgressBox" style="display: none; margin-top: 1.25rem;">
    <div style="font-size: 0.78rem; color: var(--color-accent-bright); margin-bottom: 0.4rem;" id="uploadProgressText">Mengunggah file...</div>
    <div style="height: 6px; background: var(--color-bg-surface); border-radius: 9999px; overflow: hidden; max-width: 400px; margin: 0 auto;">
      <div id="uploadProgressBar" style="height: 100%; width: 0%; background: linear-gradient(90deg, #4c8dff, #34d399); transition: width 0.2s;"></div>
    </div>
  </div>
</div>

<!-- Controls & Toolbar -->
<div class="media-toolbar">
  <div class="media-filter-tabs" id="mediaFilterTabs">
    <button type="button" class="media-tab-btn is-active" data-filter="all">Semua (<?= $totalFiles ?>)</button>
    <?php foreach ($mediaDirs as $fKey => $fMeta): 
      $countInFolder = count(array_filter($allMedia, fn($m) => $m['folder'] === $fKey));
    ?>
      <button type="button" class="media-tab-btn" data-filter="<?= $fKey ?>"><?= e($fMeta['name']) ?> (<?= $countInFolder ?>)</button>
    <?php endforeach; ?>
  </div>

  <div class="media-search-box">
    <span class="media-search-icon">🔍</span>
    <input type="text" id="mediaSearchInput" class="media-search-input" placeholder="Cari nama file media...">
  </div>
</div>

<!-- Gallery Grid -->
<div class="media-gallery-grid" id="mediaGalleryGrid">
  <?php if (empty($allMedia)): ?>
    <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem; color: var(--color-text-faint);">
      <div style="font-size: 3rem; margin-bottom: 0.5rem;">📂</div>
      <p style="font-size: 0.95rem;">Belum ada file media yang diunggah.</p>
    </div>
  <?php else: ?>
    <?php foreach ($allMedia as $item): ?>
      <div class="media-card" data-folder="<?= e($item['folder']) ?>" data-filename="<?= strtolower(e($item['filename'])) ?>">
        <div class="media-thumb-wrap" onclick="previewMedia('<?= e($item['url']) ?>', '<?= e($item['filename']) ?>', '<?= e($item['dimensions']) ?>')">
          <?php if ($item['is_image']): ?>
            <img src="<?= e($item['url']) ?>" alt="<?= e($item['filename']) ?>" class="media-thumb-img" loading="lazy">
          <?php else: ?>
            <div class="media-file-icon">📄</div>
          <?php endif; ?>
          
          <span class="media-ext-badge <?= ($item['ext'] === 'webp') ? 'ext-webp' : '' ?>">
            <?= e($item['ext']) ?>
          </span>
        </div>

        <div class="media-card-body">
          <div class="media-card-name" title="<?= e($item['filename']) ?>">
            <?= e($item['filename']) ?>
          </div>
          <div class="media-card-meta">
            <span><?= e($item['size_human']) ?> <?= !empty($item['dimensions']) ? '• ' . e($item['dimensions']) : '' ?></span>
            <span><?= e($item['folder_name']) ?></span>
          </div>

          <div class="media-card-actions">
            <button type="button" class="btn-copy-url" onclick="copyAssetUrl('<?= e($item['url']) ?>', this)" title="Salin URL Lengkap">
              <span>📋</span> Salin URL
            </button>
            <button type="button" class="btn-del-media" onclick="deleteMediaFile('<?= e($item['folder']) ?>', '<?= e($item['filename']) ?>', this)" title="Hapus File">
              🗑️
            </button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- Modal Zoom Preview -->
<div class="media-modal-backdrop" id="mediaPreviewModal" onclick="closeMediaModal()">
  <div class="media-modal-content" onclick="event.stopPropagation();">
    <img id="modalPreviewImg" src="" alt="Preview" class="media-modal-img">
    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; font-size: 0.8rem; color: var(--color-text-dim);">
      <span id="modalPreviewTitle" style="font-weight: 600; color: var(--color-text);"></span>
      <span id="modalPreviewDim"></span>
    </div>
    <div style="display: flex; gap: 0.5rem; width: 100%;">
      <button type="button" class="btn btn-secondary btn-sm" style="flex:1;" onclick="copyAssetUrl(document.getElementById('modalPreviewImg').src, this)">
        📋 Salin URL
      </button>
      <button type="button" class="btn btn-primary btn-sm" onclick="closeMediaModal()">
        Tutup
      </button>
    </div>
  </div>
</div>

<script>
  const CSRF_TOKEN = '<?= csrf_token() ?>';
  const dropzoneBox = document.getElementById('dropzoneBox');
  const fileInput = document.getElementById('fileUploadInput');
  const btnBrowse = document.getElementById('btnTriggerBrowse');
  const folderSelect = document.getElementById('targetFolderSelect');
  const webpCheck = document.getElementById('convertWebpCheck');
  const progressBox = document.getElementById('uploadProgressBox');
  const progressBar = document.getElementById('uploadProgressBar');
  const progressText = document.getElementById('uploadProgressText');

  // Trigger file selection
  btnBrowse.addEventListener('click', (e) => {
    e.stopPropagation();
    fileInput.click();
  });
  dropzoneBox.addEventListener('click', () => fileInput.click());

  // Drag & Drop events
  ['dragenter', 'dragover'].forEach(eventName => {
    dropzoneBox.addEventListener(eventName, (e) => {
      e.preventDefault();
      e.stopPropagation();
      dropzoneBox.classList.add('drag-over');
    });
  });
  ['dragleave', 'drop'].forEach(eventName => {
    dropzoneBox.addEventListener(eventName, (e) => {
      e.preventDefault();
      e.stopPropagation();
      dropzoneBox.classList.remove('drag-over');
    });
  });
  dropzoneBox.addEventListener('drop', (e) => {
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
      handleFilesUpload(e.dataTransfer.files);
    }
  });
  fileInput.addEventListener('change', () => {
    if (fileInput.files && fileInput.files.length > 0) {
      handleFilesUpload(fileInput.files);
    }
  });

  function handleFilesUpload(files) {
    const formData = new FormData();
    formData.append('action', 'upload');
    formData.append('csrf_token', CSRF_TOKEN);
    formData.append('target_folder', folderSelect.value);
    formData.append('convert_webp', webpCheck.checked ? '1' : '0');

    for (let i = 0; i < files.length; i++) {
      formData.append('files[]', files[i]);
    }

    progressBox.style.display = 'block';
    progressText.innerText = `Mengunggah ${files.length} file...`;
    progressBar.style.width = '30%';

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'media.php', true);

    xhr.upload.onprogress = function(e) {
      if (e.lengthComputable) {
        const percent = Math.round((e.loaded / e.total) * 100);
        progressBar.style.width = percent + '%';
        progressText.innerText = `Mengunggah... ${percent}%`;
      }
    };

    xhr.onload = function() {
      if (xhr.status === 200) {
        try {
          const res = JSON.parse(xhr.responseText);
          if (res.success) {
            progressBar.style.width = '100%';
            progressText.innerText = 'Selesai! Memuat ulang galeri...';
            setTimeout(() => window.location.reload(), 700);
          } else {
            alert('Upload gagal: ' + (res.error || 'Terjadi kesalahan.'));
            progressBox.style.display = 'none';
          }
        } catch(e) {
          alert('Upload selesai dengan respons: ' + xhr.responseText);
          window.location.reload();
        }
      } else {
        alert('Server error (' + xhr.status + ') saat mengunggah.');
        progressBox.style.display = 'none';
      }
    };

    xhr.onerror = function() {
      alert('Koneksi terputus saat mengunggah.');
      progressBox.style.display = 'none';
    };

    xhr.send(formData);
  }

  // Copy Asset URL
  function copyAssetUrl(url, btn) {
    navigator.clipboard.writeText(url).then(() => {
      const origHtml = btn.innerHTML;
      btn.innerHTML = '<span>✅</span> Tersalin!';
      btn.style.color = '#34d399';
      setTimeout(() => {
        btn.innerHTML = origHtml;
        btn.style.color = '';
      }, 1500);
    });
  }

  // Delete Media File
  function deleteMediaFile(folder, filename, btn) {
    if (!confirm(`Hapus file "${filename}" secara permanen dari server?`)) {
      return;
    }

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('csrf_token', CSRF_TOKEN);
    formData.append('folder', folder);
    formData.append('filename', filename);

    btn.disabled = true;
    btn.innerHTML = '⏳';

    fetch('media.php', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        const card = btn.closest('.media-card');
        card.style.opacity = '0';
        card.style.transform = 'scale(0.8)';
        setTimeout(() => card.remove(), 250);
      } else {
        alert('Gagal menghapus: ' + (res.error || 'Kesalahan server.'));
        btn.disabled = false;
        btn.innerHTML = '🗑️';
      }
    })
    .catch(err => {
      alert('Error: ' + err);
      btn.disabled = false;
      btn.innerHTML = '🗑️';
    });
  }

  // Preview Modal
  function previewMedia(url, title, dim) {
    document.getElementById('modalPreviewImg').src = url;
    document.getElementById('modalPreviewTitle').innerText = title;
    document.getElementById('modalPreviewDim').innerText = dim;
    document.getElementById('mediaPreviewModal').classList.add('is-open');
  }

  function closeMediaModal() {
    document.getElementById('mediaPreviewModal').classList.remove('is-open');
  }

  // Filter and Search
  const filterBtns = document.querySelectorAll('.media-tab-btn');
  const searchInput = document.getElementById('mediaSearchInput');
  const mediaCards = document.querySelectorAll('.media-card');

  let activeFilter = 'all';

  filterBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      filterBtns.forEach(b => b.classList.remove('is-active'));
      this.classList.add('is-active');
      activeFilter = this.getAttribute('data-filter');
      applyFilterAndSearch();
    });
  });

  searchInput.addEventListener('input', () => applyFilterAndSearch());

  function applyFilterAndSearch() {
    const q = searchInput.value.toLowerCase().trim();

    mediaCards.forEach(card => {
      const folder = card.getAttribute('data-folder');
      const filename = card.getAttribute('data-filename');

      const matchesFilter = (activeFilter === 'all' || folder === activeFilter);
      const matchesSearch = (!q || filename.includes(q));

      if (matchesFilter && matchesSearch) {
        card.style.display = 'flex';
      } else {
        card.style.display = 'none';
      }
    });
  }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
