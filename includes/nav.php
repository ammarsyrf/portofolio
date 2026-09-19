<?php
/**
 * Global Navigation Header (Dipakai di seluruh halaman publik)
 * Desain: Modern Floating Pill Bar (Bento Dashboard Aesthetic)
 * Memuat navigasi terpadu, avatar mini, tombol "Rekrut Saya", dan trigger Command Palette (⌘K)
 */

$currentPage = basename($_SERVER['PHP_SELF']);
$isHome = ($currentPage === 'index.php');
$homePrefix = $isHome ? '' : (BASE_URL . '/index.php');

// Fetch mini photo if exists
$navPhoto = '';
if (isset($profile) && !empty($profile['photo']) && file_exists(UPLOAD_DIR_PHOTOS . '/' . basename($profile['photo']))) {
    $navPhoto = upload_url('photos', $profile['photo']);
}
?>
<header class="site-header">
  <div class="site-container">
    <div class="nav-pill-bar">
      
      <!-- Brand / Logo -->
      <a href="<?= BASE_URL ?>/index.php" class="nav-brand-pill" title="Ammar Syarif — Portofolio">
        <div class="brand-grid-icon" aria-hidden="true">AS</div>
        <span class="brand-name"><span class="brand-short">AS</span><span class="brand-full">Ammar Syarif</span></span>
      </a>

      <!-- Main Navigation Menu -->
      <nav class="nav-menu" id="navMenu">
        <a href="<?= $homePrefix ?>#hero" class="nav-pill <?= ($isHome) ? 'active' : '' ?>" title="Beranda" aria-label="Beranda">
          <svg class="nav-item-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/><path d="M9 21v-7h6v7"/></svg><span class="nav-label">Beranda</span>
        </a>
        <a href="<?= $homePrefix ?>#about" class="nav-pill" title="Tentang" aria-label="Tentang">
          <svg class="nav-item-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c.8-4 3.5-6 8-6s7.2 2 8 6"/></svg><span class="nav-label">Tentang</span>
        </a>
        <a href="<?= $homePrefix ?>#skills" class="nav-pill" title="Keahlian" aria-label="Keahlian">
          <svg class="nav-item-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V9m8 10V5m8 14v-7"/></svg><span class="nav-label">Keahlian</span>
        </a>
        <a href="<?= $homePrefix ?>#projects" class="nav-pill" title="Proyek" aria-label="Proyek">
          <svg class="nav-item-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18"/></svg><span class="nav-label">Proyek</span>
        </a>
        <a href="<?= BASE_URL ?>/pages/achievements.php" class="nav-pill <?= ($currentPage === 'achievements.php') ? 'active' : '' ?>" title="Pencapaian" aria-label="Pencapaian">
          <svg class="nav-item-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m8 3 4 3 4-3v8a4 4 0 0 1-8 0z"/><path d="M8 18H5m14 0h-3"/></svg><span class="nav-label">Pencapaian</span>
        </a>
        <a href="<?= BASE_URL ?>/pages/creations.php" class="nav-pill <?= ($currentPage === 'creations.php') ? 'active' : '' ?>" title="Kreasi" aria-label="Kreasi">
          <svg class="nav-item-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="m10 9 5 3-5 3z"/></svg><span class="nav-label">Kreasi</span>
        </a>
        <a href="<?= BASE_URL ?>/pages/guestbook.php" class="nav-pill <?= ($currentPage === 'guestbook.php') ? 'active' : '' ?>" title="Buku Tamu" aria-label="Buku Tamu">
          <svg class="nav-item-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5h14v11H9l-4 4z"/><path d="M8 9h8m-8 3h5"/></svg><span class="nav-label">Buku Tamu</span>
        </a>
        <a href="<?= BASE_URL ?>/pages/links.php" class="nav-pill <?= ($currentPage === 'links.php') ? 'active' : '' ?>" title="Tautan" aria-label="Tautan">
          <svg class="nav-item-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.1.1l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1"/><path d="M14 11a5 5 0 0 0-7.1-.1l-2 2A5 5 0 0 0 12 20l1.1-1.1"/></svg><span class="nav-label">Tautan</span>
        </a>
      </nav>

      <!-- Right Action Items (Avatar, Search, Direct Mail, + Rekrut Saya) -->
      <div class="nav-right-actions">
        
        <?php if (!empty($navPhoto)): ?>
          <a href="<?= $homePrefix ?>#about" class="nav-avatar-mini" title="Lihat Profil Ammar">
            <img src="<?= e($navPhoto) ?>" alt="Ammar Syarif">
          </a>
        <?php else: ?>
          <a href="<?= $homePrefix ?>#about" class="nav-avatar-mini placeholder" title="Lihat Profil Ammar">
            <span>AS</span>
          </a>
        <?php endif; ?>

        <!-- Command Palette Trigger (⌘K) -->
        <button type="button" class="btn-palette-trigger" id="paletteBtn" title="Buka Command Palette (Ctrl+K / ⌘K)">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <span class="kbd-shortcut">⌘K</span>
        </button>

        <!-- Mail Icon to Contact -->
        <a href="<?= $homePrefix ?>#contact" class="nav-icon-btn" title="Hubungi Saya">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
            <polyline points="22,6 12,13 2,6"/>
          </svg>
        </a>

        <!-- Rekrut Saya (Pill Button with "+") -->
        <a href="<?= $homePrefix ?>#contact" class="btn-hire">
          <span class="btn-hire-plus">+</span>
          <span class="btn-hire-text">Rekrut Saya</span>
        </a>

        <!-- Mobile Nav Toggle -->
        <button type="button" class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Buka menu navigasi" aria-expanded="false">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>

      </div>

    </div>
  </div>
</header>
