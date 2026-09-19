<?php
/**
 * Form Tambah & Edit Kreasi Konten (TikTok & Instagram)
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
        redirect(BASE_URL . '/admin/creations.php');
    }
    $pageTitle = 'Edit Kreasi Konten: ' . $creation['title'];
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
                redirect(BASE_URL . '/admin/creations.php');
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-card" style="max-width: 800px; margin: 0 auto;">
  <div class="admin-card-header">
    <div>
      <div class="admin-card-title"><?= $isEdit ? 'Edit Kreasi Konten' : 'Tambah Kreasi Baru' ?></div>
      <p style="font-size: var(--text-xs); color: var(--color-cream-muted); margin-top: 0.25rem;">
        Kurasi manual postingan edukasi teknologi dari TikTok atau Instagram.
      </p>
    </div>
    <div>
      <a href="<?= BASE_URL ?>/admin/creations.php" class="btn btn-secondary btn-sm">&larr; Kembali</a>
    </div>
  </div>

  <?php if (!empty($errorMessage)): ?>
    <div class="flash-alert flash-danger" style="margin-bottom: 1.5rem;">
      <div><?= e($errorMessage) ?></div>
    </div>
  <?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Platform Konten *</label>
        <?php $curPlatform = $_POST['platform'] ?? $creation['platform']; ?>
        <select name="platform" class="form-control" required>
          <option value="tiktok" <?= ($curPlatform === 'tiktok') ? 'selected' : '' ?>>TikTok</option>
          <option value="instagram" <?= ($curPlatform === 'instagram') ? 'selected' : '' ?>>Instagram</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Tanggal / Bulan Post</label>
        <input type="text" name="created_date" class="form-control" value="<?= e($_POST['created_date'] ?? $creation['created_date']) ?>" placeholder="Mis. Januari 2025">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Judul / Topik Post *</label>
      <input type="text" name="title" class="form-control" required value="<?= e($_POST['title'] ?? $creation['title']) ?>" placeholder="Mis. Tips Optimasi Query Database MySQL untuk Pemula">
    </div>

    <div class="form-group">
      <label class="form-label">Tautan Post (URL) *</label>
      <input type="url" name="post_link" class="form-control" required value="<?= e($_POST['post_link'] ?? $creation['post_link']) ?>" placeholder="https://www.tiktok.com/@... atau https://instagram.com/p/...">
      <span class="form-help">Link langsung menuju postingan video TikTok atau reels/carousel Instagram.</span>
    </div>

    <!-- Upload Thumbnail -->
    <div class="form-group" style="padding: 1.25rem; background-color: var(--color-bg-surface); border: 1px solid var(--color-line); border-radius: var(--radius-md);">
      <label class="form-label" style="margin-bottom: 0.5rem;">Gambar Thumbnail Cover</label>
      <span class="form-help" style="margin-bottom: 0.75rem;">Format: JPG, PNG, atau WebP (Rasio 16:9 atau 9:16 cocok). Maksimal 2 MB.</span>

      <?php 
      $currentThumb = $creation['thumbnail'];
      $hasExistingThumb = !empty($currentThumb) && file_exists(UPLOAD_DIR_CREATIONS . '/' . basename($currentThumb));
      if ($hasExistingThumb): ?>
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; padding: 0.75rem; background: rgba(0,0,0,0.3); border-radius: var(--radius-sm); border: 1px solid var(--color-line);">
          <img src="<?= upload_url('creations', $currentThumb) ?>" alt="Thumbnail" style="max-height: 80px; max-width: 120px; object-fit: cover; border-radius: 4px; border: 1px solid var(--color-line);">
          <div>
            <div style="font-size: var(--text-xs); color: var(--color-cream); font-family: monospace;"><?= e(basename($currentThumb)) ?></div>
            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: var(--text-xs); color: #FCA5A5; margin-top: 0.35rem; cursor: pointer;">
              <input type="checkbox" name="delete_thumbnail" value="1"> Hapus thumbnail ini
            </label>
          </div>
        </div>
      <?php endif; ?>

      <input type="file" name="thumbnail" class="form-control" accept="image/jpeg,image/png,image/webp">
      <span class="form-help"><?= $hasExistingThumb ? 'Unggah file baru jika ingin mengganti thumbnail saat ini.' : 'Pilih screenshot atau poster preview konten.' ?></span>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Urutan Tampilan</label>
        <input type="number" name="sort_order" class="form-control" value="<?= (int)($_POST['sort_order'] ?? $creation['sort_order']) ?>" style="max-width: 120px;">
        <span class="form-help">Urutan kecil tampil lebih awal (0, 1, 2...).</span>
      </div>

      <div class="form-group" style="display: flex; flex-direction: column; justify-content: center;">
        <label class="form-label">Status Publikasi</label>
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-top: 0.5rem;">
          <input type="checkbox" name="is_published" value="1" <?= (!isset($_POST['title']) ? ($creation['is_published'] ? 'checked' : '') : (isset($_POST['is_published']) ? 'checked' : '')) ?>>
          <span style="font-size: var(--text-sm); color: var(--color-cream);">Publikasikan di halaman Kreasi</span>
        </label>
      </div>
    </div>

    <div style="display: flex; gap: 1rem; margin-top: 2rem; border-top: 1px solid var(--color-line); padding-top: 1.5rem;">
      <button type="submit" class="btn btn-primary">
        <?= $isEdit ? 'Simpan Perubahan' : 'Tambahkan Kreasi' ?>
      </button>
      <a href="<?= BASE_URL ?>/admin/creations.php" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
