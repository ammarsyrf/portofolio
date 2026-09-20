<?php
/**
 * Halaman Kreasi Konten (TikTok & Instagram Reels Creator Grid)
 * Desain: TikTok / Reels Creator Profile & Vertical 9:16 Video Feed
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/seo.php';

$profile = get_profile($pdo);

// Cek toggle pengaturan keterlihatan
$showSection = (get_setting($pdo, 'show_creations_section', '1') === '1');
$creations = $showSection ? get_published_creations($pdo) : [];

// Kelompokkan dan hitung per platform
$tiktokCount = 0;
$igCount = 0;
foreach ($creations as $item) {
    if (($item['platform'] ?? '') === 'tiktok') {
        $tiktokCount++;
    } elseif (($item['platform'] ?? '') === 'instagram') {
        $igCount++;
    }
}

// Avatar Creator
$hasPhoto = !empty($profile['photo']) && file_exists(UPLOAD_DIR_PHOTOS . '/' . basename($profile['photo']));
$avatarUrl = $hasPhoto ? upload_url('photos', $profile['photo']) : asset('img/default-avatar.png');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kreasi Konten Digital — <?= e($profile['full_name'] ?? 'Ammar Syarif') ?> | Zenerie</title>
  <meta name="description" content="Kurasi konten edukasi teknologi, tips rekayasa sistem, dan eksplorasi data di media sosial oleh <?= e($profile['full_name'] ?? 'Ammar Syarif') ?> di Zenerie.">
  <?php render_seo(
      $profile,
      'Kreasi Konten Digital — ' . ($profile['full_name'] ?? 'Ammar Syarif') . ' | Zenerie',
      'Kurasi konten edukasi teknologi, tips rekayasa sistem, dan eksplorasi data di media sosial oleh ' . ($profile['full_name'] ?? 'Ammar Syarif') . ' di Zenerie.',
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
    
    <!-- Channel Profile Header Banner -->
    <div class="creations-channel-card">
      <div class="creations-channel-inner">
        <div class="creations-channel-left">
          <div class="creations-avatar-ring">
            <img src="<?= e($avatarUrl) ?>" alt="<?= e($profile['full_name'] ?? 'Ammar Syarif') ?>" class="creations-avatar-img">
          </div>
          <div class="creations-channel-info">
            <div class="creations-channel-pills">
              <span class="channel-pill">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Kreator Teknologi
              </span>
              <span class="channel-status-chip">
                <span class="dot-pulse"></span> Aktif Berkreasi
              </span>
            </div>
            <h1 class="creations-channel-name"><?= e($profile['full_name'] ?? 'Ammar Syarif') ?></h1>
            <p class="creations-channel-tagline">
              <?= e($profile['tagline'] ?? 'Membangun sistem yang rapi dan mengubah data jadi keputusan.') ?>
            </p>
          </div>
        </div>

        <div class="creations-channel-actions">
          <?php if (!empty($profile['tiktok'])): ?>
            <a href="<?= e($profile['tiktok']) ?>" target="_blank" rel="noopener noreferrer" class="creator-btn creator-btn-tiktok" title="Kunjungi Saluran TikTok">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1.04-.1z"/></svg>
              <span>Ikuti di TikTok</span>
              <span aria-hidden="true" style="font-size: 0.8rem;">↗</span>
            </a>
          <?php endif; ?>

          <?php if (!empty($profile['instagram'])): ?>
            <a href="<?= e($profile['instagram']) ?>" target="_blank" rel="noopener noreferrer" class="creator-btn creator-btn-instagram" title="Kunjungi Profil Instagram">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
              <span>Buka Instagram</span>
              <span aria-hidden="true" style="font-size: 0.8rem;">↗</span>
            </a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Channel Stats Strip -->
      <div class="creations-channel-stats">
        <div class="channel-stat-item">
          <div class="stat-icon-box">🎬</div>
          <div>
            <div class="stat-value"><?= count($creations) ?> Video</div>
            <div class="stat-label">Terkurasi Pilihan</div>
          </div>
        </div>

        <div class="channel-stat-item">
          <div class="stat-icon-box">⚡</div>
          <div>
            <div class="stat-value">TikTok &amp; Instagram</div>
            <div class="stat-label">Saluran Distribusi</div>
          </div>
        </div>

        <div class="channel-stat-item">
          <div class="stat-icon-box">📱</div>
          <div>
            <div class="stat-value">Format Vertikal 9:16</div>
            <div class="stat-label">Feed Video Singkat</div>
          </div>
        </div>
      </div>
    </div>

    <?php if (!$showSection || empty($creations)): ?>
      <div class="glass-panel" style="padding: 4rem 2rem; text-align: center; border-radius: 20px;">
        <div style="font-size: 3.5rem; margin-bottom: 1rem;">🎬</div>
        <h2 style="font-family: var(--font-display); font-size: var(--text-xl); font-weight: 700; color: var(--color-text); margin-bottom: 0.5rem;">
          Kurasi Konten Sedang Disiapkan
        </h2>
        <p style="color: var(--color-text-dim); max-width: 52ch; margin: 0 auto 1.75rem; font-size: var(--text-sm); line-height: 1.6;">
          Bagian kreasi konten TikTok dan Instagram dikurasi secara manual dan berkala. Saat ini belum ada post yang dipublikasikan atau sedang dinonaktifkan dari panel admin.
        </p>
        <a href="<?= BASE_URL ?>/#projects" class="btn btn-primary btn-sm">
          Jelajahi Proyek Web &rarr;
        </a>
      </div>
    <?php else: ?>

      <!-- Filter Tabs Navigation -->
      <div class="creations-filter-bar" id="creationsFilterBar" role="tablist" aria-label="Filter Platform Konten">
        <button type="button" class="filter-tab-btn is-active" data-filter="all" role="tab" aria-selected="true">
          <span>Semua Konten</span>
          <span class="tab-badge"><?= count($creations) ?></span>
        </button>
        <button type="button" class="filter-tab-btn" data-filter="tiktok" role="tab" aria-selected="false">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" style="color: #00f2ea;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1.04-.1z"/></svg>
          <span>TikTok Video</span>
          <span class="tab-badge"><?= $tiktokCount ?></span>
        </button>
        <button type="button" class="filter-tab-btn" data-filter="instagram" role="tab" aria-selected="false">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #e1306c;"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          <span>Instagram Reels</span>
          <span class="tab-badge"><?= $igCount ?></span>
        </button>
      </div>

      <!-- 9:16 Vertical Video Grid -->
      <div class="creations-reel-grid" id="creationsGrid">
        <?php foreach ($creations as $c): 
          $hasThumb = !empty($c['thumbnail']) && file_exists(UPLOAD_DIR_CREATIONS . '/' . basename($c['thumbnail']));
          $isTiktok = (($c['platform'] ?? '') === 'tiktok');
          $platformKey = $isTiktok ? 'tiktok' : 'instagram';
        ?>
          <a href="<?= e($c['post_link']) ?>" target="_blank" rel="noopener noreferrer" class="creation-reel-card" data-platform="<?= $platformKey ?>" title="Tonton <?= e($c['title']) ?> di <?= $isTiktok ? 'TikTok' : 'Instagram' ?>">
            
            <!-- Media / Thumbnail Layer -->
            <div class="reel-media-wrap">
              <?php if ($hasThumb): ?>
                <img src="<?= upload_url('creations', $c['thumbnail']) ?>" alt="<?= e($c['title']) ?>" class="reel-media-img" loading="lazy">
              <?php else: ?>
                <div class="reel-placeholder-poster">
                  <div class="reel-placeholder-pattern"></div>
                  <div class="reel-placeholder-watermark"><?= $isTiktok ? '📱' : '📸' ?></div>
                  <span class="reel-placeholder-label"><?= $isTiktok ? 'TikTok Video' : 'Instagram Reel' ?></span>
                </div>
              <?php endif; ?>
            </div>

            <!-- Top Floating Header (Platform Badge & Arrow) -->
            <div class="reel-top-bar">
              <span class="reel-platform-badge <?= $isTiktok ? 'reel-badge-tiktok' : 'reel-badge-instagram' ?>">
                <?php if ($isTiktok): ?>
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1.04-.1z"/></svg>
                  <span>TikTok</span>
                <?php else: ?>
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/></svg>
                  <span>Instagram</span>
                <?php endif; ?>
              </span>
              <span class="reel-arrow-btn" aria-hidden="true">↗</span>
            </div>

            <!-- Central Floating Play Button -->
            <div class="reel-play-btn" aria-hidden="true">
              <svg viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            </div>

            <!-- Bottom Gradient Scrim & Info -->
            <div class="reel-scrim">
              <?php if (!empty($c['created_date'])): ?>
                <div class="reel-date-pill">
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                  <span><?= e($c['created_date']) ?></span>
                </div>
              <?php endif; ?>

              <h2 class="reel-title"><?= e($c['title']) ?></h2>

              <div class="reel-cta-action">
                <span>Tonton di <?= $isTiktok ? 'TikTok' : 'Instagram' ?></span>
                <span aria-hidden="true">&rarr;</span>
              </div>
            </div>

          </a>
        <?php endforeach; ?>
      </div>

      <!-- Empty State for Filter Result -->
      <div id="filterEmptyState" class="glass-panel" style="display: none; padding: 3.5rem 1.5rem; text-align: center; border-radius: 18px; margin-top: 1rem;">
        <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">🔍</div>
        <h3 style="color: #fff; font-family: var(--font-display); font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">
          Belum Ada Konten di Kategori Ini
        </h3>
        <p style="color: var(--color-text-dim); font-size: 0.85rem; max-width: 42ch; margin: 0 auto 1.25rem;">
          Konten untuk platform yang kamu pilih sedang dalam proses produksi.
        </p>
        <button type="button" class="btn btn-secondary btn-sm" id="btnResetFilter">
          Tampilkan Semua Konten
        </button>
      </div>

    <?php endif; ?>

  </main>

  <!-- Site Footer -->
  <footer class="site-footer">
    <div class="site-container footer-inner">
      <div>&copy; <?= date('Y') ?> <?= e($profile['full_name'] ?? 'Ammar Syarif') ?> — Zenerie. Digital Creations.</div>
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

  <!-- Interactive Filter Tabs Script -->
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('#creationsFilterBar .filter-tab-btn');
    const reelCards = document.querySelectorAll('#creationsGrid .creation-reel-card');
    const filterEmpty = document.getElementById('filterEmptyState');
    const btnResetFilter = document.getElementById('btnResetFilter');

    function applyFilter(filterKey) {
      // Update buttons state
      filterButtons.forEach(btn => {
        const isActive = (btn.dataset.filter === filterKey);
        btn.classList.toggle('is-active', isActive);
        btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });

      let visibleCount = 0;
      reelCards.forEach(card => {
        const platform = card.dataset.platform;
        if (filterKey === 'all' || platform === filterKey) {
          card.classList.remove('is-hidden');
          visibleCount++;
        } else {
          card.classList.add('is-hidden');
        }
      });

      if (filterEmpty) {
        filterEmpty.style.display = (visibleCount === 0) ? 'block' : 'none';
      }

      // Update URL hash smoothly without jump
      if (filterKey !== 'all') {
        history.replaceState(null, '', `#${filterKey}`);
      } else if (window.location.hash) {
        history.replaceState(null, '', window.location.pathname);
      }
    }

    filterButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        applyFilter(btn.dataset.filter);
      });
    });

    btnResetFilter?.addEventListener('click', () => {
      applyFilter('all');
    });

    // Check URL Hash on load (e.g. /creations#tiktok or /creations#instagram)
    const initialHash = window.location.hash.replace('#', '').toLowerCase();
    if (initialHash === 'tiktok' || initialHash === 'instagram') {
      applyFilter(initialHash);
    }
  });
  </script>
</body>
</html>
