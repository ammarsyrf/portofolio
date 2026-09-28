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
        redirect(BASE_URL . '/admin/guestbook');
    }

    $messageId = (int)($_POST['message_id'] ?? 0);
    if ($messageId > 0) {
        $delStmt = $pdo->prepare("DELETE FROM guestbook WHERE id = :id");
        $delStmt->execute([':id' => $messageId]);

        set_flash('success', 'Pesan buku tamu berhasil dihapus.');
    }
    redirect(BASE_URL . '/admin/guestbook');
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
                  <img src="<?= e($m['user_avatar']) ?>" alt="<?= e($m['user_name']) ?>" style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid var(--adm-border); box-shadow: 0 4px 12px rgba(0,0,0,0.35);" referrerpolicy="no-referrer">
                <?php else: ?>
                  <div style="width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);">
                    <?= e($initial) ?>
                  </div>
                <?php endif; ?>
              </td>

              <td>
                <div style="font-weight: 600; color: #ffffff; font-size: 0.88rem;">
                  <?= e($m['user_name']) ?>
                </div>
                <?php if (!empty($m['user_email'])): ?>
                  <div style="font-size: 0.74rem; color: #38bdf8;">
                    <?= e($m['user_email']) ?>
                  </div>
                <?php endif; ?>
                <div style="font-size: 0.68rem; color: var(--adm-text-muted); font-family: var(--font-mono);">
                  ID: <?= e(substr($m['google_id'], 0, 14)) ?>...
                </div>
              </td>

              <td>
                <div style="font-size: 0.85rem; color: #f1f5f9; line-height: 1.5; word-break: break-word;">
                  <?= nl2br(e($m['message'])) ?>
                </div>
              </td>

              <td style="font-size: 0.78rem; color: var(--adm-text-secondary); font-family: var(--font-mono); white-space: nowrap;">
                <?= e($timeStr) ?>
              </td>

              <td style="text-align: right;">
                <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan dari <?= e(addslashes($m['user_name'])) ?>?');" style="display: inline;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="message_id" value="<?= (int)$m['id'] ?>">
                  <button type="submit" class="btn btn-danger btn-sm" style="padding: 0.35rem 0.65rem;">
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
