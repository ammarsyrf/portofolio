<?php
/**
 * Admin Layout: Header & Topbar
 */

// Pastikan auth guard sudah aktif sebelum render header
if (!function_exists('is_admin_logged_in') || !is_admin_logged_in()) {
    require_once __DIR__ . '/../../includes/auth.php';
}

$pageTitle = $pageTitle ?? 'Dashboard';
$currentAdminUser = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> — Pengelola Portofolio</title>

  <!-- Google Fonts: Space Grotesk & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="<?= asset('img/favicon.svg') ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= asset('img/favicon-32x32.png') ?>">

  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
  
  <style>
    :root {
      --color-cream: var(--color-text);
      --color-cream-muted: var(--color-text-dim);
      --color-cream-faint: var(--color-text-faint);
      --color-brass: var(--color-accent);
      --color-brass-bright: var(--color-accent-bright);
      --color-brass-dim: rgba(76, 141, 255, 0.15);
      --color-line-brass: rgba(76, 141, 255, 0.4);
      --color-moss: #10B981;
      --color-moss-light: #34D399;
      --color-rust: #EF4444;
    }

    .admin-layout {
      display: flex;
      min-height: 100vh;
      background-color: var(--color-bg);
    }
    
    .admin-sidebar {
      width: 260px;
      background-color: var(--color-bg-alt);
      border-right: 1px solid var(--color-line);
      display: flex;
      flex-direction: column;
      flex-shrink: 0;
      position: sticky;
      top: 0;
      height: 100vh;
      overflow-y: auto;
      z-index: 50;
      transition: transform var(--transition-base);
    }

    .sidebar-brand {
      padding: 1.5rem;
      border-bottom: 1px solid var(--color-line);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .sidebar-nav {
      padding: 1.5rem 1rem;
      display: flex;
      flex-direction: column;
      gap: 0.35rem;
      flex-grow: 1;
    }

    .nav-section-title {
      font-size: 0.68rem;
      color: var(--color-brass);
      letter-spacing: 0.08em;
      text-transform: uppercase;
      margin: 1rem 0.75rem 0.5rem;
      font-weight: 600;
    }

    .sidebar-link {
      display: flex;
      align-items: center;
      gap: 0.85rem;
      padding: 0.75rem 0.85rem;
      color: var(--color-cream-muted);
      border-radius: 3px;
      font-size: var(--text-sm);
      font-weight: 500;
      border: 1px solid transparent;
      transition: all var(--transition-fast);
    }

    .sidebar-link:hover {
      background-color: var(--color-bg-surface);
      color: var(--color-cream);
      border-color: var(--color-line);
    }

    .sidebar-link.active {
      background-color: var(--color-bg-surface);
      color: var(--color-brass-bright);
      border-color: var(--color-line-brass);
    }

    .sidebar-footer {
      padding: 1.25rem;
      border-top: 1px solid var(--color-line);
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .admin-main {
      flex-grow: 1;
      min-width: 0;
      display: flex;
      flex-direction: column;
    }

    .admin-topbar {
      height: 4.25rem;
      background-color: rgba(18, 22, 31, 0.95);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--color-line);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 1.75rem;
      position: sticky;
      top: 0;
      z-index: 40;
    }

    .topbar-title {
      font-family: var(--font-display);
      font-size: var(--text-lg);
      color: var(--color-cream);
      font-weight: 500;
    }

    .topbar-actions {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .admin-badge {
      font-size: var(--text-xs);
      color: var(--color-brass-bright);
      background-color: var(--color-brass-dim);
      padding: 0.25rem 0.65rem;
      border-radius: 2px;
      border: 1px solid var(--color-line-brass);
    }

    .admin-content-area {
      padding: 2rem 1.75rem;
      flex-grow: 1;
      max-width: 1200px;
      width: 100%;
      margin: 0 auto;
    }

    @media (max-width: 900px) {
      .admin-sidebar {
        position: fixed;
        left: -260px;
      }
      .admin-sidebar.open {
        transform: translateX(260px);
      }
      .sidebar-toggle-mobile {
        display: inline-flex !important;
      }
    }

    .sidebar-toggle-mobile {
      display: none;
      background: none;
      border: 1px solid var(--color-line);
      color: var(--color-cream);
      padding: 0.4rem;
      border-radius: 2px;
      cursor: pointer;
    }

    /* Form & Card Styles */
    .admin-card {
      background-color: var(--color-bg-alt);
      border: 1px solid var(--color-line);
      border-radius: 3px;
      padding: 1.75rem;
      margin-bottom: 2rem;
    }

    .admin-card-header {
      padding-bottom: 1rem;
      margin-bottom: 1.5rem;
      border-bottom: 1px solid var(--color-line);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .admin-card-title {
      font-family: var(--font-display);
      font-size: var(--text-xl);
      color: var(--color-cream);
      font-weight: 500;
    }

    .form-group {
      margin-bottom: 1.35rem;
    }

    .form-label {
      display: block;
      font-size: var(--text-xs);
      color: var(--color-brass);
      margin-bottom: 0.4rem;
      font-weight: 500;
    }

    .form-help {
      font-size: 0.75rem;
      color: var(--color-cream-faint);
      margin-top: 0.35rem;
      line-height: 1.4;
    }

    .form-input, .form-textarea, .form-select, .form-control {
      width: 100%;
      background-color: var(--color-bg-surface);
      border: 1px solid var(--color-line);
      color: var(--color-cream);
      padding: 0.75rem 0.9rem;
      border-radius: 6px;
      font-size: var(--text-sm);
      transition: all var(--transition-fast);
      font-family: var(--font-body);
    }

    .form-input:focus, .form-textarea:focus, .form-select:focus, .form-control:focus {
      border-color: var(--color-brass);
      box-shadow: 0 0 0 3px rgba(76, 141, 255, 0.18);
      outline: none;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1.25rem;
    }

    @media (min-width: 680px) {
      .form-row-2 {
        grid-template-columns: repeat(2, 1fr);
      }
      .form-row-3 {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    /* Table Styles */
    .table-responsive {
      overflow-x: auto;
      border: 1px solid var(--color-line);
      border-radius: 3px;
    }

    .admin-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: var(--text-sm);
    }

    .admin-table th {
      background-color: var(--color-bg-surface);
      color: var(--color-brass);
      font-size: var(--text-xs);
      letter-spacing: 0.04em;
      padding: 0.85rem 1rem;
      border-bottom: 1px solid var(--color-line);
      font-weight: 600;
    }

    .admin-table td {
      padding: 0.9rem 1rem;
      border-bottom: 1px solid var(--color-line);
      color: var(--color-cream-muted);
      vertical-align: middle;
    }

    .admin-table tr:hover td {
      background-color: var(--color-bg-surface);
      color: var(--color-cream);
    }

    .badge-status {
      display: inline-block;
      padding: 0.2rem 0.55rem;
      font-size: 0.72rem;
      border-radius: 2px;
      font-weight: 600;
    }

    .badge-published {
      background-color: var(--color-moss-dim);
      color: var(--color-moss-light);
      border: 1px solid var(--color-moss);
    }

    .badge-draft {
      background-color: rgba(243, 236, 221, 0.08);
      color: var(--color-cream-faint);
      border: 1px solid var(--color-line);
    }

    /* Alert Banner */
    .flash-alert {
      padding: 0.9rem 1.25rem;
      border-radius: 3px;
      margin-bottom: 1.5rem;
      font-size: var(--text-sm);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .flash-success {
      background-color: rgba(85, 104, 79, 0.25);
      border: 1px solid var(--color-moss-light);
      color: #D1E7DD;
    }

    .flash-danger {
      background-color: rgba(185, 28, 28, 0.2);
      border: 1px solid rgba(239, 68, 68, 0.4);
      color: #FCA5A5;
    }

    .file-preview-box {
      margin-top: 0.75rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 0.75rem;
      background-color: var(--color-bg-surface);
      border: 1px solid var(--color-line);
      border-radius: 2px;
    }

    .file-preview-img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 2px;
      border: 1px solid var(--color-line-brass);
    }
  </style>
</head>
<body>

<div class="admin-layout">
