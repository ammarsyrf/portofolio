<?php
/**
 * Moderasi Buku Tamu (Guestbook): List & Hapus Pesan
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Moderasi Buku Tamu (Guestbook)';

// Handle Hapus Pesan (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        set_flash('danger', 'Token CSRF tidak valid. Silakan coba kembali.');
        redirect(BASE_URL . '/admin/guestbook.php');
    }

    $messageId = (int)($_POST['message_id'] ?? 0);
    if ($messageId > 0) {
        $delStmt = $pdo->prepare("DELETE FROM guestbook WHERE id = :id");
        $delStmt->execute([':id' => $messageId]);

        set_flash('success', 'Pesan buku tamu berhasil dihapus.');
    }
    redirect(BASE_URL . '/admin/guestbook.php');
}

// Ambil pesan terbaru
$messages = get_guestbook_messages($pdo, 250);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="admin-card">
  <div class="admin-card-header">
    <div>
      <div class="admin-card-title">Daftar Pesan Masuk Buku Tamu</div>
      <p style="font-size: var(--text-xs); color: var(--color-cream-muted); margin-top: 0.25rem;">
        Seluruh pesan yang dikirim oleh pengunjung terautentikasi Google. Anda dapat meninjau dan menghapus pesan spam atau tidak pantas.
      </p>
    </div>
    <div style="font-size: var(--text-xs); color: var(--color-accent-hover); font-weight: 600;">
      Total: <?= count($messages) ?> Pesan
    </div>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 50px;">Avatar</th>
          <th style="width: 200px;">Pengirim Google</th>
          <th>Isi Pesan</th>
          <th style="width: 150px;">Waktu Kirim</th>
          <th style="text-align: right; width: 100px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($messages)): ?>
          <tr>
            <td colspan="5" style="text-align: center; padding: 3rem 1rem;">
              <p style="color: var(--color-cream-muted); margin-bottom: 0.5rem;">Belum ada pesan yang masuk di buku tamu.</p>
              <a href="<?= BASE_URL ?>/guestbook" target="_blank" class="btn btn-secondary btn-sm">Lihat Halaman Buku Tamu Publik</a>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($messages as $m): 
            $initial = !empty($m['user_name']) ? mb_strtoupper(mb_substr($m['user_name'], 0, 1)) : '?';
            $timeStr = date('d M Y, H:i', strtotime($m['created_at']));
          ?>
            <tr>
              <td>
                <?php if (!empty($m['user_avatar'])): ?>
                  <img src="<?= e($m['user_avatar']) ?>" alt="<?= e($m['user_name']) ?>" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1px solid var(--color-line);" referrerpolicy="no-referrer">
                <?php else: ?>
                  <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--color-accent); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">
                    <?= e($initial) ?>
                  </div>
                <?php endif; ?>
              </td>

              <td>
                <div style="font-weight: 600; color: var(--color-cream); font-size: var(--text-sm);">
                  <?= e($m['user_name']) ?>
                </div>
                <?php if (!empty($m['user_email'])): ?>
                  <div style="font-size: 0.72rem; color: var(--color-cream-faint);">
                    <?= e($m['user_email']) ?>
                  </div>
                <?php endif; ?>
                <div style="font-size: 0.65rem; color: var(--color-cream-faint); font-family: monospace;">
                  ID: <?= e(substr($m['google_id'], 0, 12)) ?>...
                </div>
              </td>

              <td>
                <div style="font-size: var(--text-sm); color: var(--color-cream); line-height: 1.5; word-break: break-word;">
                  <?= nl2br(e($m['message'])) ?>
                </div>
              </td>

              <td style="font-size: var(--text-xs); color: var(--color-cream-muted); white-space: nowrap;">
                <?= e($timeStr) ?>
              </td>

              <td style="text-align: right;">
                <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan dari <?= e(addslashes($m['user_name'])) ?>?');" style="display: inline;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="message_id" value="<?= (int)$m['id'] ?>">
                  <button type="submit" class="btn btn-sm" style="padding: 0.35rem 0.65rem; background-color: rgba(185, 28, 28, 0.2); border-color: rgba(239, 68, 68, 0.4); color: #FCA5A5;">
                    Hapus
                  </button>
                </form>
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
