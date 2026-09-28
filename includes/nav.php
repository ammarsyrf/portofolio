<?php
/**
 * Global Navigation Header (Dipakai di seluruh halaman publik)
 * Desain: Modern Adaptive Floating Glass Pill (Bento & Modern Web Aesthetics)
 * Memuat navigasi utama, dropdown eksplorasi, avatar mini, tombol "Rekrut Saya", dan trigger Command Palette (⌘K)
 */

$currentPage = basename($_SERVER['PHP_SELF']);
$isHome = ($currentPage === 'index.php');
$homePrefix = $isHome ? '' : (BASE_URL . '/');

$isExplorActive = in_array($currentPage, ['achievements.php', 'creations.php', 'guestbook.php', 'links.php']);

// Fetch mini photo if exists
$navPhoto = '';
if (isset($profile) && !empty($profile['photo']) && file_exists(UPLOAD_DIR_PHOTOS . '/' . basename($profile['photo']))) {
    $navPhoto = upload_url('photos', $profile['photo']);
}

// Brand and Hire CTA dynamically configured
if (!isset($homeContent) && isset($pdo)) {
    if (!function_exists('get_homepage_content')) {
        require_once __DIR__ . '/homepage_content.php';
    }
    $homeContent = get_homepage_content($pdo);
}
$navBrandFull = !empty($homeContent['nav_brand_full']) ? $homeContent['nav_brand_full'] : (!empty($profile['full_name']) ? $profile['full_name'] : 'Ammar Syarif');
$navBrandShort = !empty($homeContent['nav_brand_short']) ? $homeContent['nav_brand_short'] : 'A//S';
$navHireText = !empty($homeContent['nav_hire_text']) ? $homeContent['nav_hire_text'] : 'Rekrut Saya';
?>
<header class="site-header" id="siteHeader">
  <div class="site-container">
    <div class="nav-pill-bar">
      
      <!-- Brand / Logo -->
      <a href="<?= BASE_URL ?>/" class="nav-brand-pill" title="<?= e($navBrandFull) ?> — Portfolio">
        <div class="brand-morph-wrap">
          <span class="brand-short"><?= e($navBrandShort) ?></span>
          <span class="brand-full"><?= e($navBrandFull) ?></span>
        </div>
      </a>

      <!-- Main Navigation Menu (Desktop & Tablet) -->
      <nav class="nav-menu" id="navMenu" aria-label="Navigasi Utama">
        <a href="<?= $homePrefix ?>#hero" class="nav-pill <?= ($isHome) ? 'active' : '' ?>" title="Beranda">
          <svg class="nav-item-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/><path d="M9 21v-7h6v7"/></svg>
          <span class="nav-label">Beranda</span>
        </a>
        <a href="<?= $homePrefix ?>#about" class="nav-pill" title="Tentang">
          <svg class="nav-item-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c.8-4 3.5-6 8-6s7.2 2 8 6"/></svg>
          <span class="nav-label">Tentang</span>
        </a>
        <a href="<?= $homePrefix ?>#skills" class="nav-pill" title="Keahlian">
          <svg class="nav-item-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19V9m8 10V5m8 14v-7"/></svg>
          <span class="nav-label">Keahlian</span>
        </a>
        <a href="<?= $homePrefix ?>#projects" class="nav-pill" title="Proyek">
          <svg class="nav-item-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18"/></svg>
          <span class="nav-label">Proyek</span>
        </a>

        <!-- Dropdown Menu: Eksplor / Lainnya -->
        <div class="nav-dropdown-wrap" id="navDropdownWrap">
          <button type="button" class="nav-pill nav-dropdown-btn <?= $isExplorActive ? 'active' : '' ?>" id="navDropdownBtn" aria-expanded="false" aria-haspopup="true">
            <svg class="nav-item-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/><circle cx="5" cy="12" r="1.5"/></svg>
            <span class="nav-label">Eksplor</span>
            <svg class="nav-chevron-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          
          <div class="nav-dropdown-menu" id="navDropdownMenu" role="menu">
            <div class="dropdown-arrow"></div>
            <a href="<?= $homePrefix ?>#github-showcase" class="dropdown-item" role="menuitem">
              <span class="dropdown-item-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true" fill="currentColor"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0 0 22 12.017C22 6.484 17.522 2 12 2z"/></svg>
              </span>
              <div class="dropdown-item-content">
                <span class="dropdown-item-title">GitHub Showcase</span>
                <span class="dropdown-item-desc">Repositori & aktivitas open-source</span>
              </div>
            </a>
            <a href="<?= BASE_URL ?>/achievements" class="dropdown-item <?= ($currentPage === 'achievements.php') ? 'active' : '' ?>" role="menuitem">
              <span class="dropdown-item-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m8 3 4 3 4-3v8a4 4 0 0 1-8 0z"/><path d="M8 18H5m14 0h-3"/></svg>
              </span>
              <div class="dropdown-item-content">
                <span class="dropdown-item-title">Pencapaian</span>
                <span class="dropdown-item-desc">Sertifikasi, penghargaan & milestone</span>
              </div>
            </a>
            <a href="<?= BASE_URL ?>/creations" class="dropdown-item <?= ($currentPage === 'creations.php') ? 'active' : '' ?>" role="menuitem">
              <span class="dropdown-item-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="m10 9 5 3-5 3z"/></svg>
              </span>
              <div class="dropdown-item-content">
                <span class="dropdown-item-title">Kreasi</span>
                <span class="dropdown-item-desc">Eksplorasi visual & side-projects</span>
              </div>
            </a>
            <a href="<?= BASE_URL ?>/guestbook" class="dropdown-item <?= ($currentPage === 'guestbook.php') ? 'active' : '' ?>" role="menuitem">
              <span class="dropdown-item-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 5h14v11H9l-4 4z"/><path d="M8 9h8m-8 3h5"/></svg>
              </span>
              <div class="dropdown-item-content">
                <span class="dropdown-item-title">Buku Tamu</span>
                <span class="dropdown-item-desc">Tinggalkan pesan & apresiasi</span>
              </div>
            </a>
            <a href="<?= BASE_URL ?>/links" class="dropdown-item <?= ($currentPage === 'links.php') ? 'active' : '' ?>" role="menuitem">
              <span class="dropdown-item-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.1.1l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1"/><path d="M14 11a5 5 0 0 0-7.1-.1l-2 2A5 5 0 0 0 12 20l1.1-1.1"/></svg>
              </span>
              <div class="dropdown-item-content">
                <span class="dropdown-item-title">Tautan</span>
                <span class="dropdown-item-desc">Media sosial & profil digital</span>
              </div>
            </a>
          </div>
        </div>
      </nav>

      <!-- Right Action Items (Avatar, Search, Direct Mail, + Rekrut Saya) -->
      <div class="nav-right-actions">
        
        <?php if (!empty($navPhoto)): ?>
          <a href="<?= $homePrefix ?>#about" class="nav-avatar-mini" title="Lihat Profil <?= e($navBrandFull) ?>">
            <img src="<?= e($navPhoto) ?>" alt="<?= e($navBrandFull) ?>">
          </a>
        <?php else: ?>
          <a href="<?= $homePrefix ?>#about" class="nav-avatar-mini placeholder" title="Lihat Profil <?= e($navBrandFull) ?>">
            <span><?= e(mb_substr($navBrandFull, 0, 2)) ?></span>
          </a>
        <?php endif; ?>

        <!-- Command Palette Trigger (⌘K) -->
        <button type="button" class="btn-palette-trigger" id="paletteBtn" title="Buka Command Palette (Ctrl+K / ⌘K)" aria-label="Buka Command Palette">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <span class="kbd-shortcut">⌘K</span>
        </button>

        <!-- Mail Icon to Contact -->
        <a href="<?= $homePrefix ?>#contact" class="nav-icon-btn" title="Hubungi Saya" aria-label="Hubungi Saya">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
            <polyline points="22,6 12,13 2,6"/>
          </svg>
        </a>

        <!-- Rekrut Saya (Pill Button with "+") -->
        <a href="<?= $homePrefix ?>#contact" class="btn-hire" title="Hubungi <?= e($navBrandFull) ?>">
          <span class="btn-hire-plus">+</span>
          <span class="btn-hire-text"><?= e($navHireText) ?></span>
        </a>

        <!-- Mobile Nav Toggle -->
        <button type="button" class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="mobileNavDrawer">
          <span class="toggle-bar"></span>
          <span class="toggle-bar"></span>
          <span class="toggle-bar"></span>
        </button>

      </div>

    </div>

    <!-- Mobile Navigation Drawer Panel -->
    <div class="mobile-nav-drawer" id="mobileNavDrawer" aria-hidden="true">
      <div class="mobile-drawer-inner">
        
        <div class="mobile-drawer-section">
          <div class="mobile-drawer-label">Navigasi Utama</div>
          <div class="mobile-drawer-grid">
            <a href="<?= $homePrefix ?>#hero" class="mobile-nav-link <?= ($isHome) ? 'active' : '' ?>">
              <svg class="nav-item-icon" viewBox="0 0 24 24"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/><path d="M9 21v-7h6v7"/></svg>
              <span>Beranda</span>
            </a>
            <a href="<?= $homePrefix ?>#about" class="mobile-nav-link">
              <svg class="nav-item-icon" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c.8-4 3.5-6 8-6s7.2 2 8 6"/></svg>
              <span>Tentang</span>
            </a>
            <a href="<?= $homePrefix ?>#skills" class="mobile-nav-link">
              <svg class="nav-item-icon" viewBox="0 0 24 24"><path d="M4 19V9m8 10V5m8 14v-7"/></svg>
              <span>Keahlian</span>
            </a>
            <a href="<?= $homePrefix ?>#projects" class="mobile-nav-link">
              <svg class="nav-item-icon" viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18"/></svg>
              <span>Proyek</span>
            </a>
            <a href="<?= $homePrefix ?>#github-showcase" class="mobile-nav-link">
              <svg class="nav-item-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0 0 22 12.017C22 6.484 17.522 2 12 2z"/></svg>
              <span>GitHub</span>
            </a>
          </div>
        </div>

        <div class="mobile-drawer-section">
          <div class="mobile-drawer-label">Halaman & Eksplorasi</div>
          <div class="mobile-drawer-list">
            <a href="<?= BASE_URL ?>/achievements" class="mobile-sub-link <?= ($currentPage === 'achievements.php') ? 'active' : '' ?>">
              <span class="sub-link-icon"><svg viewBox="0 0 24 24"><path d="m8 3 4 3 4-3v8a4 4 0 0 1-8 0z"/><path d="M8 18H5m14 0h-3"/></svg></span>
              <div class="sub-link-text">
                <span class="sub-link-title">Pencapaian</span>
                <span class="sub-link-sub">Sertifikasi & milestone</span>
              </div>
            </a>
            <a href="<?= BASE_URL ?>/creations" class="mobile-sub-link <?= ($currentPage === 'creations.php') ? 'active' : '' ?>">
              <span class="sub-link-icon"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="m10 9 5 3-5 3z"/></svg></span>
              <div class="sub-link-text">
                <span class="sub-link-title">Kreasi</span>
                <span class="sub-link-sub">Eksplorasi visual & side-projects</span>
              </div>
            </a>
            <a href="<?= BASE_URL ?>/guestbook" class="mobile-sub-link <?= ($currentPage === 'guestbook.php') ? 'active' : '' ?>">
              <span class="sub-link-icon"><svg viewBox="0 0 24 24"><path d="M5 5h14v11H9l-4 4z"/><path d="M8 9h8m-8 3h5"/></svg></span>
              <div class="sub-link-text">
                <span class="sub-link-title">Buku Tamu</span>
                <span class="sub-link-sub">Tinggalkan pesan</span>
              </div>
            </a>
            <a href="<?= BASE_URL ?>/links" class="mobile-sub-link <?= ($currentPage === 'links.php') ? 'active' : '' ?>">
              <span class="sub-link-icon"><svg viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.1.1l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1"/><path d="M14 11a5 5 0 0 0-7.1-.1l-2 2A5 5 0 0 0 12 20l1.1-1.1"/></svg></span>
              <div class="sub-link-text">
                <span class="sub-link-title">Tautan</span>
                <span class="sub-link-sub">Media sosial & direktori link</span>
              </div>
            </a>
          </div>
        </div>

        <div class="mobile-drawer-footer">
          <a href="<?= $homePrefix ?>#contact" class="mobile-drawer-cta">
            <span>Hubungi &amp; <?= e($navHireText) ?></span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
          </a>
        </div>

      </div>
    </div>

  </div>
</header>
