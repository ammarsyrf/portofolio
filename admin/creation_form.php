<?php
/**
 * Form Tambah & Edit Kreasi Konten (TikTok & Instagram)
 * Studio Edition: Live 9:16 Vertical Video Preview & Glassmorphism UI
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$creationId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = ($creationId > 0);

$creation = null;
if ($isEdit) {
    $creation = get_creation_by_id($pdo, $creationId);
    if (!$creation) {
        set_flash('danger', 'Data kreasi tidak ditemukan.');
        redirect(BASE_URL . '/admin/creations');
    }
    $pageTitle = 'Edit Kreasi: ' . $creation['title'];
} else {
    $pageTitle = 'Tambah Kreasi Konten Baru';
    $creation = [
        'id' => 0,
        'platform' => 'tiktok',
        'title' => '',
        'thumbnail' => '',
        'post_link' => '',
        'created_date' => '',
        'sort_order' => 0,
        'is_published' => 1
    ];
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $errorMessage = 'Sesi form tidak valid. Silakan coba kembali.';
    } else {
        $platform = in_array($_POST['platform'] ?? '', ['tiktok', 'instagram'], true) ? $_POST['platform'] : 'tiktok';
        $title = trim($_POST['title'] ?? '');
        $postLink = trim($_POST['post_link'] ?? '');
        $createdDate = trim($_POST['created_date'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isPublished = isset($_POST['is_published']) ? 1 : 0;
        $deleteExistingThumb = isset($_POST['delete_thumbnail']) && $_POST['delete_thumbnail'] === '1';

        if (empty($title)) {
            $errorMessage = 'Judul atau topik post wajib diisi.';
        } elseif (empty($postLink)) {
            $errorMessage = 'Tautan (URL) postingan TikTok / Instagram wajib diisi.';
        } else {
            $thumbFileName = $creation['thumbnail'];

            // Jika user memilih hapus thumbnail lama
            if ($deleteExistingThumb && !empty($thumbFileName)) {
                delete_uploaded_file($thumbFileName, UPLOAD_DIR_CREATIONS);
                $thumbFileName = '';
            }

            // Jika ada berkas thumbnail baru yang diunggah
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadRes = upload_file($_FILES['thumbnail'], 'creation', UPLOAD_DIR_CREATIONS);
                if ($uploadRes['success']) {
                    if (!empty($thumbFileName)) {
                        delete_uploaded_file($thumbFileName, UPLOAD_DIR_CREATIONS);
                    }
                    $thumbFileName = $uploadRes['filename'];
                } else {
                    $errorMessage = $uploadRes['error'];
                }
            }

            if (empty($errorMessage)) {
                if ($isEdit) {
                    $stmt = $pdo->prepare("
                        UPDATE creations SET 
                            platform = :platform,
                            title = :title,
                            thumbnail = :thumbnail,
                            post_link = :post_link,
                            created_date = :created_date,
                            sort_order = :sort_order,
                            is_published = :is_published
                        WHERE id = :id
                    ");
                    $stmt->execute([
                        ':platform'     => $platform,
                        ':title'        => $title,
                        ':thumbnail'    => $thumbFileName,
                        ':post_link'    => $postLink,
                        ':created_date' => $createdDate,
                        ':sort_order'   => $sortOrder,
                        ':is_published' => $isPublished,
                        ':id'           => $creationId
                    ]);
                    set_flash('success', 'Kreasi "' . e($title) . '" berhasil diperbarui.');
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO creations (platform, title, thumbnail, post_link, created_date, sort_order, is_published)
                        VALUES (:platform, :title, :thumbnail, :post_link, :created_date, :sort_order, :is_published)
                    ");
                    $stmt->execute([
                        ':platform'     => $platform,
                        ':title'        => $title,
                        ':thumbnail'    => $thumbFileName,
                        ':post_link'    => $postLink,
                        ':created_date' => $createdDate,
                        ':sort_order'   => $sortOrder,
                        ':is_published' => $isPublished
                    ]);
                    set_flash('success', 'Post kreasi baru berhasil ditambahkan.');
                }
                redirect(BASE_URL . '/admin/creations');
            }
        }
    }
}

$curPlatform = $_POST['platform'] ?? $creation['platform'];
$curTitle = $_POST['title'] ?? $creation['title'];
$curPostLink = $_POST['post_link'] ?? $creation['post_link'];
$curCreatedDate = $_POST['created_date'] ?? $creation['created_date'];
$curSortOrder = (int)($_POST['sort_order'] ?? $creation['sort_order']);
$curIsPublished = !isset($_POST['csrf_token']) ? (bool)$creation['is_published'] : isset($_POST['is_published']);

$currentThumb = $creation['thumbnail'];
$hasExistingThumb = !empty($currentThumb) && file_exists(UPLOAD_DIR_CREATIONS . '/' . basename($currentThumb));
$existingThumbUrl = $hasExistingThumb ? upload_url('creations', $currentThumb) : '';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<style>
  /* =========================================================
     CREATION STUDIO FORM STYLES
     ========================================================= */
  .studio-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.45fr) minmax(320px, 380px);
    gap: 2rem;
    align-items: start;
  }

  @media (max-width: 1100px) {
    .studio-layout {
      grid-template-columns: 1fr;
    }
    .studio-preview-col {
      order: -1;
    }
  }

  /* Header & Breadcrumb */
  .studio-top-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.75rem;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .studio-title-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    background: rgba(76, 141, 255, 0.12);
    border: 1px solid rgba(76, 141, 255, 0.3);
    color: #93c5fd;
    margin-bottom: 0.5rem;
  }

  /* Main Form Card */
  .studio-card {
    background: rgba(18, 24, 38, 0.85);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 1.75rem;
    margin-bottom: 1.75rem;
    box-shadow: 0 10px 35px -5px rgba(0, 0, 0, 0.45);
    position: relative;
    overflow: hidden;
  }

  .studio-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #4C8DFF, #a855f7, #ec4899);
    opacity: 0.8;
  }

  .studio-section-title {
    font-family: var(--font-display, 'Space Grotesk', sans-serif);
    font-size: 1.05rem;
    font-weight: 700;
    color: #f8fafc;
    margin-bottom: 0.35rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .studio-section-desc {
    font-size: 0.8rem;
    color: #94a3b8;
    margin-bottom: 1.35rem;
    line-height: 1.5;
  }

  /* Platform Selector Cards */
  .platform-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .platform-card {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 1rem 1.15rem;
    border-radius: 10px;
    background: rgba(15, 23, 42, 0.6);
    border: 1.5px solid rgba(255, 255, 255, 0.1);
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    user-select: none;
  }

  .platform-card:hover {
    background: rgba(30, 41, 59, 0.7);
    border-color: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
  }

  .platform-card.is-active-tiktok {
    background: linear-gradient(135deg, rgba(0, 242, 234, 0.12), rgba(255, 0, 79, 0.08));
    border-color: #00f2fe;
    box-shadow: 0 0 20px rgba(0, 242, 254, 0.25);
  }

  .platform-card.is-active-instagram {
    background: linear-gradient(135deg, rgba(236, 72, 153, 0.15), rgba(168, 85, 247, 0.12));
    border-color: #ec4899;
    box-shadow: 0 0 20px rgba(236, 72, 153, 0.25);
  }

  .platform-icon-wrap {
    width: 42px;
    height: 42px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: transform 0.25s ease;
  }

  .platform-card:hover .platform-icon-wrap {
    transform: scale(1.08);
  }

  .icon-tiktok {
    background: #000;
    border: 1px solid rgba(0, 242, 254, 0.4);
    color: #00f2fe;
  }

  .icon-instagram {
    background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
    color: #fff;
  }

  .platform-info {
    flex-grow: 1;
  }

  .platform-name {
    font-weight: 700;
    font-size: 0.95rem;
    color: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .platform-sub {
    font-size: 0.72rem;
    color: #94a3b8;
    margin-top: 0.2rem;
  }

  /* Form Elements Enhancements */
  .studio-form-group {
    margin-bottom: 1.35rem;
  }

  .studio-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    font-weight: 600;
    color: #cbd5e1;
    margin-bottom: 0.45rem;
    letter-spacing: 0.01em;
  }

  .studio-label .label-req {
    color: #f43f5e;
    margin-left: 0.25rem;
  }

  .studio-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
  }

  .studio-input-icon {
    position: absolute;
    left: 0.95rem;
    color: #64748b;
    pointer-events: none;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
  }

  .studio-input {
    width: 100%;
    background: rgba(15, 23, 42, 0.75);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 8px;
    padding: 0.75rem 1rem 0.75rem 2.65rem;
    color: #f8fafc;
    font-size: 0.88rem;
    font-family: inherit;
    transition: all 0.2s ease;
  }

  .studio-input:focus {
    background: rgba(15, 23, 42, 0.95);
    border-color: #4C8DFF;
    box-shadow: 0 0 0 3px rgba(76, 141, 255, 0.22);
    outline: none;
  }

  .studio-input::placeholder {
    color: #64748b;
  }

  .studio-help-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.4rem;
    font-size: 0.73rem;
    color: #94a3b8;
  }

  .btn-test-link {
    font-size: 0.72rem;
    color: #60a5fa;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.15rem 0.45rem;
    background: rgba(96, 165, 250, 0.1);
    border: 1px solid rgba(96, 165, 250, 0.2);
    border-radius: 4px;
    transition: all 0.2s ease;
  }

  .btn-test-link:hover {
    background: rgba(96, 165, 250, 0.2);
    color: #93c5fd;
  }

  /* Drag & Drop File Zone */
  .dropzone-container {
    border: 2px dashed rgba(255, 255, 255, 0.15);
    border-radius: 10px;
    padding: 1.5rem;
    text-align: center;
    background: rgba(15, 23, 42, 0.4);
    cursor: pointer;
    transition: all 0.25s ease;
    position: relative;
  }

  .dropzone-container:hover, .dropzone-container.is-dragover {
    border-color: #4C8DFF;
    background: rgba(76, 141, 255, 0.06);
    box-shadow: 0 0 20px rgba(76, 141, 255, 0.15);
  }

  .dropzone-icon {
    width: 46px;
    height: 46px;
    margin: 0 auto 0.75rem;
    border-radius: 50%;
    background: rgba(76, 141, 255, 0.12);
    border: 1px solid rgba(76, 141, 255, 0.3);
    color: #60a5fa;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    transition: transform 0.2s ease;
  }

  .dropzone-container:hover .dropzone-icon {
    transform: translateY(-2px);
  }

  .dropzone-title {
    font-size: 0.88rem;
    font-weight: 600;
    color: #f1f5f9;
    margin-bottom: 0.25rem;
  }

  .dropzone-sub {
    font-size: 0.73rem;
    color: #94a3b8;
  }

  /* File Info & Thumbnail Status */
  .file-badge-preview {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.85rem 1rem;
    background: rgba(15, 23, 42, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    margin-top: 1rem;
  }

  .file-badge-thumb {
    width: 50px;
    height: 70px;
    border-radius: 6px;
    object-fit: cover;
    border: 1px solid rgba(255, 255, 255, 0.15);
  }

  /* Switch Toggle */
  .switch-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    gap: 1rem;
  }

  .switch-label-group {
    display: flex;
    flex-direction: column;
  }

  .switch-title {
    font-size: 0.88rem;
    font-weight: 600;
    color: #f1f5f9;
  }

  .switch-desc {
    font-size: 0.74rem;
    color: #94a3b8;
    margin-top: 0.15rem;
  }

  .toggle-switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 26px;
    flex-shrink: 0;
  }

  .toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #334155;
    transition: .3s;
    border-radius: 34px;
    border: 1px solid rgba(255, 255, 255, 0.1);
  }

  .toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: #fff;
    transition: .3s;
    border-radius: 50%;
  }

  .toggle-switch input:checked + .toggle-slider {
    background-color: #10b981;
    box-shadow: 0 0 14px rgba(16, 185, 129, 0.4);
  }

  .toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(22px);
  }

  /* Form Actions */
  .studio-actions-bar {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
  }

  .btn-studio-primary {
    padding: 0.75rem 1.6rem;
    font-size: 0.92rem;
    font-weight: 700;
    border-radius: 8px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: #fff;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 18px rgba(37, 99, 235, 0.35);
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }

  .btn-studio-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 22px rgba(37, 99, 235, 0.5);
    background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%);
  }

  /* =========================================================
     LIVE 9:16 PHONE / CARD PREVIEW COLUMN
     ========================================================= */
  .studio-preview-col {
    position: sticky;
    top: 5.5rem;
  }

  .preview-wrapper-card {
    background: rgba(18, 24, 38, 0.85);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    padding: 1.5rem;
    box-shadow: 0 10px 35px -5px rgba(0, 0, 0, 0.45);
  }

  .preview-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
    padding-bottom: 0.85rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }

  .preview-header-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: #f8fafc;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .live-sync-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.7rem;
    color: #34d399;
    background: rgba(16, 185, 129, 0.15);
    padding: 0.2rem 0.5rem;
    border-radius: 9999px;
    font-weight: 600;
  }

  .live-sync-badge::before {
    content: '';
    width: 6px;
    height: 6px;
    background: #34d399;
    border-radius: 50%;
    animation: pulseSync 1.5s infinite;
  }

  @keyframes pulseSync {
    0% { transform: scale(0.9); opacity: 0.7; }
    50% { transform: scale(1.3); opacity: 1; box-shadow: 0 0 8px #34d399; }
    100% { transform: scale(0.9); opacity: 0.7; }
  }

  /* Live 9:16 Mock Card */
  .live-mock-card {
    position: relative;
    width: 100%;
    max-width: 280px;
    aspect-ratio: 9 / 15.5;
    margin: 0 auto;
    border-radius: 16px;
    overflow: hidden;
    background: #0f172a;
    border: 1px solid rgba(255, 255, 255, 0.12);
    box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.7), 0 0 25px rgba(76, 141, 255, 0.15);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .mock-bg-cover {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 1;
    transition: opacity 0.3s ease;
  }

  .mock-bg-fallback {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    color: #475569;
  }

  .mock-top-bar {
    position: relative;
    z-index: 3;
    padding: 0.85rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .mock-platform-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(8px);
  }

  .mock-badge-tiktok {
    background: rgba(0, 0, 0, 0.8);
    color: #00f2fe;
    border: 1px solid rgba(0, 242, 254, 0.4);
  }

  .mock-badge-instagram {
    background: linear-gradient(135deg, rgba(236, 72, 153, 0.85), rgba(168, 85, 247, 0.85));
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.3);
  }

  .mock-link-icon {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 0.75rem;
  }

  .mock-play-btn {
    position: relative;
    z-index: 3;
    width: 48px;
    height: 48px;
    margin: auto;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  }

  .mock-play-btn span {
    margin-left: 2px;
  }

  .mock-scrim {
    position: relative;
    z-index: 3;
    padding: 1.25rem 0.85rem 0.85rem;
    background: linear-gradient(to top, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.7) 65%, transparent 100%);
  }

  .mock-date {
    font-size: 0.68rem;
    color: #94a3b8;
    margin-bottom: 0.3rem;
  }

  .mock-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .preview-footnote {
    font-size: 0.72rem;
    color: #64748b;
    text-align: center;
    margin-top: 1rem;
    line-height: 1.4;
  }
</style>

<div class="studio-top-nav">
  <div>
    <div class="studio-title-badge">✨ Vertical Video Studio</div>
    <h1 style="font-family: var(--font-display, sans-serif); font-size: 1.5rem; font-weight: 700; color: #f8fafc; margin: 0;">
      <?= $isEdit ? 'Edit Kreasi Konten' : 'Tambah Kreasi Konten Baru' ?>
    </h1>
    <p style="font-size: 0.82rem; color: #94a3b8; margin: 0.35rem 0 0;">
      Form kurasi konten edukasi vertikal dengan sinkronisasi live preview format 9:16 untuk feed publik.
    </p>
  </div>
  <div>
    <a href="<?= BASE_URL ?>/admin/creations" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.6rem 1.1rem; font-size: 0.85rem; border-radius: 8px;">
      &larr; Kembali ke Daftar Kreasi
    </a>
  </div>
</div>

<?php if (!empty($errorMessage)): ?>
  <div class="flash-alert flash-danger" style="margin-bottom: 1.75rem; border-radius: 8px;">
    <div><strong>Peringatan:</strong> <?= e($errorMessage) ?></div>
  </div>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data" id="creationForm">
  <?= csrf_field() ?>

  <div class="studio-layout">
    
    <!-- LEFT COLUMN: Form Inputs -->
    <div class="studio-inputs-col">
      
      <!-- Section 1: Platform & Konten -->
      <div class="studio-card">
        <div class="studio-section-title">
          <span>🎬</span> Platform & Identitas Konten
        </div>
        <div class="studio-section-desc">
          Pilih kanal publikasi dan masukkan rincian topik konten video Anda.
        </div>

        <!-- Hidden input platform -->
        <input type="hidden" name="platform" id="inputPlatform" value="<?= e($curPlatform) ?>">

        <!-- Platform Interactive Cards -->
        <div class="platform-grid">
          <div class="platform-card <?= ($curPlatform === 'tiktok') ? 'is-active-tiktok' : '' ?>" id="cardPlatformTiktok" onclick="setPlatform('tiktok')">
            <div class="platform-icon-wrap icon-tiktok">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1.04-.1z"/>
              </svg>
            </div>
            <div class="platform-info">
              <div class="platform-name">
                <span>TikTok</span>
                <span style="font-size: 0.7rem; color: #00f2fe;">9:16</span>
              </div>
              <div class="platform-sub">Video Vertikal Edukasi</div>
            </div>
          </div>

          <div class="platform-card <?= ($curPlatform === 'instagram') ? 'is-active-instagram' : '' ?>" id="cardPlatformInstagram" onclick="setPlatform('instagram')">
            <div class="platform-icon-wrap icon-instagram">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
              </svg>
            </div>
            <div class="platform-info">
              <div class="platform-name">
                <span>Instagram</span>
                <span style="font-size: 0.7rem; color: #ec4899;">Reels</span>
              </div>
              <div class="platform-sub">Reels & Postingan Visual</div>
            </div>
          </div>
        </div>

        <!-- Judul Post -->
        <div class="studio-form-group">
          <label class="studio-label" for="inputTitle">
            <span>Judul / Topik Post <span class="label-req">*</span></span>
            <span style="font-size: 0.72rem; color: #64748b;" id="titleCharCount">0 karakter</span>
          </label>
          <div class="studio-input-wrap">
            <span class="studio-input-icon">🏷️</span>
            <input type="text" 
                   name="title" 
                   id="inputTitle" 
                   class="studio-input" 
                   required 
                   value="<?= e($curTitle) ?>" 
                   placeholder="Contoh: Tips Optimasi Query Database MySQL untuk Pemula" 
                   autocomplete="off">
          </div>
          <div class="studio-help-row">
            <span>Tulis judul singkat, padat, dan memikat untuk menarik pengunjung.</span>
          </div>
        </div>

        <!-- Tautan URL Post -->
        <div class="studio-form-group">
          <label class="studio-label" for="inputPostLink">
            <span>Tautan Post (URL) <span class="label-req">*</span></span>
            <a href="#" id="btnTestLink" class="btn-test-link" target="_blank" style="display: none;">
              Buka Link ↗
            </a>
          </label>
          <div class="studio-input-wrap">
            <span class="studio-input-icon">🔗</span>
            <input type="url" 
                   name="post_link" 
                   id="inputPostLink" 
                   class="studio-input" 
                   required 
                   value="<?= e($curPostLink) ?>" 
                   placeholder="https://www.tiktok.com/@... atau https://www.instagram.com/reel/..." 
                   autocomplete="off">
          </div>
          <div class="studio-help-row">
            <span>Mendukung link video TikTok atau reels/postingan Instagram.</span>
            <span id="autoDetectLabel" style="color: #38bdf8; font-weight: 600; display: none;">Otomatis Terdeteksi!</span>
          </div>
        </div>

        <!-- Tanggal / Waktu Post -->
        <div class="studio-form-group" style="margin-bottom: 0;">
          <label class="studio-label" for="inputDate">
            <span>Tanggal / Bulan Rilis</span>
            <span style="font-size: 0.72rem; color: #64748b;">Opsional</span>
          </label>
          <div class="studio-input-wrap">
            <span class="studio-input-icon">📅</span>
            <input type="text" 
                   name="created_date" 
                   id="inputDate" 
                   class="studio-input" 
                   value="<?= e($curCreatedDate) ?>" 
                   placeholder="Contoh: September 2024 atau 12 Des 2024">
          </div>
          <div class="studio-help-row">
            <span>Ditampilkan sebagai penanda waktu rilis konten di kartu feed.</span>
          </div>
        </div>

      </div>

      <!-- Section 2: Media & Thumbnail Cover -->
      <div class="studio-card">
        <div class="studio-section-title">
          <span>🖼️</span> Poster / Thumbnail Cover (9:16)
        </div>
        <div class="studio-section-desc">
          Gunakan screenshot terbaik atau poster grafis berasio vertikal 9:16 (format JPG, PNG, WebP maks. 2MB).
        </div>

        <!-- Hidden actual file input -->
        <input type="file" 
               name="thumbnail" 
               id="inputThumbnail" 
               accept="image/jpeg,image/png,image/webp" 
               style="display: none;">

        <!-- Dropzone Box -->
        <div class="dropzone-container" id="dropZone" onclick="document.getElementById('inputThumbnail').click();">
          <div class="dropzone-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="17 8 12 3 7 8"></polyline>
              <line x1="12" y1="3" x2="12" y2="15"></line>
            </svg>
          </div>
          <div class="dropzone-title">Klik atau seret file poster ke area ini</div>
          <div class="dropzone-sub">Rasio 9:16 portrait sangat disarankan untuk estetika terbaik</div>
        </div>

        <!-- Selected New File Preview Badge -->
        <div id="newFileBadge" class="file-badge-preview" style="display: none;">
          <img id="newFileThumbImg" class="file-badge-thumb" src="" alt="Thumbnail Baru">
          <div style="flex-grow: 1;">
            <div style="font-size: 0.75rem; color: #34d399; font-weight: 700; margin-bottom: 0.2rem;">✓ File Baru Terpilih (Siap Diunggah)</div>
            <div id="newFileName" style="font-size: 0.82rem; color: #f8fafc; font-family: monospace; word-break: break-all;"></div>
            <div id="newFileSize" style="font-size: 0.72rem; color: #94a3b8; margin-top: 0.2rem;"></div>
          </div>
          <button type="button" class="btn btn-secondary btn-sm" onclick="cancelNewFile(event)" style="padding: 0.35rem 0.65rem; font-size: 0.75rem;">
            Batalkan
          </button>
        </div>

        <!-- Existing Thumbnail Card (If editing) -->
        <?php if ($hasExistingThumb): ?>
          <div id="existingThumbCard" class="file-badge-preview" style="margin-top: 1rem;">
            <img src="<?= $existingThumbUrl ?>" class="file-badge-thumb" alt="Thumbnail Saat Ini">
            <div style="flex-grow: 1;">
              <div style="font-size: 0.74rem; color: #60a5fa; font-weight: 600;">Thumbnail Aktif Saat Ini</div>
              <div style="font-size: 0.8rem; color: #cbd5e1; font-family: monospace; margin-top: 0.2rem;">
                <?= e(basename($currentThumb)) ?>
              </div>
              <label style="display: inline-flex; align-items: center; gap: 0.45rem; font-size: 0.75rem; color: #f87171; margin-top: 0.4rem; cursor: pointer;">
                <input type="checkbox" name="delete_thumbnail" id="checkboxDeleteThumb" value="1" onchange="toggleDeleteThumb(this)"> 
                Hapus poster ini dan gunakan placeholder default
              </label>
            </div>
          </div>
        <?php endif; ?>

      </div>

      <!-- Section 3: Pengaturan Publikasi & Urutan -->
      <div class="studio-card">
        <div class="studio-section-title">
          <span>⚙️</span> Pengaturan Publikasi
        </div>
        <div class="studio-section-desc">
          Atur urutan penayangan dan visibilitas konten di feed publik.
        </div>

        <div style="display: grid; grid-template-columns: 140px 1fr; gap: 1.5rem; align-items: center;">
          
          <!-- Urutan Tampilan -->
          <div class="studio-form-group" style="margin-bottom: 0;">
            <label class="studio-label" for="inputSortOrder">
              <span>Urutan</span>
            </label>
            <div class="studio-input-wrap">
              <span class="studio-input-icon">🔢</span>
              <input type="number" 
                     name="sort_order" 
                     id="inputSortOrder" 
                     class="studio-input" 
                     value="<?= $curSortOrder ?>" 
                     min="0" 
                     max="999">
            </div>
            <div class="studio-help-row">
              <span>Mulai dari 0</span>
            </div>
          </div>

          <!-- Switch Publikasi -->
          <div class="switch-row">
            <div class="switch-label-group">
              <div class="switch-title" id="publishTitleText">
                <?= $curIsPublished ? '● Publikasi Aktif' : '○ Mode Draf (Tersembunyi)' ?>
              </div>
              <div class="switch-desc" id="publishDescText">
                <?= $curIsPublished ? 'Konten tampil di halaman Kreasi.' : 'Disimpan di admin saja, belum terlihat pengunjung.' ?>
              </div>
            </div>
            <label class="toggle-switch">
              <input type="checkbox" 
                     name="is_published" 
                     id="togglePublished" 
                     value="1" 
                     <?= $curIsPublished ? 'checked' : '' ?> 
                     onchange="updatePublishState(this)">
              <span class="toggle-slider"></span>
            </label>
          </div>

        </div>

      </div>

      <!-- Action Buttons -->
      <div class="studio-actions-bar">
        <button type="submit" class="btn-studio-primary">
          💾 <?= $isEdit ? 'Simpan Perubahan' : 'Publikasikan Kreasi Baru' ?>
        </button>
        <a href="<?= BASE_URL ?>/admin/creations" class="btn btn-secondary" style="padding: 0.75rem 1.4rem; font-size: 0.9rem; border-radius: 8px;">
          Batal
        </a>
      </div>

    </div>

    <!-- RIGHT COLUMN: Live 9:16 Vertical Video Preview -->
    <div class="studio-preview-col">
      <div class="preview-wrapper-card">
        
        <div class="preview-card-header">
          <div class="preview-header-title">
            <span>📱</span> Pratinjau Feed 9:16
          </div>
          <div class="live-sync-badge">
            Sinkron
          </div>
        </div>

        <!-- 9:16 Mockup Card Container -->
        <div class="live-mock-card" id="mockCard">
          
          <!-- Image Element (Shown if uploaded or existing) -->
          <img id="mockBgImg" 
               class="mock-bg-cover" 
               src="<?= !empty($existingThumbUrl) ? $existingThumbUrl : '' ?>" 
               alt="Preview" 
               style="<?= !empty($existingThumbUrl) ? 'display: block;' : 'display: none;' ?>">

          <!-- Fallback Graphic if no image -->
          <div id="mockBgFallback" class="mock-bg-fallback" style="<?= empty($existingThumbUrl) ? 'display: flex;' : 'display: none;' ?>">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.35;">
              <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
              <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
              <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
            </svg>
            <span style="font-size: 0.75rem; letter-spacing: 0.05em; font-weight: 600; opacity: 0.6;">POSTER 9:16</span>
          </div>

          <!-- Top Meta Bar -->
          <div class="mock-top-bar">
            <div id="mockPlatformBadge" class="mock-platform-badge <?= ($curPlatform === 'tiktok') ? 'mock-badge-tiktok' : 'mock-badge-instagram' ?>">
              <span id="mockBadgeIcon">
                <?= ($curPlatform === 'tiktok') ? '⚡ TikTok' : '📷 Instagram' ?>
              </span>
            </div>
            <div class="mock-link-icon">
              ↗
            </div>
          </div>

          <!-- Floating Play Button -->
          <div class="mock-play-btn">
            <span>▶</span>
          </div>

          <!-- Bottom Gradient Scrim -->
          <div class="mock-scrim">
            <div class="mock-date" id="mockDate">
              <?= !empty($curCreatedDate) ? e($curCreatedDate) : 'Baru Saja' ?>
            </div>
            <div class="mock-title" id="mockTitle">
              <?= !empty($curTitle) ? e($curTitle) : 'Judul kreasi konten akan muncul di sini...' ?>
            </div>
          </div>

        </div>

        <div class="preview-footnote">
          *Tampilan di atas adalah simulasi kartu vertikal yang akan dilihat langsung oleh pengunjung di halaman <code>/creations</code>.
        </div>

      </div>
    </div>

  </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const inputPlatform = document.getElementById('inputPlatform');
  const cardTiktok = document.getElementById('cardPlatformTiktok');
  const cardInstagram = document.getElementById('cardPlatformInstagram');
  
  const inputTitle = document.getElementById('inputTitle');
  const inputPostLink = document.getElementById('inputPostLink');
  const inputDate = document.getElementById('inputDate');
  const inputThumbnail = document.getElementById('inputThumbnail');
  
  const titleCharCount = document.getElementById('titleCharCount');
  const btnTestLink = document.getElementById('btnTestLink');
  const autoDetectLabel = document.getElementById('autoDetectLabel');

  // Preview elements
  const mockPlatformBadge = document.getElementById('mockPlatformBadge');
  const mockBadgeIcon = document.getElementById('mockBadgeIcon');
  const mockTitle = document.getElementById('mockTitle');
  const mockDate = document.getElementById('mockDate');
  const mockBgImg = document.getElementById('mockBgImg');
  const mockBgFallback = document.getElementById('mockBgFallback');

  // Dropzone
  const dropZone = document.getElementById('dropZone');
  const newFileBadge = document.getElementById('newFileBadge');
  const newFileThumbImg = document.getElementById('newFileThumbImg');
  const newFileName = document.getElementById('newFileName');
  const newFileSize = document.getElementById('newFileSize');

  // Platform switcher
  window.setPlatform = function(plat) {
    inputPlatform.value = plat;
    if (plat === 'tiktok') {
      cardTiktok.classList.add('is-active-tiktok');
      cardInstagram.classList.remove('is-active-instagram');
      mockPlatformBadge.className = 'mock-platform-badge mock-badge-tiktok';
      mockBadgeIcon.textContent = '⚡ TikTok';
    } else {
      cardInstagram.classList.add('is-active-instagram');
      cardTiktok.classList.remove('is-active-tiktok');
      mockPlatformBadge.className = 'mock-platform-badge mock-badge-instagram';
      mockBadgeIcon.textContent = '📷 Instagram';
    }
  };

  // Sync title
  function updateTitle() {
    const val = inputTitle.value.trim();
    titleCharCount.textContent = val.length + ' karakter';
    mockTitle.textContent = val ? val : 'Judul kreasi konten akan muncul di sini...';
  }
  inputTitle.addEventListener('input', updateTitle);
  updateTitle();

  // Sync date
  inputDate.addEventListener('input', () => {
    const val = inputDate.value.trim();
    mockDate.textContent = val ? val : 'Baru Saja';
  });

  // Sync post link & auto-detect platform
  function updateLink() {
    const val = inputPostLink.value.trim();
    if (val) {
      btnTestLink.style.display = 'inline-flex';
      btnTestLink.href = val;

      const lower = val.toLowerCase();
      if (lower.includes('tiktok.com') && inputPlatform.value !== 'tiktok') {
        setPlatform('tiktok');
        autoDetectLabel.textContent = 'Terdeteksi: TikTok!';
        autoDetectLabel.style.display = 'inline';
      } else if ((lower.includes('instagram.com') || lower.includes('instagr.am')) && inputPlatform.value !== 'instagram') {
        setPlatform('instagram');
        autoDetectLabel.textContent = 'Terdeteksi: Instagram Reels!';
        autoDetectLabel.style.display = 'inline';
      } else {
        autoDetectLabel.style.display = 'none';
      }
    } else {
      btnTestLink.style.display = 'none';
      btnTestLink.href = '#';
      autoDetectLabel.style.display = 'none';
    }
  }
  inputPostLink.addEventListener('input', updateLink);
  updateLink();

  // Handle Drag and Drop
  ['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, (e) => {
      e.preventDefault();
      e.stopPropagation();
      dropZone.classList.add('is-dragover');
    }, false);
  });

  ['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, (e) => {
      e.preventDefault();
      e.stopPropagation();
      dropZone.classList.remove('is-dragover');
    }, false);
  });

  dropZone.addEventListener('drop', (e) => {
    const dt = e.dataTransfer;
    const files = dt.files;
    if (files && files.length > 0) {
      inputThumbnail.files = files;
      handleFileSelected(files[0]);
    }
  });

  inputThumbnail.addEventListener('change', (e) => {
    if (e.target.files && e.target.files[0]) {
      handleFileSelected(e.target.files[0]);
    }
  });

  function handleFileSelected(file) {
    if (!file.type.startsWith('image/')) {
      alert('Mohon pilih berkas gambar (JPG, PNG, atau WebP).');
      return;
    }

    const reader = new FileReader();
    reader.onload = function(evt) {
      const src = evt.target.result;
      
      // Update new file badge
      newFileBadge.style.display = 'flex';
      newFileThumbImg.src = src;
      newFileName.textContent = file.name;
      newFileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';

      // Update mock card preview
      mockBgImg.src = src;
      mockBgImg.style.display = 'block';
      mockBgFallback.style.display = 'none';
    };
    reader.readAsDataURL(file);
  }

  window.cancelNewFile = function(e) {
    e.stopPropagation();
    inputThumbnail.value = '';
    newFileBadge.style.display = 'none';
    
    // Restore existing or fallback
    const existingCard = document.getElementById('existingThumbCard');
    const deleteCheck = document.getElementById('checkboxDeleteThumb');
    if (existingCard && (!deleteCheck || !deleteCheck.checked)) {
      const existingImg = existingCard.querySelector('img');
      if (existingImg && existingImg.src) {
        mockBgImg.src = existingImg.src;
        mockBgImg.style.display = 'block';
        mockBgFallback.style.display = 'none';
        return;
      }
    }
    mockBgImg.src = '';
    mockBgImg.style.display = 'none';
    mockBgFallback.style.display = 'flex';
  };

  window.toggleDeleteThumb = function(checkbox) {
    if (checkbox.checked) {
      if (newFileBadge.style.display !== 'flex') {
        mockBgImg.src = '';
        mockBgImg.style.display = 'none';
        mockBgFallback.style.display = 'flex';
      }
    } else {
      if (newFileBadge.style.display !== 'flex') {
        const existingCard = document.getElementById('existingThumbCard');
        const existingImg = existingCard ? existingCard.querySelector('img') : null;
        if (existingImg && existingImg.src) {
          mockBgImg.src = existingImg.src;
          mockBgImg.style.display = 'block';
          mockBgFallback.style.display = 'none';
        }
      }
    }
  };

  window.updatePublishState = function(chk) {
    const titleText = document.getElementById('publishTitleText');
    const descText = document.getElementById('publishDescText');
    if (chk.checked) {
      titleText.textContent = '● Publikasi Aktif';
      descText.textContent = 'Konten tampil di halaman Kreasi.';
    } else {
      titleText.textContent = '○ Mode Draf (Tersembunyi)';
      descText.textContent = 'Disimpan di admin saja, belum terlihat pengunjung.';
    }
  };
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
