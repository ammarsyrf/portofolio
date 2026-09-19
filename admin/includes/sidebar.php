<?php
/**
 * Admin Layout: Sidebar
 * Updated: Seluruh Modul (Projects, Achievements, Creations, Links, Guestbook)
 */
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-brand">
    <div class="brand-wrapper">
      <div class="brand-logo" style="width: 28px; height: 28px; font-size: 0.85rem;">AS</div>
      <span class="brand-name" style="font-size: var(--text-sm);">Portal Admin</span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-title">Ringkasan</div>

    <a href="<?= BASE_URL ?>/admin/dashboard.php" class="sidebar-link <?= ($currentPage === 'dashboard.php') ? 'active' : '' ?>">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="7" height="7"></rect>
        <rect x="14" y="3" width="7" height="7"></rect>
        <rect x="14" y="14" width="7" height="7"></rect>
        <rect x="3" y="14" width="7" height="7"></rect>
      </svg>
      Dashboard
    </a>

    <a href="<?= BASE_URL ?>/admin/profile.php" class="sidebar-link <?= ($currentPage === 'profile.php') ? 'active' : '' ?>">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
        <circle cx="12" cy="7" r="4"></circle>
      </svg>
      Profil, Foto & CV
    </a>

    <div class="nav-section-title">Konten Portofolio</div>

    <a href="<?= BASE_URL ?>/admin/projects.php" class="sidebar-link <?= ($currentPage === 'projects.php' || $currentPage === 'project_form.php') ? 'active' : '' ?>">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
      </svg>
      Kelola Proyek
    </a>

    <a href="<?= BASE_URL ?>/admin/achievements.php" class="sidebar-link <?= ($currentPage === 'achievements.php' || $currentPage === 'achievement_form.php') ? 'active' : '' ?>">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="8" r="7"></circle>
        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
      </svg>
      Pencapaian & Sertifikat
    </a>

    <a href="<?= BASE_URL ?>/admin/creations.php" class="sidebar-link <?= ($currentPage === 'creations.php' || $currentPage === 'creation_form.php') ? 'active' : '' ?>">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polygon points="23 7 16 12 23 17 23 7"></polygon>
        <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
      </svg>
      Kreasi TikTok & IG
    </a>

    <a href="<?= BASE_URL ?>/admin/links.php" class="sidebar-link <?= ($currentPage === 'links.php' || $currentPage === 'link_form.php') ? 'active' : '' ?>">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
      </svg>
      Tautan (Links)
    </a>

    <div class="nav-section-title">Interaksi Publik</div>

    <a href="<?= BASE_URL ?>/admin/guestbook.php" class="sidebar-link <?= ($currentPage === 'guestbook.php') ? 'active' : '' ?>">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
      </svg>
      Moderasi Buku Tamu
    </a>

    <div class="nav-section-title">Tautan Keluar</div>

    <a href="<?= BASE_URL ?>/index.php" target="_blank" class="sidebar-link">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
        <polyline points="15 3 21 3 21 9"></polyline>
        <line x1="10" y1="14" x2="21" y2="3"></line>
      </svg>
      Lihat Website Publik
    </a>
  </nav>

  <div class="sidebar-footer">
    <div style="font-size: var(--text-xs); color: var(--color-text-faint);">
      Login sebagai: <strong style="color: var(--color-text);"><?= e($_SESSION['admin_username'] ?? 'Admin') ?></strong>
    </div>
    <a href="<?= BASE_URL ?>/admin/logout.php" class="btn btn-secondary btn-sm" style="width: 100%; text-align: center;">
      Keluar (Logout)
    </a>
  </div>
</aside>

<div class="admin-main">
  <header class="admin-topbar">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <button type="button" class="sidebar-toggle-mobile" id="sidebarToggle" aria-label="Toggle Sidebar Menu">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>
      <h1 class="topbar-title"><?= e($pageTitle ?? 'Portal Admin') ?></h1>
    </div>

    <div class="topbar-actions">
      <a href="<?= BASE_URL ?>/index.php" target="_blank" class="btn btn-secondary btn-sm">
        Pratinjau Web &nearr;
      </a>
    </div>
  </header>

  <main class="admin-content-area">
    <?php
    $flash = get_flash();
    if ($flash): ?>
      <div class="flash-alert flash-<?= e($flash['type']) ?>">
        <div><?= e($flash['message']) ?></div>
      </div>
    <?php endif; ?>
