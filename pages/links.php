<?php
/**
 * Halaman Tautan Penting (Links — Gaya Linktree Pill Glass)
 * Cocok untuk bio Instagram / LinkedIn
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$profile = get_profile($pdo);
$customLinks = get_active_links($pdo);

// Cek file CV
$cvUrl = '#';
$hasCv = false;
if (!empty($profile['cv_file'])) {
    $cvPath = UPLOAD_DIR_CV . '/' . basename($profile['cv_file']);
    if (file_exists($cvPath)) {
        $hasCv = true;
        $cvUrl = upload_url('cv', $profile['cv_file']);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tautan Resmi — <?= e($profile['full_name'] ?? 'Ammar Syarif') ?></title>
  <meta name="description" content="Kumpulan tautan resmi, berkas CV, kontak, dan kanal profesional Ammar Syarif.">

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
    <div class="links-wrapper">
      
      <!-- Profil Header -->
      <?php if (!empty($profile['photo']) && file_exists(UPLOAD_DIR_PHOTOS . '/' . basename($profile['photo']))): ?>
        <img src="<?= upload_url('photos', $profile['photo']) ?>" alt="<?= e($profile['full_name']) ?>" class="links-avatar">
      <?php else: ?>
        <div class="links-avatar-placeholder">
          <span>AS</span>
        </div>
      <?php endif; ?>

      <h1 class="links-name"><?= e($profile['full_name'] ?? 'Ammar Syarif') ?></h1>
      <div style="font-size: var(--text-xs); color: var(--color-accent-bright); font-weight: 600; margin-bottom: 0.35rem;">
        <?= e($profile['role_title'] ?? 'Web Developer & Data Analyst') ?>
      </div>
      <p class="links-tagline">
        <?= e($profile['tagline'] ?? 'Membangun sistem yang rapi dan mengubah data jadi keputusan.') ?>
      </p>

      <!-- Pill Links List -->
      <div class="links-pill-list">
        
        <!-- CV Download Pill -->
        <?php if ($hasCv): ?>
          <a href="<?= e($cvUrl) ?>" download class="link-pill" style="border-color: var(--color-accent); background-color: rgba(76, 141, 255, 0.1);">
            <span>📄</span>
            <span>Unduh Berkas CV Resmi (PDF)</span>
          </a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/#contact" class="link-pill">
            <span>📄</span>
            <span>Minta Berkas CV (Hubungi Kontak)</span>
          </a>
        <?php endif; ?>

        <!-- Email Pill -->
        <?php if (!empty($profile['email'])): ?>
          <a href="mailto:<?= e($profile['email']) ?>" class="link-pill">
            <span>✉️</span>
            <span>Kirim Email Profesional</span>
          </a>
        <?php endif; ?>

        <!-- LinkedIn Pill -->
        <?php if (!empty($profile['linkedin'])): ?>
          <a href="<?= e($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer" class="link-pill">
            <span>💼</span>
            <span>Profil LinkedIn</span>
          </a>
        <?php endif; ?>

        <!-- GitHub Pill -->
        <?php if (!empty($profile['github'])): ?>
          <a href="<?= e($profile['github']) ?>" target="_blank" rel="noopener noreferrer" class="link-pill">
            <span>🐙</span>
            <span>Repositori GitHub</span>
          </a>
        <?php endif; ?>

        <?php if (!empty($profile['instagram'])): ?>
          <a href="<?= e($profile['instagram']) ?>" target="_blank" rel="noopener noreferrer" class="link-pill">
            <span>📸</span>
            <span>Instagram</span>
          </a>
        <?php endif; ?>

        <?php if (!empty($profile['tiktok'])): ?>
          <a href="<?= e($profile['tiktok']) ?>" target="_blank" rel="noopener noreferrer" class="link-pill">
            <span>🎵</span>
            <span>TikTok</span>
          </a>
        <?php endif; ?>

        <!-- Tautan Kustom Tambahan dari Database -->
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
        foreach ($customLinks as $cl): 
          // Hindari duplikasi jika label mirip dengan link statis utama
          if (in_array(strtolower($cl['label']), ['unduh cv (pdf)', 'profil linkedin', 'repositori github', 'kirim email resmi', 'instagram', 'tiktok'])) {
              continue;
          }
          $clIcon = $iconEmojiMap[$cl['icon'] ?? ''] ?? '🔗';
        ?>
          <a href="<?= e($cl['url']) ?>" target="_blank" rel="noopener noreferrer" class="link-pill">
            <span><?= $clIcon ?></span>
            <span><?= e($cl['label']) ?></span>
          </a>
        <?php endforeach; ?>

        <!-- Kembali ke Portofolio Utama -->
        <a href="<?= BASE_URL ?>/" class="link-pill" style="margin-top: 1rem; border-color: rgba(255,255,255,0.2);">
          <span>🌐</span>
          <span>Buka Website Portofolio Lengkap</span>
        </a>

      </div>

    </div>
  </div>

  <!-- Site Footer -->
  <footer class="site-footer">
    <div class="site-container footer-inner">
      <div>&copy; <?= date('Y') ?> Ammar Syarif. Bio Links.</div>
      <div style="display: flex; gap: 1.5rem;">
        <a href="<?= BASE_URL ?>/" class="footer-admin-link">Beranda</a>
        <a href="<?= BASE_URL ?>/guestbook" class="footer-admin-link">Buku Tamu</a>
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
