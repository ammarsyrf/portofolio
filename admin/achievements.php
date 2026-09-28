<?php
/**
 * Kelola Pencapaian & Sertifikat: List & Hapus
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Kelola Pencapaian & Sertifikat';

// Handle Hapus Pencapaian (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        set_flash('danger', 'Token CSRF tidak valid. Silakan coba kembali.');
        redirect(BASE_URL . '/admin/achievements');
    }

    $achievementId = (int)($_POST['achievement_id'] ?? 0);
    if ($achievementId > 0) {
        $achievement = get_achievement_by_id($pdo, $achievementId);
        if ($achievement) {
            // Hapus file sertifikat jika ada
            if (!empty($achievement['image'])) {
                delete_uploaded_file($achievement['image'], UPLOAD_DIR_ACHIEVEMENTS);
            }

            $delStmt = $pdo->prepare("DELETE FROM achievements WHERE id = :id");
            $delStmt->execute([':id' => $achievementId]);

            set_flash('success', 'Pencapaian "' . e($achievement['title']) . '" berhasil dihapus.');
        } else {
            set_flash('danger', 'Pencapaian tidak ditemukan.');
        }
    }
    redirect(BASE_URL . '/admin/achievements');
}

// Ambil seluruh pencapaian
$achievements = get_all_achievements($pdo);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-card">
  <div class="admin-card-header">
    <div>
      <div class="admin-card-title">Daftar Sertifikat & Penghargaan</div>
      <p style="font-size: var(--text-xs); color: var(--color-cream-muted); margin-top: 0.25rem;">
        Kelola sertifikasi profesional, penghargaan, dan bukti kredensial untuk ditampilkan di halaman Pencapaian.
      </p>
    </div>
    <div>
      <a href="<?= BASE_URL ?>/admin/achievement-form" class="btn btn-primary btn-sm">
        + Tambah Pencapaian Baru
      </a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 50px;">Berkas</th>
          <th style="width: 70px;">Urutan</th>
          <th>Judul Sertifikat & Penerbit</th>
          <th>Tanggal</th>
          <th>Kredensial</th>
          <th style="width: 100px;">Status</th>
          <th style="text-align: right; width: 140px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($achievements)): ?>
          <tr>
            <td colspan="7" style="text-align: center; padding: 3rem 1rem;">
              <p style="color: var(--color-cream-muted); margin-bottom: 1rem;">Belum ada sertifikat atau pencapaian yang tersimpan.</p>
              <a href="<?= BASE_URL ?>/admin/achievement-form" class="btn btn-primary btn-sm">Tambah Pencapaian Pertama</a>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($achievements as $ach): 
            $hasImg = !empty($ach['image']) && file_exists(UPLOAD_DIR_ACHIEVEMENTS . '/' . basename($ach['image']));
          ?>
            <tr>
              <td>
                <?php if ($hasImg): ?>
                  <img src="<?= upload_url('achievements', $ach['image']) ?>" alt="<?= e($ach['title']) ?>" style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid var(--adm-border); box-shadow: 0 4px 12px rgba(0,0,0,0.35);">
                <?php else: ?>
                  <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.04); border: 1px solid var(--adm-border); display: flex; align-items: center; justify-content: center; font-size: 0.68rem; color: var(--adm-text-muted); border-radius: 8px;">
                    No Img
                  </div>
                <?php endif; ?>
              </td>

              <td style="color: var(--adm-accent-bright); font-family: var(--font-mono); font-weight: 700;">
                #<?= (int)$ach['sort_order'] ?>
              </td>

              <td>
                <div style="font-weight: 600; color: #ffffff; font-size: 0.88rem;">
                  <?= e($ach['title']) ?>
                </div>
                <div style="font-size: 0.76rem; color: var(--adm-text-muted);">
                  Penerbit: <strong style="color: #cbd5e1;"><?= e($ach['issuer']) ?></strong>
                </div>
              </td>

              <td style="font-size: 0.8rem; color: var(--adm-text-secondary); font-family: var(--font-mono);">
                <?= e($ach['issue_date'] ?: '-') ?>
              </td>

              <td>
                <?php if (!empty($ach['verify_link'])): ?>
                  <a href="<?= e($ach['verify_link']) ?>" target="_blank" rel="noopener noreferrer" style="font-size: 0.78rem; font-weight: 600; color: #38bdf8; display: inline-flex; align-items: center; gap: 0.25rem;">
                    Buka Kredensial &nearr;
                  </a>
                <?php else: ?>
                  <span style="font-size: 0.78rem; color: var(--adm-text-muted);">-</span>
                <?php endif; ?>
              </td>

              <td>
                <?php if ($ach['is_published']): ?>
                  <span class="badge-status badge-published">Published</span>
                <?php else: ?>
                  <span class="badge-status badge-draft">Draft</span>
                <?php endif; ?>
              </td>

              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.45rem;">
                  <a href="<?= BASE_URL ?>/admin/achievement-form?id=<?= (int)$ach['id'] ?>" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.65rem;">
                    Edit
                  </a>

                  <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sertifikat <?= e(addslashes($ach['title'])) ?>? Tindakan ini tidak dapat dibatalkan.');" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="achievement_id" value="<?= (int)$ach['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.35rem 0.65rem;">
                      Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
