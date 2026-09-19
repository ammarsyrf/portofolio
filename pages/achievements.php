<?php
/**
 * Halaman Pencapaian & Sertifikat (Achievements)
 * Desain: Dark Dashboard / Glass Interface
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$achievements = get_published_achievements($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pencapaian & Sertifikat — Ammar Syarif</title>
  <meta name="description" content="Daftar sertifikat, penghargaan, dan pengakuan profesional Ammar Syarif di bidang rekayasa sistem web dan analitik data.">

  <!-- Google Fonts: Space Grotesk & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

  <!-- Main Stylesheet -->
  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

  <!-- Global Nav -->
  <?php require_once __DIR__ . '/../includes/nav.php'; ?>

  <main class="site-container" style="padding-top: 4.5rem; padding-bottom: 6rem;">
    <div class="section-header">
      <div class="section-caption">Rekam Jejak & Pengakuan</div>
      <h1 class="section-title">Pencapaian & Sertifikat</h1>
      <p style="color: var(--color-text-dim); max-width: var(--max-text-width); margin-top: 0.75rem; font-size: var(--text-base);">
        Dokumentasi sertifikasi kompetensi, pelatihan profesional, dan penghargaan dalam perjalanan rekayasa piranti lunak dan analitik sistem informasi.
      </p>
    </div>

    <?php if (empty($achievements)): ?>
      <div class="glass-panel" style="padding: 4rem 2rem; text-align: center; border-radius: 14px;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">🏆</div>
        <h2 style="font-family: var(--font-display); font-size: var(--text-xl); font-weight: 700; color: var(--color-text); margin-bottom: 0.5rem;">
          Belum Ada Sertifikat yang Ditampilkan
        </h2>
        <p style="color: var(--color-text-dim); max-width: 52ch; margin: 0 auto 1.75rem; font-size: var(--text-sm); line-height: 1.6;">
          Daftar pencapaian dan sertifikat sedang dalam proses kurasi dan verifikasi dokumen. Data dapat ditambahkan secara langsung melalui portal administrator.
        </p>
        <a href="<?= BASE_URL ?>/index.php#projects" class="btn btn-primary btn-sm">
          Lihat Koleksi Proyek
        </a>
      </div>
    <?php else: ?>
      <div class="achievements-grid">
        <?php foreach ($achievements as $ach): 
          $hasImg = !empty($ach['image']) && file_exists(UPLOAD_DIR_ACHIEVEMENTS . '/' . basename($ach['image']));
        ?>
          <article class="glass-panel achievement-card reveal-card">
            <div class="achievement-media">
              <?php if ($hasImg): ?>
                <img src="<?= upload_url('achievements', $ach['image']) ?>" alt="<?= e($ach['title']) ?>" loading="lazy">
              <?php else: ?>
                <div class="project-placeholder-pattern">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="8" r="7"></circle>
                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                  </svg>
                  <span>Sertifikat Terverifikasi</span>
                </div>
              <?php endif; ?>
            </div>

            <div class="achievement-body">
              <div class="achievement-issuer"><?= e($ach['issuer']) ?></div>
              <h2 class="achievement-title"><?= e($ach['title']) ?></h2>

              <?php if (!empty($ach['issue_date'])): ?>
                <div class="achievement-date">Diterbitkan: <?= e($ach['issue_date']) ?></div>
              <?php endif; ?>

              <?php if (!empty($ach['description'])): ?>
                <p class="achievement-desc"><?= e($ach['description']) ?></p>
              <?php endif; ?>

              <?php if (!empty($ach['verify_link'])): ?>
                <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--color-line);">
                  <a href="<?= e($ach['verify_link']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" style="width: 100%;">
                    Verifikasi Sertifikat &rarr;
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <!-- Site Footer -->
  <footer class="site-footer">
    <div class="site-container footer-inner">
      <div>&copy; <?= date('Y') ?> Ammar Syarif. Portfolio & Achievements.</div>
      <div style="display: flex; gap: 1.5rem;">
        <a href="<?= BASE_URL ?>/index.php" class="footer-admin-link">Beranda</a>
        <a href="<?= BASE_URL ?>/pages/links.php" class="footer-admin-link">Tautan</a>
        <a href="<?= BASE_URL ?>/admin/login.php" class="footer-admin-link">Portal Admin</a>
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
