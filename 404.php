<?php
/**
 * Custom 404 Error Page — Zenerie / Ammar Syarif
 * Desain Dark Glassmorphism serasi dengan seluruh ekosistem portofolio
 */

http_response_code(404);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/seo.php';

$profile = get_profile($pdo);
$pageTitle = '404 — Halaman Tidak Ditemukan';
$currentPage = '404.php';
$isHome = false;
$homePrefix = BASE_URL . '/';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> | Zenerie</title>
  <meta name="robots" content="noindex, follow">
  
  <?php render_seo(
      profile: $profile,
      title: 'Halaman Tidak Ditemukan (404) | Zenerie',
      description: 'Halaman yang Anda tuju tidak ditemukan atau telah dipindahkan.',
      path: '/404'
  ); ?>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">

  <style>
    .error-page-wrapper {
      min-height: calc(100vh - 160px);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 4rem 1.5rem;
      text-align: center;
    }

    .error-glass-card {
      background: rgba(14, 18, 27, 0.75);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 24px;
      padding: 3.5rem 2.5rem;
      max-width: 620px;
      width: 100%;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6), 0 0 35px rgba(76, 141, 255, 0.12);
      position: relative;
      overflow: hidden;
    }

    .error-glass-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, #38bdf8, #4C8DFF, #a855f7);
    }

    .error-big-code {
      font-family: var(--font-display, 'Space Grotesk', sans-serif);
      font-size: clamp(5rem, 15vw, 8rem);
      font-weight: 800;
      line-height: 1;
      background: linear-gradient(135deg, #38bdf8 0%, #4C8DFF 50%, #a855f7 100%);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 0.5rem;
      letter-spacing: -0.04em;
    }

    .error-sub {
      font-family: var(--font-display, 'Space Grotesk', sans-serif);
      font-size: 1.35rem;
      font-weight: 700;
      color: #f8fafc;
      margin-bottom: 1rem;
    }

    .error-desc {
      font-size: 0.92rem;
      color: var(--color-text-dim, #94a3b8);
      line-height: 1.6;
      max-width: 480px;
      margin: 0 auto 2.25rem;
    }

    .error-actions {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 1rem;
      margin-bottom: 2.25rem;
    }

    .error-quick-strip {
      padding-top: 1.75rem;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 0.75rem;
      font-size: 0.8rem;
    }

    .error-quick-link {
      color: var(--color-text-dim, #94a3b8);
      text-decoration: none;
      padding: 0.35rem 0.75rem;
      border-radius: 9999px;
      background: rgba(255, 255, 255, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.08);
      transition: all 0.2s ease;
    }

    .error-quick-link:hover {
      color: #fff;
      border-color: rgba(76, 141, 255, 0.4);
      background: rgba(76, 141, 255, 0.12);
    }
  </style>
</head>
<body>
  <?php require_once __DIR__ . '/includes/nav.php'; ?>

  <main>
    <div class="error-page-wrapper">
      <div class="error-glass-card">
        <div class="error-big-code">404</div>
        <h1 class="error-sub">Halaman Tidak Ditemukan</h1>
        <p class="error-desc">
          Tautan yang Anda tuju mungkin salah ketik, telah dipindahkan, atau sudah tidak tersedia lagi di server Zenerie.
        </p>

        <div class="error-actions">
          <a href="<?= BASE_URL ?>/" class="btn btn-primary" style="padding: 0.75rem 1.6rem; font-weight: 700;">
            🏠 Kembali ke Beranda
          </a>
          <button type="button" class="btn btn-secondary" onclick="if(window.openCommandPalette) window.openCommandPalette();" style="padding: 0.75rem 1.4rem;">
            🔍 Cari Menu (Ctrl+K)
          </button>
        </div>

        <div class="error-quick-strip">
          <span style="display: flex; align-items: center; color: var(--color-text-faint); margin-right: 0.25rem;">Pintasan:</span>
          <a href="<?= BASE_URL ?>/achievements" class="error-quick-link">🏆 Pencapaian</a>
          <a href="<?= BASE_URL ?>/creations" class="error-quick-link">🎬 Kreasi</a>
          <a href="<?= BASE_URL ?>/guestbook" class="error-quick-link">📖 Buku Tamu</a>
          <a href="<?= BASE_URL ?>/links" class="error-quick-link">🔗 Tautan</a>
        </div>
      </div>
    </div>
  </main>

  <footer class="site-footer" style="padding: 2rem 0; text-align: center; font-size: 0.8rem; color: var(--color-text-faint); border-top: 1px solid var(--color-line);">
    <div class="site-container">
      &copy; <?= date('Y') ?> <?= e($profile['full_name'] ?? 'Ammar Syarif') ?> • Zenerie Portfolio
    </div>
  </footer>

  <?php require_once __DIR__ . '/includes/command_palette.php'; ?>
  <script src="<?= asset('js/main.js') ?>"></script>
  <script src="<?= asset('js/command_palette.js') ?>"></script>
</body>
</html>
