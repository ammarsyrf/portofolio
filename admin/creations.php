<?php
/**
 * Kelola Kreasi Konten (TikTok & Instagram): List, Toggle Section, & Hapus
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Kelola Kreasi Konten (TikTok & IG)';

// Handle Toggle Section Visibility (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_section') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        set_flash('danger', 'Token CSRF tidak valid. Silakan coba kembali.');
        redirect(BASE_URL . '/admin/creations');
    }

    $enableSection = isset($_POST['show_creations_section']) ? '1' : '0';
    set_setting($pdo, 'show_creations_section', $enableSection);

    set_flash('success', $enableSection === '1' 
        ? 'Halaman Kreasi berhasil DI-AKTIFKAN untuk publik.' 
        : 'Halaman Kreasi sekarang DI-NONAKTIFKAN (menampilkan pesan penyiapan di publik).'
    );
    redirect(BASE_URL . '/admin/creations');
}

// Handle Hapus Kreasi (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        set_flash('danger', 'Token CSRF tidak valid. Silakan coba kembali.');
        redirect(BASE_URL . '/admin/creations');
    }

    $creationId = (int)($_POST['creation_id'] ?? 0);
    if ($creationId > 0) {
        $creation = get_creation_by_id($pdo, $creationId);
        if ($creation) {
            // Hapus file thumbnail jika ada
            if (!empty($creation['thumbnail'])) {
                delete_uploaded_file($creation['thumbnail'], UPLOAD_DIR_CREATIONS);
            }

            $delStmt = $pdo->prepare("DELETE FROM creations WHERE id = :id");
            $delStmt->execute([':id' => $creationId]);

            set_flash('success', 'Kreasi "' . e($creation['title']) . '" berhasil dihapus.');
        } else {
            set_flash('danger', 'Kreasi tidak ditemukan.');
        }
    }
    redirect(BASE_URL . '/admin/creations');
}

// Ambil status toggle dan daftar seluruh kreasi
$showCreationsSection = (get_setting($pdo, 'show_creations_section', '1') === '1');
$creations = get_all_creations($pdo);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<!-- Card Pengaturan Visibilitas Section Publik -->
<div class="admin-card" style="margin-bottom: 1.5rem; background: rgba(15, 23, 42, 0.45); border-color: rgba(59, 130, 246, 0.3);">
  <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
    <div>
      <div style="font-weight: 700; color: #ffffff; font-size: 0.95rem; display: flex; align-items: center; gap: 0.55rem;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--adm-accent-bright);">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        Status Tampilan Halaman Kreasi di Publik
      </div>
      <p style="font-size: 0.78rem; color: var(--adm-text-secondary); margin-top: 0.35rem; max-width: 65ch;">
        Jika dinonaktifkan, pengunjung yang membuka <code>/creations</code> akan melihat tampilan informatif bahwa kurasi konten sedang disiapkan.
      </p>
    </div>

    <form method="POST" action="" style="margin: 0;">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="toggle_section">
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <label style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.8rem; color: #ffffff;">
          <input type="checkbox" name="show_creations_section" value="1" <?= $showCreationsSection ? 'checked' : '' ?> onchange="this.form.submit()" style="width: 18px; height: 18px; accent-color: var(--adm-emerald);">
          <span style="font-weight: 700; color: <?= $showCreationsSection ? '#34d399' : '#f87171' ?>;">
            <?= $showCreationsSection ? '● Aktif (Ditampilkan)' : '○ Nonaktif (Disembunyikan)' ?>
          </span>
        </label>
      </div>
    </form>
  </div>
</div>

<!-- Card Daftar Konten Kreasi -->
<div class="admin-card">
  <div class="admin-card-header">
    <div>
      <div class="admin-card-title">Daftar Konten TikTok & Instagram</div>
      <p style="font-size: var(--text-xs); color: var(--adm-text-secondary); margin-top: 0.25rem;">
        Kurasi manual post edukasi teknologi dan video coding untuk audiens media sosial.
      </p>
    </div>
    <div>
      <a href="<?= BASE_URL ?>/admin/creation-form" class="btn btn-primary btn-sm">
        + Tambah Kreasi Baru
      </a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 50px;">Thumb</th>
          <th style="width: 70px;">Urutan</th>
          <th>Platform & Judul Post</th>
          <th>Tanggal</th>
          <th>Tautan Post</th>
          <th style="width: 100px;">Status</th>
          <th style="text-align: right; width: 140px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($creations)): ?>
          <tr>
            <td colspan="7" style="text-align: center; padding: 3rem 1rem;">
              <p style="color: var(--adm-text-muted); margin-bottom: 1rem;">Belum ada postingan kreasi yang dikurasi.</p>
              <a href="<?= BASE_URL ?>/admin/creation-form" class="btn btn-primary btn-sm">Tambah Kreasi Pertama</a>
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($creations as $c): 
            $hasThumb = !empty($c['thumbnail']) && file_exists(UPLOAD_DIR_CREATIONS . '/' . basename($c['thumbnail']));
          ?>
            <tr>
              <td>
                <?php if ($hasThumb): ?>
                  <img src="<?= upload_url('creations', $c['thumbnail']) ?>" alt="<?= e($c['title']) ?>" style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid var(--adm-border); box-shadow: 0 4px 12px rgba(0,0,0,0.35);">
                <?php else: ?>
                  <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.04); border: 1px solid var(--adm-border); display: flex; align-items: center; justify-content: center; font-size: 0.68rem; color: var(--adm-text-muted); border-radius: 8px;">
                    No Img
                  </div>
                <?php endif; ?>
              </td>

              <td style="color: var(--adm-accent-bright); font-family: var(--font-mono); font-weight: 700;">
                #<?= (int)$c['sort_order'] ?>
              </td>

              <td>
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.2rem;">
                  <?php if ($c['platform'] === 'tiktok'): ?>
                    <span style="font-size: 0.65rem; font-weight: 700; padding: 0.18rem 0.5rem; border-radius: 4px; background: rgba(0, 242, 234, 0.15); color: #00f2ea; border: 1px solid rgba(0, 242, 234, 0.3);">
                      TIKTOK
                    </span>
                  <?php else: ?>
                    <span style="font-size: 0.65rem; font-weight: 700; padding: 0.18rem 0.5rem; border-radius: 4px; background: rgba(225, 48, 108, 0.15); color: #e1306c; border: 1px solid rgba(225, 48, 108, 0.3);">
                      INSTAGRAM
                    </span>
                  <?php endif; ?>
                  <span style="font-weight: 600; color: #ffffff; font-size: 0.88rem;">
                    <?= e($c['title']) ?>
                  </span>
                </div>
              </td>

              <td style="font-size: 0.8rem; color: var(--adm-text-secondary); font-family: var(--font-mono);">
                <?= e($c['created_date'] ?: '-') ?>
              </td>

              <td>
                <a href="<?= e($c['post_link']) ?>" target="_blank" rel="noopener noreferrer" style="font-size: 0.78rem; font-weight: 600; color: #38bdf8; display: inline-flex; align-items: center; gap: 0.25rem;">
                  Buka Konten &nearr;
                </a>
              </td>

              <td>
                <?php if ($c['is_published']): ?>
                  <span class="badge-status badge-published">Published</span>
                <?php else: ?>
                  <span class="badge-status badge-draft">Draft</span>
                <?php endif; ?>
              </td>

              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 0.45rem;">
                  <a href="<?= BASE_URL ?>/admin/creation-form?id=<?= (int)$c['id'] ?>" class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.65rem;">
                    Edit
                  </a>

                  <form method="POST" action="" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kreasi <?= e(addslashes($c['title'])) ?>? Tindakan ini tidak dapat dibatalkan.');" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="creation_id" value="<?= (int)$c['id'] ?>">
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
