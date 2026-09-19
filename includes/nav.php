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
      
      <!-- Brand / Logo Grid Icon -->
      <a href="<?= BASE_URL ?>/index.php" class="nav-brand-pill" title="Ammar Syarif — Portofolio">
        <div class="brand-grid-icon">
          <span></span><span></span><span></span>
          <span></span><span></span><span></span>
          <span></span><span></span><span></span>
        </div>
        <span class="brand-name">AS</span>
      </a>

      <!-- Main Navigation Menu (Pills) -->
      <nav class="nav-menu" id="navMenu">
        <a href="<?= $homePrefix ?>#hero" class="nav-pill <?= ($isHome) ? 'active' : '' ?>">Beranda</a>
        <a href="<?= $homePrefix ?>#about" class="nav-pill">Tentang</a>
        <a href="<?= $homePrefix ?>#skills" class="nav-pill">Keahlian</a>
        <a href="<?= $homePrefix ?>#projects" class="nav-pill">Proyek</a>
        <a href="<?= BASE_URL ?>/pages/achievements.php" class="nav-pill <?= ($currentPage === 'achievements.php') ? 'active' : '' ?>">Pencapaian</a>
        <a href="<?= BASE_URL ?>/pages/creations.php" class="nav-pill <?= ($currentPage === 'creations.php') ? 'active' : '' ?>">Kreasi</a>
        <a href="<?= BASE_URL ?>/pages/guestbook.php" class="nav-pill <?= ($currentPage === 'guestbook.php') ? 'active' : '' ?>">Buku Tamu</a>
        <a href="<?= BASE_URL ?>/pages/links.php" class="nav-pill <?= ($currentPage === 'links.php') ? 'active' : '' ?>">Tautan</a>
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
