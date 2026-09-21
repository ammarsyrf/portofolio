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

    <div class="topbar-actions">
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
})();
</script>
