<?php
/**
 * Halaman Kreasi Konten (TikTok & Instagram Kurasi Manual)
 * Desain: Dark Dashboard / Glass Interface
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/seo.php';

$profile = get_profile($pdo);

// Cek toggle pengaturan keterlihatan
$showSection = (get_setting($pdo, 'show_creations_section', '1') === '1');
$creations = $showSection ? get_published_creations($pdo) : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kreasi Konten Digital — Ammar Syarif (@zentokun90) | Zenerie</title>
  <meta name="description" content="Kurasi konten edukasi teknologi, tips rekayasa sistem, dan eksplorasi data di TikTok &amp; Instagram oleh Ammar Syarif (@zentokun90) di Zenerie.">
  <?php render_seo(
      $profile,
      'Kreasi Konten Digital — Ammar Syarif (@zentokun90) | Zenerie',
      'Kurasi konten edukasi teknologi, tips rekayasa sistem, dan eksplorasi data di TikTok & Instagram oleh Ammar Syarif (@zentokun90) di Zenerie.',
      '/creations',
      'website',
      ['Kreasi Konten' => '/creations']
  ); ?>

  <!-- Google Fonts: Space Grotesk & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

  <!-- Global Nav -->
  <?php require_once __DIR__ . '/../includes/nav.php'; ?>

  <main class="site-container" style="padding-top: 4.5rem; padding-bottom: 6rem;">
    <div class="section-header">
      <div class="section-caption">Publikasi & Eksplorasi Media</div>
      <h1 class="section-title">Kreasi Konten Digital</h1>
      <p style="color: var(--color-text-dim); max-width: var(--max-text-width); margin-top: 0.75rem; font-size: var(--text-base);">
        Kurasi konten video singkat dan dokumentasi teknologi di TikTok dan Instagram mengenai rekayasa web, optimasi basis data, dan analitik sistem.
      </p>
    </div>

    <?php if (!$showSection || empty($creations)): ?>
      <div class="glass-panel" style="padding: 4rem 2rem; text-align: center; border-radius: 14px;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">🎬</div>
        <h2 style="font-family: var(--font-display); font-size: var(--text-xl); font-weight: 700; color: var(--color-text); margin-bottom: 0.5rem;">
          Kurasi Konten Sedang Disiapkan
        </h2>
        <p style="color: var(--color-text-dim); max-width: 52ch; margin: 0 auto 1.75rem; font-size: var(--text-sm); line-height: 1.6;">
          Bagian kreasi konten TikTok dan Instagram dikurasi secara manual dan berkala. Saat ini belum ada post yang dipublikasikan atau sedang dinonaktifkan dari panel admin.
        </p>
        <a href="<?= BASE_URL ?>/#projects" class="btn btn-primary btn-sm">
          Jelajahi Proyek Web
        </a>
      </div>
    <?php else: ?>
      <div class="creations-grid">
        <?php foreach ($creations as $c): 
          $hasThumb = !empty($c['thumbnail']) && file_exists(UPLOAD_DIR_CREATIONS . '/' . basename($c['thumbnail']));
          $isTiktok = ($c['platform'] === 'tiktok');
        ?>
          <article class="glass-panel creation-card reveal-card">
            <div class="creation-media">
              <?php if ($hasThumb): ?>
                <img src="<?= upload_url('creations', $c['thumbnail']) ?>" alt="<?= e($c['title']) ?>" loading="lazy">
              <?php else: ?>
                <div class="project-placeholder-pattern">
                  <span style="font-size: 2rem;"><?= $isTiktok ? '📱' : '📸' ?></span>
                  <span>Pratinjau <?= $isTiktok ? 'TikTok' : 'Instagram' ?></span>
                </div>
              <?php endif; ?>

              <span class="platform-badge <?= $isTiktok ? 'badge-tiktok' : 'badge-instagram' ?>">
                <?= $isTiktok ? 'TikTok' : 'Instagram' ?>
              </span>
            </div>

            <div class="creation-body">
              <h2 class="creation-title"><?= e($c['title']) ?></h2>

              <?php if (!empty($c['created_date'])): ?>
                <div style="font-size: 0.72rem; color: var(--color-text-faint); margin-bottom: 0.85rem;">
                  <?= e($c['created_date']) ?>
                </div>
              <?php endif; ?>

              <div style="margin-top: auto; padding-top: 0.75rem; border-top: 1px solid var(--color-line);">
                <a href="<?= e($c['post_link']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" style="width: 100%;">
                  Tonton di <?= $isTiktok ? 'TikTok' : 'Instagram' ?> &rarr;
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <!-- Site Footer -->
  <footer class="site-footer">
    <div class="site-container footer-inner">
      <div>&copy; <?= date('Y') ?> <?= e($profile['full_name'] ?? 'Ammar Syarif') ?> (@zentokun90) — Zenerie. Digital Creations.</div>
      <div style="display: flex; gap: 1.5rem;">
        <a href="<?= BASE_URL ?>/" class="footer-admin-link">Beranda</a>
        <a href="<?= BASE_URL ?>/achievements" class="footer-admin-link">Pencapaian</a>
        <a href="<?= BASE_URL ?>/admin/login" class="footer-admin-link">Portal Admin</a>
      </div>
    </div>
  </footer>

  <!-- Command Palette -->
  <?php require_once __DIR__ . '/../includes/command_palette.php'; ?>

  <!-- Scripts -->
  <script src="<?= asset('js/main.js') ?>"></script>
  <script src="<?= asset('js/command_palette.js') ?>"></script>
</body>
</html>
