<?php
/**
 * Kelola Tautan (Links): List & Hapus
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Kelola Tautan Penting (Links)';

// Handle Hapus Tautan (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        set_flash('danger', 'Token CSRF tidak valid. Silakan coba kembali.');
        redirect(BASE_URL . '/admin/links.php');
    }

    $linkId = (int)($_POST['link_id'] ?? 0);
    if ($linkId > 0) {
        $delStmt = $pdo->prepare("DELETE FROM links WHERE id = :id");
        $delStmt->execute([':id' => $linkId]);

        set_flash('success', 'Tautan berhasil dihapus.');
    }
    redirect(BASE_URL . '/admin/links.php');
}

// Ambil seluruh tautan
$links = get_all_links($pdo);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-card">
  <div class="admin-card-header">
    <div>
      <div class="admin-card-title">Daftar Seluruh Tautan</div>
      <p style="font-size: var(--text-xs); color: var(--color-cream-muted); margin-top: 0.25rem;">
        Kelola tombol tautan yang tampil di halaman Bio Links (<code>/pages/links.php</code>), seperti CV, portofolio, dan kontak.
      </p>
    </div>
    <div>
      <a href="<?= BASE_URL ?>/admin/link_form.php" class="btn btn-primary btn-sm">
        + Tambah Tautan Baru
      </a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 70px;">Urutan</th>
          <th style="width: 60px;">Ikon</th>
          <th>Label Tautan</th>
          <th>URL Tujuan</th>
          <th style="width: 100px;">Status</th>
          <th style="text-align: right; width: 140px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($links)): ?>
          <tr>
            <td colspan="6" style="text-align: center; padding: 3rem 1rem;">
              <p style="color: var(--color-cream-muted); margin-bottom: 1rem;">Belum ada tautan yang dibuat.</p>
              <a href="<?= BASE_URL ?>/admin/link_form.php" class="btn btn-primary btn-sm">Tambah Tautan Pertama</a>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($links as $l): ?>
            <tr>
              <td style="color: var(--color-accent-hover); font-weight: 600;">
                #<?= (int)$l['sort_order'] ?>
              </td>

              <td>
                <span style="font-size: 1.25rem; display: inline-block;">
                  <?php
                  $iconEmojiMap = [
                      'cv'        => '📄',
                      'email'     => '✉️',
                      'linkedin'  => '💼',
                      'github'    => '🐙',
                      'link'      => '🔗',
                      'instagram' => '📸',
                      'tiktok'    => '🎵',
                      'globe'     => '🌐',
                      'code'      => '💻'
                  ];
                  echo $iconEmojiMap[$l['icon']] ?? '🔗';
                  ?>
                </span>
              </td>

              <td>
                <div style="font-weight: 600; color: var(--color-cream); font-size: var(--text-sm);">
                  <?= e($l['label']) ?>
                </div>
                <div style="font-size: 0.72rem; color: var(--color-cream-faint);">
                  Kata kunci ikon: <code><?= e($l['icon']) ?></code>
                </div>
              </td>

              <td style="max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                <a href="<?= e($l['url']) ?>" target="_blank" rel="noopener noreferrer" style="font-size: var(--text-xs); color: var(--color-accent);">
                  <?= e($l['url']) ?> &nearr;
                </a>
              </td>

              <td>
                <?php if ($l['is_active']): ?>
                  <span class="badge-status badge-published">Aktif</span>
                <?php else: ?>
                  <span class="badge-status badge-draft">Nonaktif</span>
                <?php endif; ?>
              </td>

              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.4rem;">
                  <a href="<?= BASE_URL ?>/admin/link_form.php?id=<?= (int)$l['id'] ?>" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.65rem;">
                    Edit
                  </a>

                  <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tautan <?= e(addslashes($l['label'])) ?>?');" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="link_id" value="<?= (int)$l['id'] ?>">
                    <button type="submit" class="btn btn-sm" style="padding: 0.35rem 0.65rem; background-color: rgba(185, 28, 28, 0.2); border-color: rgba(239, 68, 68, 0.4); color: #FCA5A5;">
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
