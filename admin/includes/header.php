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

  <!-- Google Fonts: Space Grotesk, Plus Jakarta Sans, Inter & JetBrains Mono -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="<?= asset('img/favicon.svg') ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= asset('img/favicon-32x32.png') ?>">

  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
  
  <style>
    /* =========================================================================
       ULTRA-PREMIUM ADMIN DESIGN SYSTEM TOKENS & VARIABLES
       ========================================================================= */
    :root {
      --font-display: 'Plus Jakarta Sans', 'Space Grotesk', system-ui, sans-serif;
      --font-body: 'Inter', system-ui, sans-serif;
      --font-mono: 'JetBrains Mono', monospace;

      /* Dark Studio & OLED Surfaces */
      --adm-bg: #07090e;
      --adm-bg-alt: #0c1017;
      --adm-surface: #111722;
      --adm-surface-elev: #171f2e;
      --adm-surface-glass: rgba(17, 23, 34, 0.75);

      /* Border & Glass Accents */
      --adm-border: rgba(255, 255, 255, 0.08);
      --adm-border-focus: rgba(59, 130, 246, 0.5);
      --adm-border-glass: rgba(255, 255, 255, 0.12);

      /* Colors & Gradients */
      --adm-accent: #3b82f6;
      --adm-accent-bright: #60a5fa;
      --adm-accent-glow: rgba(59, 130, 246, 0.35);
      --adm-gradient-primary: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
      --adm-gradient-accent: linear-gradient(135deg, #38bdf8 0%, #3b82f6 50%, #8b5cf6 100%);
      --adm-gradient-card: linear-gradient(180deg, rgba(23, 31, 46, 0.6) 0%, rgba(12, 16, 23, 0.8) 100%);

      /* Status Colors */
      --adm-emerald: #10b981;
      --adm-emerald-glow: rgba(16, 185, 129, 0.25);
      --adm-amber: #f59e0b;
      --adm-amber-glow: rgba(245, 158, 11, 0.25);
      --adm-rose: #f43f5e;
      --adm-rose-glow: rgba(244, 63, 94, 0.25);
      --adm-purple: #a855f7;

      /* Typography */
      --adm-text-primary: #f8fafc;
      --adm-text-secondary: #94a3b8;
      --adm-text-muted: #64748b;
      --adm-text-faint: #475569;

      /* Legacy compatibility mappings */
      --color-bg: var(--adm-bg);
      --color-bg-alt: var(--adm-bg-alt);
      --color-bg-surface: var(--adm-surface);
      --color-text: var(--adm-text-primary);
      --color-text-dim: var(--adm-text-secondary);
      --color-text-faint: var(--adm-text-muted);
      --color-accent: var(--adm-accent);
      --color-accent-bright: var(--adm-accent-bright);
      --color-line: var(--adm-border);
      --color-cream: var(--adm-text-primary);
      --color-cream-muted: var(--adm-text-secondary);
      --color-cream-faint: var(--adm-text-muted);
      --color-brass: var(--adm-accent-bright);
      --color-brass-bright: var(--adm-accent-bright);
      --color-brass-dim: rgba(59, 130, 246, 0.12);
      --color-line-brass: rgba(59, 130, 246, 0.35);
      --color-moss: var(--adm-emerald);
      --color-moss-light: #34d399;
      --color-moss-dim: rgba(16, 185, 129, 0.15);
      --color-rust: var(--adm-rose);
    }

    /* Global Base Reset for Admin */
    body {
      background-color: var(--adm-bg);
      color: var(--adm-text-primary);
      font-family: var(--font-body);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      overflow-x: hidden;
    }

    /* Custom Modern Scrollbars */
    ::-webkit-scrollbar {
      width: 6px;
      height: 6px;
    }
    ::-webkit-scrollbar-track {
      background: var(--adm-bg-alt);
    }
    ::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.15);
      border-radius: 999px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: var(--adm-accent);
    }

    .admin-layout {
      display: flex;
      min-height: 100vh;
      background-color: var(--adm-bg);
      background-image: 
        radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.08) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(139, 92, 246, 0.06) 0px, transparent 50%);
      background-attachment: fixed;
    }

    .admin-content-area {
      padding: 2.25rem 2rem;
      flex-grow: 1;
      max-width: 1360px;
      width: 100%;
      margin: 0 auto;
    }

    /* ── Ultra-Modern Cards ── */
    .admin-card {
      background: var(--adm-gradient-card);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid var(--adm-border);
      border-radius: 14px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.05);
      position: relative;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .admin-card:hover {
      border-color: rgba(255, 255, 255, 0.14);
      box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }

    .admin-card-header {
      padding-bottom: 1.25rem;
      margin-bottom: 1.75rem;
      border-bottom: 1px solid var(--adm-border);
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
    }

    .admin-card-title {
      font-family: var(--font-display);
      font-size: 1.25rem;
      color: var(--adm-text-primary);
      font-weight: 700;
      letter-spacing: -0.015em;
      display: flex;
      align-items: center;
      gap: 0.65rem;
    }

    /* ── Form Controls & Precision Inputs ── */
    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-label {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      font-size: 0.8rem;
      color: #cbd5e1;
      margin-bottom: 0.5rem;
      font-weight: 600;
      letter-spacing: 0.01em;
    }

    .form-help {
      font-size: 0.76rem;
      color: var(--adm-text-muted);
      margin-top: 0.4rem;
      line-height: 1.45;
    }

    .form-input, .form-textarea, .form-select, .form-control {
      width: 100%;
      background-color: var(--adm-surface);
      border: 1px solid var(--adm-border);
      color: var(--adm-text-primary);
      padding: 0.75rem 1rem;
      border-radius: 9px;
      font-size: 0.875rem;
      font-family: var(--font-body);
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .form-input::placeholder, .form-textarea::placeholder, .form-control::placeholder {
      color: var(--adm-text-faint);
    }

    .form-input:hover, .form-textarea:hover, .form-select:hover, .form-control:hover {
      border-color: rgba(255, 255, 255, 0.16);
      background-color: var(--adm-surface-elev);
    }

    .form-input:focus, .form-textarea:focus, .form-select:focus, .form-control:focus {
      border-color: var(--adm-accent);
      background-color: var(--adm-surface-elev);
      box-shadow: 0 0 0 3.5px rgba(59, 130, 246, 0.22), 0 0 20px rgba(59, 130, 246, 0.15);
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

    /* ── High-Precision Modern Buttons ── */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.55rem;
      font-family: var(--font-body);
      font-weight: 600;
      font-size: 0.85rem;
      padding: 0.65rem 1.25rem;
      border-radius: 8px;
      border: 1px solid transparent;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      white-space: nowrap;
      position: relative;
    }

    .btn-primary {
      background: var(--adm-gradient-primary);
      color: #ffffff;
      border: 1px solid rgba(255, 255, 255, 0.15);
      box-shadow: 0 4px 14px rgba(59, 130, 246, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
      transform: translateY(-1.5px);
      box-shadow: 0 8px 22px rgba(59, 130, 246, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.3);
      color: #ffffff;
    }
    .btn-primary:active {
      transform: translateY(0);
    }

    .btn-secondary {
      background: rgba(255, 255, 255, 0.05);
      color: var(--adm-text-primary);
      border: 1px solid var(--adm-border);
      backdrop-filter: blur(8px);
    }
    .btn-secondary:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.2);
      color: #ffffff;
      transform: translateY(-1px);
    }

    .btn-danger {
      background: rgba(244, 63, 94, 0.15);
      color: #fda4af;
      border: 1px solid rgba(244, 63, 94, 0.3);
    }
    .btn-danger:hover {
      background: rgba(244, 63, 94, 0.28);
      border-color: rgba(244, 63, 94, 0.5);
      color: #fff;
      transform: translateY(-1px);
    }

    .btn-sm {
      padding: 0.45rem 0.85rem;
      font-size: 0.78rem;
      border-radius: 6px;
    }

    /* ── Frosted Modern Tables ── */
    .table-responsive {
      overflow-x: auto;
      border: 1px solid var(--adm-border);
      border-radius: 10px;
      background: var(--adm-surface);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .admin-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: 0.86rem;
    }

    .admin-table th {
      background: var(--adm-surface-elev);
      color: var(--adm-accent-bright);
      font-size: 0.72rem;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      padding: 0.95rem 1.15rem;
      border-bottom: 1px solid var(--adm-border);
      font-weight: 700;
    }

    .admin-table td {
      padding: 1rem 1.15rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.04);
      color: var(--adm-text-secondary);
      vertical-align: middle;
      transition: background 0.15s ease;
    }

    .admin-table tr:last-child td {
      border-bottom: none;
    }

    .admin-table tr:hover td {
      background-color: rgba(255, 255, 255, 0.03);
      color: var(--adm-text-primary);
    }

    /* ── Status Badges & Pills ── */
    .badge-status {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      padding: 0.25rem 0.65rem;
      font-size: 0.72rem;
      border-radius: 999px;
      font-weight: 600;
      letter-spacing: 0.02em;
    }

    .badge-published {
      background-color: var(--color-moss-dim);
      color: var(--color-moss-light);
      border: 1px solid rgba(16, 185, 129, 0.4);
      box-shadow: 0 0 10px rgba(16, 185, 129, 0.15);
    }

    .badge-draft {
      background-color: rgba(148, 163, 184, 0.1);
      color: var(--adm-text-secondary);
      border: 1px solid rgba(148, 163, 184, 0.2);
    }

    /* ── Flash Alerts ── */
    .flash-alert {
      padding: 1rem 1.35rem;
      border-radius: 10px;
      margin-bottom: 1.75rem;
      font-size: 0.86rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      backdrop-filter: blur(12px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
      animation: alertSlideDown 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes alertSlideDown {
      from { opacity: 0; transform: translateY(-8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .flash-success {
      background-color: rgba(16, 185, 129, 0.12);
      border: 1px solid rgba(16, 185, 129, 0.4);
      color: #6ee7b7;
    }

    .flash-danger {
      background-color: rgba(244, 63, 94, 0.12);
      border: 1px solid rgba(244, 63, 94, 0.4);
      color: #fda4af;
    }

    /* ── File Preview Box ── */
    .file-preview-box {
      margin-top: 0.85rem;
      display: flex;
      align-items: center;
      gap: 1.15rem;
      padding: 0.95rem;
      background: var(--adm-surface-elev);
      border: 1px solid var(--adm-border);
      border-radius: 10px;
    }

    .file-preview-img {
      width: 68px;
      height: 68px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid rgba(59, 130, 246, 0.35);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }
  </style>
</head>
<body>

<div class="admin-layout">
