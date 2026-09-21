<?php
/**
 * Halaman Tautan Penting (Links — Modern Dark Glassmorphism Bento Grid)
 * Cocok untuk bio Instagram / LinkedIn / Komunitas
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/seo.php';

$profile = get_profile($pdo);
$customLinks = get_active_links($pdo);

// Fallback profil jika kosong
if (!$profile) {
    $profile = [
        'full_name' => 'Ammar Syarif',
        'role_title' => 'Web Developer & Data Analyst',
        'tagline' => 'Membangun sistem yang rapi dan mengubah data jadi keputusan.',
        'email' => 'zentokun90@gmail.com',
        'linkedin' => 'https://linkedin.com/in/zentokun90',
        'github' => 'https://github.com/zentokun90',
        'instagram' => 'https://instagram.com/zentokun90',
        'tiktok' => 'https://tiktok.com/@zentokun90',
        'photo' => '',
        'cv_file' => ''
    ];
}

// Cek file CV
$cvUrl = '#';
$hasCv = false;
if (!empty($profile['cv_file'])) {
    $cvPath = UPLOAD_DIR_CV . '/' . basename($profile['cv_file']);
    if (file_exists($cvPath)) {
        $hasCv = true;
        $cvUrl = BASE_URL . '/download-cv.php';
    }
}

// Map icon custom links ke SVG yang serasi
$iconSvgMap = [
    'cv'        => '<svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
    'email'     => '<svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>',
    'linkedin'  => '<svg viewBox="0 0 24 24"><path d="M6 9v9M6 6v.01M10 18v-5a4 4 0 0 1 8 0v5m-8-4v4"/></svg>',
    'github'    => '<svg viewBox="0 0 24 24"><path d="M15 22v-3.9c0-1 .1-1.6-.5-2.2 2.4-.3 4.9-1.2 4.9-5.3 0-1.2-.4-2.1-1.1-2.9.1-.3.5-1.4-.1-2.9 0 0-.9-.3-3 1.1a10.2 10.2 0 0 0-5.4 0C7.7 4.6 6.8 4.9 6.8 4.9c-.6 1.5-.2 2.6-.1 2.9-.7.8-1.1 1.7-1.1 2.9 0 4.1 2.5 5 4.9 5.3-.6.6-.6 1.3-.6 2.2V22"/><path d="M9 19c-2 .6-3.5-.5-4-1.5"/></svg>',
    'instagram' => '<svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
    'tiktok'    => '<svg viewBox="0 0 24 24"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>',
    'globe'     => '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
    'code'      => '<svg viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
    'link'      => '<svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.1.1l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1"/><path d="M14 11a5 5 0 0 0-7.1-.1l-2 2A5 5 0 0 0 12 20l1.1-1.1"/></svg>'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tautan Resmi <?= e($profile['full_name'] ?? 'Ammar Syarif') ?> — Bio Bento | Zenerie</title>
  <meta name="description" content="Pusat tautan resmi, berkas CV, kontak, repositori kode, dan media sosial <?= e($profile['full_name'] ?? 'Ammar Syarif') ?> di Zenerie.">
  <?php render_seo(
      $profile,
      'Tautan Resmi ' . ($profile['full_name'] ?? 'Ammar Syarif') . ' — Bio Bento | Zenerie',
      'Pusat tautan resmi, berkas CV, kontak, repositori kode, dan media sosial ' . ($profile['full_name'] ?? 'Ammar Syarif') . ' di Zenerie.',
      '/links',
      'website',
      ['Tautan Bio' => '/links']
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

  <div class="site-container">
    <div class="links-bento-container">
      
      <!-- Profile Hero Bento Card -->
      <div class="links-profile-bento-card">
        <div class="links-profile-main">
          <div class="links-avatar-wrap">
            <?php if (!empty($profile['photo']) && file_exists(UPLOAD_DIR_PHOTOS . '/' . basename($profile['photo']))): ?>
              <img src="<?= upload_url('photos', $profile['photo']) ?>" alt="<?= e($profile['full_name']) ?>" class="links-bento-avatar">
            <?php else: ?>
              <div class="links-bento-avatar-placeholder">
                <span>AS</span>
              </div>
            <?php endif; ?>
            <span class="links-online-indicator" title="Tersedia untuk kolaborasi & proyek"></span>
          </div>

          <div class="links-profile-info">
            <div class="links-badge-group">
              <span class="links-role-pill"><?= e($profile['role_title'] ?? 'Web Developer & Data Analyst') ?></span>
              <span class="links-status-chip"><span class="chip-pulse"></span> Open for Opportunities</span>
            </div>
            <h1 class="links-bento-name"><?= e($profile['full_name'] ?? 'Ammar Syarif') ?></h1>
            <p class="links-bento-tagline">
              <?= e($profile['tagline'] ?? 'Membangun sistem yang rapi dan mengubah data jadi keputusan.') ?>
            </p>
          </div>
        </div>

        <div class="links-profile-actions">
          <button type="button" class="links-action-btn" id="btnShareLinks" title="Salin tautan bio ini">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
            <span id="btnShareText">Bagikan Tautan</span>
          </button>
          <a href="<?= BASE_URL ?>/" class="links-action-btn links-action-btn-primary" title="Kunjungi Portofolio Lengkap">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/><path d="M9 21v-7h6v7"/></svg>
            <span>Portofolio Utama</span>
          </a>
        </div>
      </div>

      <!-- Bento Grid Layout -->
      <div class="links-bento-grid">
        
        <!-- 1. Featured CV / Resume Card (Spans 2 columns on desktop) -->
        <?php if ($hasCv): ?>
          <a href="<?= e($cvUrl) ?>" download class="links-bento-card card-cv card-span-2">
            <div class="card-glow-bg"></div>
            <div class="links-card-content">
              <div class="links-card-header">
                <span class="links-card-badge"><span class="badge-dot"></span> RESUME RESMI (PDF)</span>
                <span class="links-card-action-icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </span>
              </div>
              <div class="links-card-body">
                <div class="links-card-icon-box">
                  <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <div class="links-card-text">
                  <h2 class="links-card-title">Unduh Curriculum Vitae (CV)</h2>
                  <p class="links-card-desc">Kualifikasi lengkap, keahlian teknis, riwayat kerja, dan pencapaian profesional terbaru dalam format PDF resmi.</p>
                </div>
              </div>
              <div class="links-card-footer">
                <span class="links-card-cta">Unduh Berkas Sekarang &rarr;</span>
              </div>
            </div>
          </a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/#contact" class="links-bento-card card-cv card-span-2">
            <div class="card-glow-bg"></div>
            <div class="links-card-content">
              <div class="links-card-header">
                <span class="links-card-badge"><span class="badge-dot"></span> RESUME / CV</span>
                <span class="links-card-action-icon" aria-hidden="true">↗</span>
              </div>
              <div class="links-card-body">
                <div class="links-card-icon-box">
                  <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <div class="links-card-text">
                  <h2 class="links-card-title">Minta Berkas Resume (CV)</h2>
                  <p class="links-card-desc">Hubungi saya langsung untuk mendapatkan salinan berkas CV &amp; portofolio teknis terbaru.</p>
                </div>
              </div>
              <div class="links-card-footer">
                <span class="links-card-cta">Hubungi via Kontak &rarr;</span>
              </div>
            </div>
          </a>
        <?php endif; ?>

        <!-- 2. Email Card -->
        <?php if (!empty($profile['email'])): ?>
          <a href="mailto:<?= e($profile['email']) ?>" class="links-bento-card card-email">
            <div class="card-glow-bg"></div>
            <div class="links-card-content">
              <div class="links-card-header">
                <span class="links-card-kicker">Email Langsung</span>
                <span class="links-card-action-icon" aria-hidden="true">↗</span>
              </div>
              <div class="links-card-icon-box">
                <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
              </div>
              <div class="links-card-text">
                <h3 class="links-card-title">Kirim Email Resmi</h3>
                <p class="links-card-desc">Kirim pesan langsung untuk diskusi proyek atau kolaborasi</p>
              </div>
            </div>
          </a>
        <?php endif; ?>

        <!-- 3. LinkedIn Card -->
        <?php if (!empty($profile['linkedin'])): ?>
          <a href="<?= e($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer" class="links-bento-card card-linkedin">
            <div class="card-glow-bg"></div>
            <div class="links-card-content">
              <div class="links-card-header">
                <span class="links-card-kicker">Jaringan Karir</span>
                <span class="links-card-action-icon" aria-hidden="true">↗</span>
              </div>
              <div class="links-card-icon-box">
                <svg viewBox="0 0 24 24"><path d="M6 9v9M6 6v.01M10 18v-5a4 4 0 0 1 8 0v5m-8-4v4"/></svg>
              </div>
              <div class="links-card-text">
                <h3 class="links-card-title">LinkedIn</h3>
                <p class="links-card-desc">Koneksi &amp; riwayat karir profesional</p>
              </div>
            </div>
          </a>
        <?php endif; ?>

        <!-- 4. GitHub Card -->
        <?php if (!empty($profile['github'])): ?>
          <a href="<?= e($profile['github']) ?>" target="_blank" rel="noopener noreferrer" class="links-bento-card card-github">
            <div class="card-glow-bg"></div>
            <div class="links-card-content">
              <div class="links-card-header">
                <span class="links-card-kicker">Open Source</span>
                <span class="links-card-action-icon" aria-hidden="true">↗</span>
              </div>
              <div class="links-card-icon-box">
                <svg viewBox="0 0 24 24"><path d="M15 22v-3.9c0-1 .1-1.6-.5-2.2 2.4-.3 4.9-1.2 4.9-5.3 0-1.2-.4-2.1-1.1-2.9.1-.3.5-1.4-.1-2.9 0 0-.9-.3-3 1.1a10.2 10.2 0 0 0-5.4 0C7.7 4.6 6.8 4.9 6.8 4.9c-.6 1.5-.2 2.6-.1 2.9-.7.8-1.1 1.7-1.1 2.9 0 4.1 2.5 5 4.9 5.3-.6.6-.6 1.3-.6 2.2V22"/><path d="M9 19c-2 .6-3.5-.5-4-1.5"/></svg>
              </div>
              <div class="links-card-text">
                <h3 class="links-card-title">GitHub</h3>
                <p class="links-card-desc">Repositori kode &amp; proyek aktif</p>
              </div>
            </div>
          </a>
        <?php endif; ?>

        <!-- 5. Instagram Card -->
        <?php if (!empty($profile['instagram'])): ?>
          <a href="<?= e($profile['instagram']) ?>" target="_blank" rel="noopener noreferrer" class="links-bento-card card-instagram">
            <div class="card-glow-bg"></div>
            <div class="links-card-content">
              <div class="links-card-header">
                <span class="links-card-kicker">Media Sosial</span>
                <span class="links-card-action-icon" aria-hidden="true">↗</span>
              </div>
              <div class="links-card-icon-box">
                <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
              </div>
              <div class="links-card-text">
                <h3 class="links-card-title">Instagram</h3>
                <p class="links-card-desc">Dokumentasi visual &amp; kabar terbaru</p>
              </div>
            </div>
          </a>
        <?php endif; ?>

        <!-- 6. TikTok Card -->
        <?php if (!empty($profile['tiktok'])): ?>
          <a href="<?= e($profile['tiktok']) ?>" target="_blank" rel="noopener noreferrer" class="links-bento-card card-tiktok">
            <div class="card-glow-bg"></div>
            <div class="links-card-content">
              <div class="links-card-header">
                <span class="links-card-kicker">Video Pendek</span>
                <span class="links-card-action-icon" aria-hidden="true">↗</span>
              </div>
              <div class="links-card-icon-box">
                <svg viewBox="0 0 24 24"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
              </div>
              <div class="links-card-text">
                <h3 class="links-card-title">TikTok</h3>
                <p class="links-card-desc">Konten rekayasa web &amp; video kreasi</p>
              </div>
            </div>
          </a>
        <?php endif; ?>

        <!-- 7. Guestbook Card -->
        <a href="<?= BASE_URL ?>/guestbook" class="links-bento-card card-guestbook">
          <div class="card-glow-bg"></div>
          <div class="links-card-content">
            <div class="links-card-header">
              <span class="links-card-kicker">Interaksi Publik</span>
              <span class="links-card-action-icon" aria-hidden="true">↗</span>
            </div>
            <div class="links-card-icon-box">
              <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div class="links-card-text">
              <h3 class="links-card-title">Buku Tamu (Guestbook)</h3>
              <p class="links-card-desc">Tinggalkan pesan apresiasi, sapaan, atau masukan publik</p>
            </div>
          </div>
        </a>

        <!-- 8. Custom Database Links -->
        <?php 
        foreach ($customLinks as $cl): 
          // Hindari duplikasi jika label mirip dengan link statis utama
          if (in_array(strtolower(trim($cl['label'])), ['unduh cv (pdf)', 'profil linkedin', 'repositori github', 'kirim email resmi', 'instagram', 'tiktok', 'buku tamu'])) {
              continue;
          }
          $iconKey = strtolower(trim($cl['icon'] ?? 'link'));
          $svgMarkup = $iconSvgMap[$iconKey] ?? $iconSvgMap['link'];
          if (str_starts_with($cl['url'], 'mailto:')) {
              $host = 'Email Langsung';
          } else {
              $host = parse_url($cl['url'], PHP_URL_HOST) ?: 'Tautan Eksternal';
          }
        ?>
          <a href="<?= e($cl['url']) ?>" target="_blank" rel="noopener noreferrer" class="links-bento-card card-custom">
            <div class="card-glow-bg"></div>
            <div class="links-card-content">
              <div class="links-card-header">
                <span class="links-card-kicker">Tautan Tambahan</span>
                <span class="links-card-action-icon" aria-hidden="true">↗</span>
              </div>
              <div class="links-card-icon-box">
                <?= $svgMarkup ?>
              </div>
              <div class="links-card-text">
                <h3 class="links-card-title"><?= e($cl['label']) ?></h3>
                <p class="links-card-desc"><?= e($host) ?></p>
              </div>
            </div>
          </a>
        <?php endforeach; ?>

        <!-- 9. Bottom Card: Full Portfolio Showcase (Spans full width) -->
        <a href="<?= BASE_URL ?>/" class="links-bento-card card-portfolio card-span-full">
          <div class="card-glow-bg"></div>
          <div class="links-card-content links-portfolio-content">
            <div class="links-portfolio-left">
              <div class="links-card-icon-box" style="margin-bottom: 0;">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
              </div>
              <div class="links-portfolio-text">
                <div class="links-card-kicker">PORTAL UTAMA</div>
                <h2 class="links-card-title">Jelajahi Portofolio Interaktif Lengkap</h2>
                <p class="links-card-desc">Studi kasus mendalam, demonstrasi live proyek, dashboard analitik, dan buku tamu.</p>
              </div>
            </div>
            <div class="links-portfolio-action">
              <span class="links-portfolio-btn">Buka Portofolio &rarr;</span>
            </div>
          </div>
        </a>

      </div>

    </div>
  </div>

  <!-- Site Footer -->
  <footer class="site-footer">
    <div class="site-container footer-inner">
      <div>&copy; <?= date('Y') ?> <?= e($profile['full_name'] ?? 'Ammar Syarif') ?> — Zenerie. Bio Bento Links.</div>
      <div style="display: flex; gap: 1.5rem;">
        <a href="<?= BASE_URL ?>/" class="footer-admin-link">Beranda</a>
        <a href="<?= BASE_URL ?>/guestbook" class="footer-admin-link">Buku Tamu</a>
        <a href="<?= BASE_URL ?>/admin/login" class="footer-admin-link">Portal Admin</a>
      </div>
    </div>
  </footer>

  <!-- Command Palette -->
  <?php require_once __DIR__ . '/../includes/command_palette.php'; ?>

  <!-- Scripts -->
  <script src="<?= asset('js/main.js') ?>"></script>
  <script src="<?= asset('js/command_palette.js') ?>"></script>
  <script>
    // Fitur Salin / Bagikan Tautan Bio
    document.getElementById('btnShareLinks')?.addEventListener('click', function() {
      const url = window.location.href;
      const btnText = document.getElementById('btnShareText');
      
      const setCopied = () => {
        if (!btnText) return;
        const originalText = btnText.textContent;
        btnText.textContent = 'Tautan Disalin! ✓';
        setTimeout(() => {
          btnText.textContent = originalText;
        }, 2200);
      };

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(setCopied).catch(() => fallbackCopy(url));
      } else {
        fallbackCopy(url);
      }

      function fallbackCopy(text) {
        const input = document.createElement('input');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        try {
          document.execCommand('copy');
          setCopied();
        } catch (err) {}
        document.body.removeChild(input);
      }
    });
  </script>
</body>
</html>
