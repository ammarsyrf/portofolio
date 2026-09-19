<?php
/**
 * Form Tambah & Edit Pencapaian / Sertifikat
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$achievementId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = ($achievementId > 0);

$achievement = null;
if ($isEdit) {
    $achievement = get_achievement_by_id($pdo, $achievementId);
    if (!$achievement) {
        set_flash('danger', 'Pencapaian tidak ditemukan.');
        redirect(BASE_URL . '/admin/achievements.php');
    }
    $pageTitle = 'Edit Pencapaian: ' . $achievement['title'];
} else {
    $pageTitle = 'Tambah Pencapaian Baru';
    $achievement = [
        'id' => 0,
        'title' => '',
        'issuer' => '',
        'issue_date' => '',
        'description' => '',
        'image' => '',
        'verify_link' => '',
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
        $title = trim($_POST['title'] ?? '');
        $issuer = trim($_POST['issuer'] ?? '');
        $issueDate = trim($_POST['issue_date'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $verifyLink = trim($_POST['verify_link'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isPublished = isset($_POST['is_published']) ? 1 : 0;
        $deleteExistingImage = isset($_POST['delete_image']) && $_POST['delete_image'] === '1';

        if (empty($title)) {
            $errorMessage = 'Judul sertifikat / pencapaian wajib diisi.';
        } elseif (empty($issuer)) {
            $errorMessage = 'Nama penerbit / penyelenggara wajib diisi.';
        } else {
            $imageFileName = $achievement['image'];

            // Jika user memilih untuk menghapus berkas gambar yang ada
            if ($deleteExistingImage && !empty($imageFileName)) {
                delete_uploaded_file($imageFileName, UPLOAD_DIR_ACHIEVEMENTS);
                $imageFileName = '';
            }

            // Jika ada berkas sertifikat baru yang diunggah
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadRes = upload_file($_FILES['image'], 'achievement', UPLOAD_DIR_ACHIEVEMENTS);
                if ($uploadRes['success']) {
                    if (!empty($imageFileName)) {
                        delete_uploaded_file($imageFileName, UPLOAD_DIR_ACHIEVEMENTS);
                    }
                    $imageFileName = $uploadRes['filename'];
                } else {
                    $errorMessage = $uploadRes['error'];
                }
            }

            if (empty($errorMessage)) {
                if ($isEdit) {
                    $stmt = $pdo->prepare("
                        UPDATE achievements SET 
                            title = :title,
                            issuer = :issuer,
                            issue_date = :issue_date,
                            description = :description,
                            image = :image,
                            verify_link = :verify_link,
                            sort_order = :sort_order,
                            is_published = :is_published
                        WHERE id = :id
                    ");
                    $stmt->execute([
                        ':title'        => $title,
                        ':issuer'       => $issuer,
                        ':issue_date'   => $issueDate,
                        ':description'  => $description,
                        ':image'        => $imageFileName,
                        ':verify_link'  => $verifyLink,
                        ':sort_order'   => $sortOrder,
                        ':is_published' => $isPublished,
                        ':id'           => $achievementId
                    ]);
                    set_flash('success', 'Pencapaian "' . e($title) . '" berhasil diperbarui.');
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO achievements (title, issuer, issue_date, description, image, verify_link, sort_order, is_published)
                        VALUES (:title, :issuer, :issue_date, :description, :image, :verify_link, :sort_order, :is_published)
                    ");
                    $stmt->execute([
                        ':title'        => $title,
                        ':issuer'       => $issuer,
                        ':issue_date'   => $issueDate,
                        ':description'  => $description,
                        ':image'        => $imageFileName,
                        ':verify_link'  => $verifyLink,
                        ':sort_order'   => $sortOrder,
                        ':is_published' => $isPublished
                    ]);
                    set_flash('success', 'Pencapaian baru berhasil ditambahkan.');
                }
                redirect(BASE_URL . '/admin/achievements.php');
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
      <div class="admin-card-title"><?= $isEdit ? 'Edit Pencapaian / Sertifikat' : 'Tambah Pencapaian Baru' ?></div>
      <p style="font-size: var(--text-xs); color: var(--color-cream-muted); margin-top: 0.25rem;">
        Isi detail sertifikasi, penghargaan, atau kredensial yang relevan.
      </p>
    </div>
    <div>
      <a href="<?= BASE_URL ?>/admin/achievements.php" class="btn btn-secondary btn-sm">&larr; Kembali</a>
    </div>
  </div>

  <?php if (!empty($errorMessage)): ?>
    <div class="flash-alert flash-danger" style="margin-bottom: 1.5rem;">
      <div><?= e($errorMessage) ?></div>
    </div>
  <?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="form-group">
      <label class="form-label">Judul Pencapaian / Sertifikasi *</label>
      <input type="text" name="title" class="form-control" required value="<?= e($_POST['title'] ?? $achievement['title']) ?>" placeholder="Mis. Sertifikasi Data Science Associate / Juara 1 Hackathon">
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Penerbit / Penyelenggara *</label>
        <input type="text" name="issuer" class="form-control" required value="<?= e($_POST['issuer'] ?? $achievement['issuer']) ?>" placeholder="Mis. BNSP / Coursera / Dicoding">
      </div>

      <div class="form-group">
        <label class="form-label">Tanggal / Periode Perolehan</label>
        <input type="text" name="issue_date" class="form-control" value="<?= e($_POST['issue_date'] ?? $achievement['issue_date']) ?>" placeholder="Mis. September 2024 atau 2024">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Deskripsi Singkat / Keterangan</label>
      <textarea name="description" class="form-control" rows="3" placeholder="Ringkasan singkat materi sertifikasi, keahlian yang diuji, atau pencapaian yang diraih..."><?= e($_POST['description'] ?? $achievement['description']) ?></textarea>
    </div>

    <div class="form-group">
      <label class="form-label">Tautan Verifikasi Kredensial (URL)</label>
      <input type="url" name="verify_link" class="form-control" value="<?= e($_POST['verify_link'] ?? $achievement['verify_link']) ?>" placeholder="https://coursera.org/verify/... atau tautan verifikasi online">
      <span class="form-help">Jika ada, sertakan tautan resmi untuk memverifikasi keaslian sertifikat ini.</span>
    </div>

    <!-- Upload Gambar Sertifikat -->
    <div class="form-group" style="padding: 1.25rem; background-color: var(--color-bg-surface); border: 1px solid var(--color-line); border-radius: var(--radius-md);">
      <label class="form-label" style="margin-bottom: 0.5rem;">Gambar / Scan Sertifikat</label>
      <span class="form-help" style="margin-bottom: 0.75rem;">Format: JPG, PNG, atau WebP. Maksimal 2 MB.</span>

      <?php 
      $currentImg = $achievement['image'];
      $hasExistingImg = !empty($currentImg) && file_exists(UPLOAD_DIR_ACHIEVEMENTS . '/' . basename($currentImg));
      if ($hasExistingImg): ?>
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; padding: 0.75rem; background: rgba(0,0,0,0.3); border-radius: var(--radius-sm); border: 1px solid var(--color-line);">
          <img src="<?= upload_url('achievements', $currentImg) ?>" alt="Sertifikat" style="max-height: 80px; max-width: 140px; object-fit: contain; border-radius: 4px; border: 1px solid var(--color-line);">
          <div>
            <div style="font-size: var(--text-xs); color: var(--color-cream); font-family: monospace;"><?= e(basename($currentImg)) ?></div>
            <label style="display: flex; align-items: center; gap: 0.4rem; font-size: var(--text-xs); color: #FCA5A5; margin-top: 0.35rem; cursor: pointer;">
              <input type="checkbox" name="delete_image" value="1"> Hapus berkas sertifikat ini
            </label>
          </div>
        </div>
      <?php endif; ?>

      <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
      <span class="form-help"><?= $hasExistingImg ? 'Unggah file baru jika ingin mengganti berkas saat ini.' : 'Pilih file scan atau mockup sertifikat.' ?></span>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Urutan Tampilan</label>
        <input type="number" name="sort_order" class="form-control" value="<?= (int)($_POST['sort_order'] ?? $achievement['sort_order']) ?>" style="max-width: 120px;">
        <span class="form-help">Angka lebih kecil tampil lebih dulu (0, 1, 2...).</span>
      </div>

      <div class="form-group" style="display: flex; flex-direction: column; justify-content: center;">
        <label class="form-label">Status Publikasi</label>
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-top: 0.5rem;">
          <input type="checkbox" name="is_published" value="1" <?= (!isset($_POST['title']) ? ($achievement['is_published'] ? 'checked' : '') : (isset($_POST['is_published']) ? 'checked' : '')) ?>>
          <span style="font-size: var(--text-sm); color: var(--color-cream);">Tampilkan di halaman Pencapaian publik</span>
        </label>
      </div>
    </div>

    <div style="display: flex; gap: 1rem; margin-top: 2rem; border-top: 1px solid var(--color-line); padding-top: 1.5rem;">
      <button type="submit" class="btn btn-primary">
        <?= $isEdit ? 'Simpan Perubahan' : 'Tambahkan Pencapaian' ?>
      </button>
      <a href="<?= BASE_URL ?>/admin/achievements.php" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
