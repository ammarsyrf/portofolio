<?php
/**
 * Admin Dashboard
 * Updated: Ringkasan Metrik Lengkap (Projects, Achievements, Creations, Guestbook)
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Dashboard Ringkasan';

// Hitung statistik proyek
$totalProj = (int)$pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$pubProj = (int)$pdo->query("SELECT COUNT(*) FROM projects WHERE is_published = 1")->fetchColumn();

// Hitung statistik achievements
$totalAch = (int)$pdo->query("SELECT COUNT(*) FROM achievements")->fetchColumn();

// Hitung statistik creations
$totalCreations = (int)$pdo->query("SELECT COUNT(*) FROM creations")->fetchColumn();
$creationsVisible = (get_setting($pdo, 'show_creations_section', '1') === '1');

// Hitung statistik guestbook
$totalGuest = (int)$pdo->query("SELECT COUNT(*) FROM guestbook")->fetchColumn();

// Hitung links
$totalLinks = (int)$pdo->query("SELECT COUNT(*) FROM links")->fetchColumn();

// Data profil
$profile = get_profile($pdo);
$hasCv = !empty($profile['cv_file']) && file_exists(UPLOAD_DIR_CV . '/' . basename($profile['cv_file']));
$hasPhoto = !empty($profile['photo']) && file_exists(UPLOAD_DIR_PHOTOS . '/' . basename($profile['photo']));

// Ambil proyek terkini
$recentProjects = $pdo->query("SELECT * FROM projects ORDER BY sort_order ASC, id DESC LIMIT 5")->fetchAll();

// Ambil pesan guestbook terbaru
$recentGuestbook = $pdo->query("SELECT * FROM guestbook ORDER BY id DESC LIMIT 3")->fetchAll();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div style="margin-bottom: 2.25rem;">
  <div style="font-size: var(--text-xs); color: var(--color-accent-bright); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.35rem;">
    Ringkasan Sistem Portofolio
  </div>
  <h2 style="font-family: var(--font-display); font-size: var(--text-2xl); color: var(--color-text); font-weight: 700;">
    Selamat Datang, <?= e($_SESSION['admin_username'] ?? 'Admin') ?>
  </h2>
  <p style="color: var(--color-text-dim); font-size: var(--text-sm); margin-top: 0.25rem;">
    Kelola seluruh konten, berkas, pencapaian, kreasi, dan interaksi pengunjung dari satu panel kendali terpadu.
  </p>
</div>

<!-- Metrik Statistik Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
  
  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: var(--text-xs); color: var(--color-accent-bright); font-weight: 600; margin-bottom: 0.4rem;">TOTAL PROYEK</div>
    <div style="font-family: var(--font-display); font-size: var(--text-3xl); font-weight: 700; color: var(--color-text);">
      <?= $totalProj ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--color-text-dim); margin-top: 0.25rem;">
      <?= $pubProj ?> dipublikasikan
    </div>
  </div>

  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: var(--text-xs); color: var(--color-accent-bright); font-weight: 600; margin-bottom: 0.4rem;">PENCAPAIAN</div>
    <div style="font-family: var(--font-display); font-size: var(--text-3xl); font-weight: 700; color: var(--color-text);">
      <?= $totalAch ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--color-text-dim); margin-top: 0.25rem;">
      Sertifikat & penghargaan
    </div>
  </div>

  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: var(--text-xs); color: var(--color-accent-bright); font-weight: 600; margin-bottom: 0.4rem;">KREASI TIKTOK & IG</div>
    <div style="font-family: var(--font-display); font-size: var(--text-3xl); font-weight: 700; color: var(--color-text);">
      <?= $totalCreations ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--color-text-dim); margin-top: 0.25rem;">
      Status: <?= $creationsVisible ? 'Ditampilkan' : 'Disembunyikan' ?>
    </div>
  </div>

  <div class="admin-card" style="margin-bottom: 0;">
    <div style="font-size: var(--text-xs); color: var(--color-accent-bright); font-weight: 600; margin-bottom: 0.4rem;">BUKU TAMU</div>
    <div style="font-family: var(--font-display); font-size: var(--text-3xl); font-weight: 700; color: var(--color-text);">
      <?= $totalGuest ?>
    </div>
    <div style="font-size: 0.75rem; color: var(--color-text-dim); margin-top: 0.25rem;">
      Pesan terverifikasi Google
    </div>
  </div>

</div>

<!-- Pintasan Cepat -->
<div style="display: grid; grid-template-columns: 1fr; gap: 2rem; margin-bottom: 2rem;">
  
  <!-- Tabel Proyek Terkini -->
  <div class="admin-card" style="margin-bottom: 0;">
    <div class="admin-card-header">
      <div class="admin-card-title">Daftar Proyek Terkini</div>
      <div style="display: flex; gap: 0.75rem;">
        <a href="<?= BASE_URL ?>/admin/project_form.php" class="btn btn-primary btn-sm">
          + Tambah Proyek
        </a>
        <a href="<?= BASE_URL ?>/admin/projects.php" class="btn btn-secondary btn-sm">
          Semua Proyek
        </a>
      </div>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th style="width: 70px;">Urutan</th>
            <th>Judul Proyek</th>
            <th>Kategori</th>
            <th>Teknologi</th>
            <th>Status</th>
            <th style="text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recentProjects)): ?>
            <tr>
              <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada proyek.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($recentProjects as $p): ?>
              <tr>
                <td style="color: var(--color-accent-bright); font-weight: 600;">#<?= (int)$p['sort_order'] ?></td>
                <td>
                  <strong style="color: var(--color-text);"><?= e($p['title']) ?></strong>
                  <div style="font-size: 0.75rem; color: var(--color-text-faint);"><?= e($p['my_role'] ?: '-') ?></div>
                </td>
                <td><?= e($p['category']) ?></td>
                <td style="font-size: 0.78rem;"><?= e($p['tech_stack'] ?: '-') ?></td>
                <td>
                  <?php if ($p['is_published']): ?>
                    <span class="badge-status badge-published">Published</span>
                  <?php else: ?>
                    <span class="badge-status badge-draft">Draft</span>
                  <?php endif; ?>
                </td>
                <td style="text-align: right;">
                  <a href="<?= BASE_URL ?>/admin/project_form.php?id=<?= (int)$p['id'] ?>" class="btn btn-secondary btn-sm" style="padding: 0.3rem 0.65rem;">
                    Edit
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pesan Guestbook Terkini -->
  <div class="admin-card" style="margin-bottom: 0;">
    <div class="admin-card-header">
      <div class="admin-card-title">Pesan Buku Tamu Terkini</div>
      <a href="<?= BASE_URL ?>/admin/guestbook.php" class="btn btn-secondary btn-sm">
        Kelola & Moderasi
      </a>
    </div>

    <?php if (empty($recentGuestbook)): ?>
      <p style="color: var(--color-text-dim); font-size: var(--text-sm);">Belum ada pesan dari pengunjung.</p>
    <?php else: ?>
      <div style="display: flex; flex-direction: column; gap: 1rem;">
        <?php foreach ($recentGuestbook as $gb): ?>
          <div style="padding: 1rem; border-radius: 8px; background-color: var(--color-bg-surface); border: 1px solid var(--color-line); display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
            <div>
              <div style="font-weight: 600; color: var(--color-text); font-size: var(--text-sm);">
                <?= e($gb['user_name']) ?> <span style="font-weight: 400; font-size: 0.75rem; color: var(--color-text-faint);">&bull; <?= e($gb['created_at']) ?></span>
              </div>
              <div style="font-size: 0.82rem; color: var(--color-text-dim); margin-top: 0.25rem;">
                <?= e(mb_strimwidth($gb['message'], 0, 120, '...')) ?>
              </div>
            </div>
            <a href="<?= BASE_URL ?>/admin/guestbook.php" class="btn btn-secondary btn-sm" style="font-size: 0.72rem; flex-shrink: 0;">
              Lihat
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
