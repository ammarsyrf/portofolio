<?php
/**
 * Admin Layout: Sidebar + Topbar (Collapsible)
 */
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<style>
  /* ── Sidebar Collapse ── */
  .admin-sidebar {
    width: 260px;
    transition: width 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    position: fixed;
    left: 0; top: 0; bottom: 0;
    z-index: 50;
    background-color: var(--color-bg-alt);
    border-right: 1px solid var(--color-line);
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
  }
  .admin-sidebar.collapsed {
    width: 62px;
  }

  /* Teks / label yang hilang saat collapsed */
  .sb-label {
    transition: opacity 0.15s ease, max-width 0.25s ease;
    opacity: 1;
    max-width: 200px;
    overflow: hidden;
    white-space: nowrap;
  }
  .admin-sidebar.collapsed .sb-label {
    opacity: 0;
    max-width: 0;
  }

  /* Section title */
  .nav-section-title {
    transition: opacity 0.15s ease, height 0.25s ease, margin 0.25s ease, padding 0.25s ease;
    overflow: hidden;
  }
  .admin-sidebar.collapsed .nav-section-title {
    opacity: 0;
    height: 0 !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  /* Brand */
  .sidebar-brand {
    padding: 1.25rem 1rem;
    border-bottom: 1px solid var(--color-line);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
  }
  .brand-wrapper {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    overflow: hidden;
  }
  .brand-logo {
    width: 28px; height: 28px; font-size: 0.85rem;
    background: var(--color-accent);
    color: #fff;
    border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700;
    flex-shrink: 0;
  }
  .brand-name { font-size: var(--text-sm); font-weight: 600; color: var(--color-text); }

  /* Collapse toggle btn inside sidebar */
  #sbCollapseBtn {
    background: none;
    border: 1px solid var(--color-line);
    color: var(--color-text-dim);
    border-radius: 4px;
    padding: 0.25rem 0.4rem;
    cursor: pointer;
    flex-shrink: 0;
    transition: background 0.15s;
    display: flex;
    align-items: center;
  }
  #sbCollapseBtn:hover { background: var(--color-bg-surface); color: var(--color-text); }
  .admin-sidebar.collapsed #sbCollapseBtn svg {
    transform: rotate(180deg);
  }
  #sbCollapseBtn svg { transition: transform 0.28s ease; }

  /* Nav */
  .sidebar-nav {
    padding: 1rem 0.65rem;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    flex-grow: 1;
    overflow-y: auto;
    overflow-x: hidden;
  }
  .nav-section-title {
    font-size: 0.65rem;
    color: var(--color-accent);
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin: 0.9rem 0.6rem 0.4rem;
    font-weight: 700;
  }
  .sidebar-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.65rem 0.75rem;
    color: var(--color-text-dim);
    border-radius: 6px;
    font-size: var(--text-sm);
    font-weight: 500;
    border: 1px solid transparent;
    transition: all 0.15s ease;
    white-space: nowrap;
    text-decoration: none;
    position: relative;
  }
  .sidebar-link svg { flex-shrink: 0; }
  .sidebar-link:hover {
    background-color: var(--color-bg-surface);
    color: var(--color-text);
    border-color: var(--color-line);
  }
  .sidebar-link.active {
    background-color: var(--color-bg-surface);
    color: var(--color-accent-bright);
    border-color: rgba(76,141,255,0.3);
  }

  /* Tooltip saat collapsed */
  .admin-sidebar.collapsed .sidebar-link[data-tip]:hover::after {
    content: attr(data-tip);
    position: absolute;
    left: calc(100% + 8px);
    top: 50%;
    transform: translateY(-50%);
    background: rgba(10,15,25,0.97);
    color: #fff;
    font-size: 0.75rem;
    padding: 0.3rem 0.75rem;
    border-radius: 6px;
    white-space: nowrap;
    z-index: 9999;
    pointer-events: none;
    box-shadow: 0 4px 14px rgba(0,0,0,0.4);
    border: 1px solid rgba(76,141,255,0.2);
  }

  /* Footer sidebar */
  .sidebar-footer {
    padding: 0.9rem 0.75rem;
    border-top: 1px solid var(--color-line);
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    flex-shrink: 0;
    overflow: hidden;
  }
  .sidebar-footer-user {
    font-size: var(--text-xs);
    color: var(--color-text-faint);
    transition: opacity 0.15s, max-height 0.25s;
    overflow: hidden;
    max-height: 30px;
    white-space: nowrap;
  }
  .admin-sidebar.collapsed .sidebar-footer-user {
    opacity: 0;
    max-height: 0;
  }

  /* Main content padding */
  .admin-main {
    flex-grow: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    margin-left: 260px;
    transition: margin-left 0.28s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .admin-main.collapsed {
    margin-left: 62px;
  }

  /* Topbar */
  .admin-topbar {
    height: 4rem;
    background-color: rgba(18,22,31,0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--color-line);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
    position: sticky;
    top: 0;
    z-index: 40;
  }
  .topbar-left { display: flex; align-items: center; gap: 0.75rem; }
  .topbar-title {
    font-family: var(--font-display);
    font-size: var(--text-lg);
    color: var(--color-text);
    font-weight: 600;
  }

  /* Mobile */
  @media (max-width: 900px) {
    .admin-sidebar {
      left: -260px;
      width: 260px !important;
      transition: left 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .admin-sidebar.mobile-open {
      left: 0;
    }
    .admin-sidebar.collapsed { left: -260px; }
    .admin-main { margin-left: 0 !important; }
    #sbOverlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 49; }
    #sbOverlay.show { display: block; }
    #sbDesktopToggle { display: none !important; }
    .sidebar-toggle-mobile { display: inline-flex !important; }
  }

  .sidebar-toggle-mobile {
    display: none;
    background: none;
    border: 1px solid var(--color-line);
    color: var(--color-text);
    padding: 0.35rem 0.45rem;
    border-radius: 4px;
    cursor: pointer;
    align-items: center;
  }
</style>

<!-- Overlay (mobile) -->
<div id="sbOverlay" onclick="closeMobileSidebar()"></div>

<aside class="admin-sidebar" id="adminSidebar">
  <!-- Brand + Collapse btn -->
  <div class="sidebar-brand">
    <div class="brand-wrapper">
      <div class="brand-logo">AS</div>
      <span class="brand-name sb-label">Portal Admin</span>
    </div>
    <button id="sbCollapseBtn" onclick="toggleDesktopSidebar()" title="Collapse sidebar">
      <!-- Chevron left icon -->
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="15 18 9 12 15 6"></polyline>
      </svg>
    </button>
  </div>

  <!-- Navigation -->
  <nav class="sidebar-nav">
    <div class="nav-section-title">Ringkasan</div>

    <a href="<?= BASE_URL ?>/admin/dashboard" data-tip="Dashboard"
       class="sidebar-link <?= ($currentPage === 'dashboard.php') ? 'active' : '' ?>">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect>
        <rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect>
      </svg>
      <span class="sb-label">Dashboard</span>
    </a>

    <a href="<?= BASE_URL ?>/admin/profile" data-tip="Profil"
       class="sidebar-link <?= ($currentPage === 'profile.php') ? 'active' : '' ?>">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
      </svg>
      <span class="sb-label">Profil, Foto &amp; CV</span>
    </a>

    <a href="<?= BASE_URL ?>/admin/homepage" data-tip="Konten Utama"
       class="sidebar-link <?= ($currentPage === 'homepage.php') ? 'active' : '' ?>">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="m3 11 9-8 9 8v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Z"></path><path d="M9 22v-7h6v7"></path>
      </svg>
      <span class="sb-label">Konten Halaman Utama</span>
    </a>

    <div class="nav-section-title">Konten Portofolio</div>

    <a href="<?= BASE_URL ?>/admin/projects" data-tip="Proyek"
       class="sidebar-link <?= in_array($currentPage, ['projects.php','project_form.php']) ? 'active' : '' ?>">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
      </svg>
      <span class="sb-label">Kelola Proyek</span>
    </a>

    <a href="<?= BASE_URL ?>/admin/achievements" data-tip="Pencapaian"
       class="sidebar-link <?= in_array($currentPage, ['achievements.php','achievement_form.php']) ? 'active' : '' ?>">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="8" r="7"></circle>
        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
      </svg>
      <span class="sb-label">Pencapaian &amp; Sertifikat</span>
    </a>

    <a href="<?= BASE_URL ?>/admin/creations" data-tip="Kreasi"
       class="sidebar-link <?= in_array($currentPage, ['creations.php','creation_form.php']) ? 'active' : '' ?>">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polygon points="23 7 16 12 23 17 23 7"></polygon>
        <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
      </svg>
      <span class="sb-label">Kreasi TikTok &amp; IG</span>
    </a>

    <a href="<?= BASE_URL ?>/admin/links" data-tip="Tautan"
       class="sidebar-link <?= in_array($currentPage, ['links.php','link_form.php']) ? 'active' : '' ?>">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
      </svg>
      <span class="sb-label">Tautan (Links)</span>
    </a>

    <a href="<?= BASE_URL ?>/admin/media" data-tip="Media & Assets"
       class="sidebar-link <?= ($currentPage === 'media.php') ? 'active' : '' ?>">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
        <circle cx="8.5" cy="8.5" r="1.5"></circle>
        <polyline points="21 15 16 10 5 21"></polyline>
      </svg>
      <span class="sb-label">Galeri Media &amp; Assets</span>
    </a>

    <div class="nav-section-title">Interaksi Publik</div>

    <a href="<?= BASE_URL ?>/admin/guestbook" data-tip="Buku Tamu"
       class="sidebar-link <?= ($currentPage === 'guestbook.php') ? 'active' : '' ?>">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
      </svg>
      <span class="sb-label">Moderasi Buku Tamu</span>
    </a>

    <div class="nav-section-title">Tautan Keluar</div>

    <a href="<?= BASE_URL ?>/" target="_blank" data-tip="Website Publik" class="sidebar-link">
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
        <polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line>
      </svg>
      <span class="sb-label">Lihat Website Publik</span>
    </a>
  </nav>

  <!-- Footer -->
  <div class="sidebar-footer">
    <div class="sidebar-footer-user">
      Login sebagai: <strong style="color: var(--color-text);"><?= e($_SESSION['admin_username'] ?? 'Admin') ?></strong>
    </div>
    <a href="<?= BASE_URL ?>/admin/logout" class="btn btn-secondary btn-sm" style="text-align:center; justify-content:center;">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
        <polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line>
      </svg>
      <span class="sb-label">Keluar</span>
    </a>
  </div>
</aside>

<div class="admin-main" id="adminMain">
  <header class="admin-topbar">
    <div class="topbar-left">
      <!-- Mobile toggle -->
      <button type="button" class="sidebar-toggle-mobile" id="sidebarToggle" aria-label="Toggle Sidebar" onclick="toggleMobileSidebar()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>
      <h1 class="topbar-title"><?= e($pageTitle ?? 'Portal Admin') ?></h1>
    </div>

    <!-- Quick Command Palette Trigger Button in Topbar -->
    <div class="topbar-actions" style="display: flex; align-items: center; gap: 0.75rem;">
      <button type="button" class="btn-cmd-trigger" onclick="openCmdPalette()" title="Buka Command Palette (Ctrl + K / ⌘K)">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <span class="cmd-text-label">Cari aksi atau menu...</span>
        <kbd class="cmd-shortcut-badge">⌘K</kbd>
      </button>

      <span style="font-size:0.72rem; color: var(--color-text-faint); display:none;" id="topbarDate"><?= date('D, d M Y') ?></span>
      <a href="<?= BASE_URL ?>/" target="_blank" class="btn btn-secondary btn-sm">Pratinjau &nearr;</a>
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

<!-- =========================================================================
     ADMIN QUICK COMMAND PALETTE MODAL (Ctrl + K / ⌘K)
     ========================================================================= -->
<style>
  .btn-cmd-trigger {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    background: var(--color-bg-surface);
    border: 1px solid var(--color-line);
    color: var(--color-text-dim);
    padding: 0.4rem 0.85rem;
    border-radius: 6px;
    font-size: 0.78rem;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .btn-cmd-trigger:hover {
    border-color: rgba(76, 141, 255, 0.5);
    color: var(--color-text);
    background: rgba(76, 141, 255, 0.08);
  }
  .cmd-shortcut-badge {
    background: var(--color-bg-alt);
    border: 1px solid var(--color-line);
    color: var(--color-accent-bright);
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.15rem 0.4rem;
    border-radius: 4px;
    font-family: inherit;
  }
  @media (max-width: 680px) {
    .cmd-text-label { display: none; }
  }

  /* Command Palette Modal */
  .cmd-palette-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(5, 8, 15, 0.82);
    backdrop-filter: blur(10px);
    z-index: 99999;
    align-items: flex-start;
    justify-content: center;
    padding: 10vh 1rem 2rem;
  }
  .cmd-palette-backdrop.is-open {
    display: flex;
  }
  .cmd-palette-box {
    width: 100%;
    max-width: 620px;
    background: #0d121d;
    border: 1px solid rgba(76, 141, 255, 0.35);
    border-radius: 12px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6), 0 0 30px rgba(76, 141, 255, 0.15);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    animation: cmdSlideDown 0.18s cubic-bezier(0.16, 1, 0.3, 1);
  }
  @keyframes cmdSlideDown {
    from { opacity: 0; transform: translateY(-12px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
  }
  .cmd-input-wrap {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--color-line);
    background: rgba(18, 24, 38, 0.7);
  }
  .cmd-input-wrap svg {
    color: var(--color-accent-bright);
    flex-shrink: 0;
  }
  .cmd-search-input {
    width: 100%;
    background: transparent;
    border: none;
    color: #fff;
    font-size: 0.95rem;
    font-family: inherit;
    outline: none;
  }
  .cmd-search-input::placeholder {
    color: var(--color-text-faint);
  }
  .cmd-list-container {
    max-height: 380px;
    overflow-y: auto;
    padding: 0.65rem 0.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
  }
  .cmd-group-title {
    font-size: 0.65rem;
    font-weight: 700;
    color: var(--color-accent-bright);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 0.5rem 0.75rem 0.25rem;
  }
  .cmd-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.65rem 0.85rem;
    border-radius: 6px;
    color: var(--color-text-dim);
    text-decoration: none;
    cursor: pointer;
    font-size: 0.84rem;
    transition: all 0.1s;
    border: 1px solid transparent;
  }
  .cmd-item:hover, .cmd-item.is-selected {
    background: rgba(76, 141, 255, 0.15);
    color: #fff;
    border-color: rgba(76, 141, 255, 0.4);
  }
  .cmd-item-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  .cmd-item-icon {
    font-size: 1.1rem;
    width: 24px;
    text-align: center;
    flex-shrink: 0;
  }
  .cmd-item-title {
    font-weight: 500;
  }
  .cmd-item-desc {
    font-size: 0.72rem;
    color: var(--color-text-faint);
  }
  .cmd-item-badge {
    font-size: 0.68rem;
    padding: 0.15rem 0.45rem;
    border-radius: 4px;
    background: var(--color-bg-surface);
    border: 1px solid var(--color-line);
    color: var(--color-text-faint);
  }
  .cmd-footer-bar {
    padding: 0.65rem 1.25rem;
    border-top: 1px solid var(--color-line);
    background: rgba(13, 18, 29, 0.95);
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.72rem;
    color: var(--color-text-faint);
  }
  .cmd-footer-hints {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  .cmd-footer-hints span {
    display: flex;
    align-items: center;
    gap: 0.35rem;
  }
  .cmd-toast {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    background: #10b981;
    color: #fff;
    padding: 0.75rem 1.25rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    z-index: 100000;
    display: none;
    animation: toastFadeIn 0.2s ease;
  }
  @keyframes toastFadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>

<!-- Modal Elements -->
<div class="cmd-palette-backdrop" id="adminCmdPaletteModal" onclick="closeCmdPalette()">
  <div class="cmd-palette-box" onclick="event.stopPropagation();">
    <div class="cmd-input-wrap">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input type="text" id="cmdPaletteSearchInput" class="cmd-search-input" placeholder="Ketik aksi atau menu (misal: tambah proyek, backup, matriks)..." autocomplete="off">
      <kbd class="cmd-shortcut-badge" style="cursor:pointer;" onclick="closeCmdPalette()">ESC</kbd>
    </div>

    <div class="cmd-list-container" id="cmdPaletteList">
      
      <!-- Group: Aksi Cepat -->
      <div class="cmd-group" data-group="quick">
        <div class="cmd-group-title">⚡ Aksi Cepat</div>

        <div class="cmd-item" data-keywords="ganti status ketersediaan kerja freelance hire available busy" onclick="toggleHireStatusQuick()">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">🟢</span>
            <div>
              <div class="cmd-item-title">Ganti Status Ketersediaan Kerja</div>
              <div class="cmd-item-desc">Toggle status ketersediaan (Siap Kerja / Sibuk) instan</div>
            </div>
          </div>
          <span class="cmd-item-badge">Instant AJAX</span>
        </div>

        <a href="<?= BASE_URL ?>/admin/backup_db.php" class="cmd-item" data-keywords="backup download database sql snapshot export simpan dump">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">💾</span>
            <div>
              <div class="cmd-item-title">Download Backup Database SQL</div>
              <div class="cmd-item-desc">Unduh snapshot database lengkap (.sql) untuk backup</div>
            </div>
          </div>
          <span class="cmd-item-badge">Export SQL</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/project_form.php" class="cmd-item" data-keywords="tambah proyek baru add project create">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">➕</span>
            <div>
              <div class="cmd-item-title">Tambah Proyek Baru</div>
              <div class="cmd-item-desc">Buat dan publikasikan entri portofolio proyek baru</div>
            </div>
          </div>
          <span class="cmd-item-badge">Formulir</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/achievement_form.php" class="cmd-item" data-keywords="tambah pencapaian sertifikat piagam baru add achievement">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">🏆</span>
            <div>
              <div class="cmd-item-title">Tambah Pencapaian &amp; Sertifikat</div>
              <div class="cmd-item-desc">Unggah piagam atau sertifikasi baru</div>
            </div>
          </div>
          <span class="cmd-item-badge">Formulir</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/creation_form.php" class="cmd-item" data-keywords="tambah kreasi tiktok instagram video konten add creation">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">🎬</span>
            <div>
              <div class="cmd-item-title">Tambah Kreasi TikTok &amp; IG</div>
              <div class="cmd-item-desc">Tambah karya visual atau video edukasi</div>
            </div>
          </div>
          <span class="cmd-item-badge">Formulir</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/media.php" class="cmd-item" data-keywords="galeri media upload assets foto gambar webp drag drop">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">🖼️</span>
            <div>
              <div class="cmd-item-title">Galeri Media &amp; Upload Assets</div>
              <div class="cmd-item-desc">Kelola file media, upload drag &amp; drop, auto-WebP</div>
            </div>
          </div>
          <span class="cmd-item-badge">Media Manager</span>
        </a>
      </div>

      <!-- Group: Konten Beranda -->
      <div class="cmd-group" data-group="homepage">
        <div class="cmd-group-title">🧭 Editor Konten Beranda</div>

        <a href="<?= BASE_URL ?>/admin/homepage.php#nav" class="cmd-item" data-keywords="konten beranda navigasi brand header logo">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">🧭</span>
            <div>
              <div class="cmd-item-title">Edit Navigasi &amp; Brand</div>
              <div class="cmd-item-desc">Nama brand, tombol rekrut, inisial</div>
            </div>
          </div>
          <span class="cmd-item-badge">Beranda</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/homepage.php#hero" class="cmd-item" data-keywords="konten beranda hero terminal pendidikan badge teks headline">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">💻</span>
            <div>
              <div class="cmd-item-title">Edit Hero &amp; Terminal Mockup</div>
              <div class="cmd-item-desc">Headline hero, badge, mock shell code &amp; status</div>
            </div>
          </div>
          <span class="cmd-item-badge">Beranda</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/homepage.php#domains" class="cmd-item" data-keywords="konten beranda domain keahlian frontend backend database devops mobile data analitik 6 kartu skills">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">🎨</span>
            <div>
              <div class="cmd-item-title">Edit Domain Keahlian (6 Kartu)</div>
              <div class="cmd-item-desc">Subgroup skill tags, highlight poin &amp; ekosistem</div>
            </div>
          </div>
          <span class="cmd-item-badge">Beranda</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/homepage.php#matrix" class="cmd-item" data-keywords="konten beranda matriks keahlian tier 1 2 3 progress kompetensi">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">⚡</span>
            <div>
              <div class="cmd-item-title">Edit Matriks Keahlian</div>
              <div class="cmd-item-desc">Tingkat kemahiran Tier 1 Advanced, 2 Proficient, 3 Familiar</div>
            </div>
          </div>
          <span class="cmd-item-badge">Beranda</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/homepage.php#deck" class="cmd-item" data-keywords="konten beranda cara kerja standar rekayasa siap kolaborasi deck panel">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">🎯</span>
            <div>
              <div class="cmd-item-title">Edit Alur Kerja &amp; Standar (Deck)</div>
              <div class="cmd-item-desc">Visi kerja 3 langkah, pilar rekayasa &amp; kolaborasi</div>
            </div>
          </div>
          <span class="cmd-item-badge">Beranda</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/homepage.php#contact" class="cmd-item" data-keywords="konten beranda kontak email linkedin github instagram tiktok oauth google">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">📬</span>
            <div>
              <div class="cmd-item-title">Edit Kontak &amp; Integrasi OAuth</div>
              <div class="cmd-item-desc">Sosial media, email, dan Google Client ID OAuth</div>
            </div>
          </div>
          <span class="cmd-item-badge">Beranda</span>
        </a>
      </div>

      <!-- Group: Navigasi Menu -->
      <div class="cmd-group" data-group="pages">
        <div class="cmd-group-title">📂 Menu Halaman Admin</div>

        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="cmd-item" data-keywords="dashboard beranda ringkasan telemetry statistik visitor">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">🚀</span>
            <div>
              <div class="cmd-item-title">Dashboard Ringkasan</div>
              <div class="cmd-item-desc">Analitik pengunjung, keamanan &amp; performa server</div>
            </div>
          </div>
          <span class="cmd-item-badge">Halaman</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/profile.php" class="cmd-item" data-keywords="profil foto cv biodata kontak resume">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">👤</span>
            <div>
              <div class="cmd-item-title">Profil, Foto &amp; CV</div>
              <div class="cmd-item-desc">Data pribadi, bio, upload foto avatar &amp; PDF resume</div>
            </div>
          </div>
          <span class="cmd-item-badge">Halaman</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/projects.php" class="cmd-item" data-keywords="kelola proyek daftar list karya">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">📁</span>
            <div>
              <div class="cmd-item-title">Kelola Semua Proyek</div>
              <div class="cmd-item-desc">Daftar proyek, urutan, status publikasi &amp; edit</div>
            </div>
          </div>
          <span class="cmd-item-badge">Halaman</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/guestbook.php" class="cmd-item" data-keywords="moderasi buku tamu pesan komentar guestbook">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">💬</span>
            <div>
              <div class="cmd-item-title">Moderasi Buku Tamu</div>
              <div class="cmd-item-desc">Setujui, sembunyikan, atau hapus ucapan dari pengunjung</div>
            </div>
          </div>
          <span class="cmd-item-badge">Halaman</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/links.php" class="cmd-item" data-keywords="kelola tautan links bio linktree">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">🔗</span>
            <div>
              <div class="cmd-item-title">Kelola Tautan (Links)</div>
              <div class="cmd-item-desc">Daftar tautan media sosial dan eksternal</div>
            </div>
          </div>
          <span class="cmd-item-badge">Halaman</span>
        </a>
      </div>

      <!-- Group: Eksternal & Akun -->
      <div class="cmd-group" data-group="ext">
        <div class="cmd-group-title">🌐 Eksternal &amp; Akun</div>

        <a href="<?= BASE_URL ?>/" target="_blank" class="cmd-item" data-keywords="lihat website publik preview frontend beranda">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">🌐</span>
            <div>
              <div class="cmd-item-title">Lihat Website Publik ↗</div>
              <div class="cmd-item-desc">Buka halaman beranda publik di tab baru</div>
            </div>
          </div>
          <span class="cmd-item-badge">Publik</span>
        </a>

        <a href="<?= BASE_URL ?>/admin/logout.php" class="cmd-item" data-keywords="keluar logout sign out admin">
          <div class="cmd-item-left">
            <span class="cmd-item-icon">🚪</span>
            <div>
              <div class="cmd-item-title">Keluar (Logout)</div>
              <div class="cmd-item-desc">Akhiri sesi portal admin saat ini</div>
            </div>
          </div>
          <span class="cmd-item-badge">Sesi</span>
        </a>
      </div>

    </div>

    <div class="cmd-footer-bar">
      <div class="cmd-footer-hints">
        <span><kbd class="cmd-shortcut-badge">↑</kbd> <kbd class="cmd-shortcut-badge">↓</kbd> Navigasi</span>
        <span><kbd class="cmd-shortcut-badge">ENTER</kbd> Pilih</span>
        <span><kbd class="cmd-shortcut-badge">ESC</kbd> Tutup</span>
      </div>
      <div>Portal Admin Ammar Syarif</div>
    </div>
  </div>
</div>

<div class="cmd-toast" id="cmdToast"></div>

<script>
(function () {
  const KEY       = 'porto_sb_collapsed';
  const sidebar   = document.getElementById('adminSidebar');
  const main      = document.getElementById('adminMain');
  const isMobile  = () => window.innerWidth <= 900;

  // Init state (desktop only)
  if (!isMobile() && localStorage.getItem(KEY) === 'true') {
    sidebar.classList.add('collapsed');
    main.classList.add('collapsed');
  }

  window.toggleDesktopSidebar = function () {
    if (isMobile()) return;
    const collapsed = sidebar.classList.toggle('collapsed');
    main.classList.toggle('collapsed', collapsed);
    localStorage.setItem(KEY, collapsed);
  };

  window.toggleMobileSidebar = function () {
    sidebar.classList.toggle('mobile-open');
    document.getElementById('sbOverlay').classList.toggle('show');
  };

  window.closeMobileSidebar = function () {
    sidebar.classList.remove('mobile-open');
    document.getElementById('sbOverlay').classList.remove('show');
  };

  // Show date on topbar when wide enough
  const dateEl = document.getElementById('topbarDate');
  if (window.innerWidth > 640 && dateEl) dateEl.style.display = 'block';

  // ── COMMAND PALETTE LOGIC ──
  const cmdModal = document.getElementById('adminCmdPaletteModal');
  const cmdInput = document.getElementById('cmdPaletteSearchInput');
  const cmdList  = document.getElementById('cmdPaletteList');

  window.openCmdPalette = function() {
    cmdModal.classList.add('is-open');
    cmdInput.value = '';
    filterCmdItems('');
    setTimeout(() => cmdInput.focus(), 50);
  };

  window.closeCmdPalette = function() {
    cmdModal.classList.remove('is-open');
  };

  // Global Keyboard Shortcut (Ctrl+K or Cmd+K, Escape)
  window.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
      e.preventDefault();
      if (cmdModal.classList.contains('is-open')) {
        closeCmdPalette();
      } else {
        openCmdPalette();
      }
    } else if (e.key === 'Escape' && cmdModal.classList.contains('is-open')) {
      closeCmdPalette();
    }
  });

  // Filter items
  cmdInput.addEventListener('input', function() {
    filterCmdItems(this.value.toLowerCase().trim());
  });

  function filterCmdItems(q) {
    const items = cmdList.querySelectorAll('.cmd-item');
    const groups = cmdList.querySelectorAll('.cmd-group');

    items.forEach(item => {
      const kw = (item.getAttribute('data-keywords') || '') + ' ' + item.innerText.toLowerCase();
      if (!q || kw.includes(q)) {
        item.style.display = 'flex';
      } else {
        item.style.display = 'none';
      }
    });

    groups.forEach(grp => {
      const visible = grp.querySelectorAll('.cmd-item[style*="display: flex"], .cmd-item:not([style*="display: none"])');
      grp.style.display = (visible.length > 0) ? 'block' : 'none';
    });

    // Reset selection
    const firstVisible = cmdList.querySelector('.cmd-item:not([style*="display: none"])');
    items.forEach(i => i.classList.remove('is-selected'));
    if (firstVisible) firstVisible.classList.add('is-selected');
  }

  // Keyboard navigation within list
  cmdInput.addEventListener('keydown', function(e) {
    const visibleItems = Array.from(cmdList.querySelectorAll('.cmd-item:not([style*="display: none"])'));
    if (visibleItems.length === 0) return;

    let currentIndex = visibleItems.findIndex(i => i.classList.contains('is-selected'));

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      if (currentIndex < visibleItems.length - 1) {
        if (currentIndex >= 0) visibleItems[currentIndex].classList.remove('is-selected');
        visibleItems[currentIndex + 1].classList.add('is-selected');
        visibleItems[currentIndex + 1].scrollIntoView({ block: 'nearest' });
      }
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      if (currentIndex > 0) {
        visibleItems[currentIndex].classList.remove('is-selected');
        visibleItems[currentIndex - 1].classList.add('is-selected');
        visibleItems[currentIndex - 1].scrollIntoView({ block: 'nearest' });
      }
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (currentIndex >= 0 && visibleItems[currentIndex]) {
        visibleItems[currentIndex].click();
      }
    }
  });

  // Quick Action: Toggle Hire Status via AJAX
  window.toggleHireStatusQuick = function() {
    closeCmdPalette();
    showCmdToast('⏳ Memperbarui status ketersediaan kerja...');

    fetch('<?= BASE_URL ?>/admin/api_hire_status.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: ''
    })
    .then(r => r.json())
    .then(res => {
      if (res.status === 'success') {
        showCmdToast('✅ Status berhasil diubah: ' + res.label);
      } else {
        showCmdToast('❌ Gagal: ' + (res.message || 'Kesalahan'));
      }
    })
    .catch(() => showCmdToast('❌ Koneksi gagal'));
  };

  function showCmdToast(msg) {
    const toast = document.getElementById('cmdToast');
    toast.innerText = msg;
    toast.style.display = 'block';
    setTimeout(() => { toast.style.display = 'none'; }, 3000);
  }

})();
</script>
