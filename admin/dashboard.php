<?php
/**
 * Admin Dashboard — Bento Grid with Visitor Telemetry & Analytics
 * Stats: Proyek, Pencapaian, Kreasi, Buku Tamu, Tautan
 *        Visitor Analytics (IP Unik, Negara, Kota, Device, Browser, OS, 7-Hari Chart)
 *        Profile Kelengkapan, Log Pengunjung Real-time, Pesan Terkini
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Dashboard';

// ── Statistik Konten ────────────────────────────────────────────────────────
$totalProj   = (int)$pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$pubProj     = (int)$pdo->query("SELECT COUNT(*) FROM projects WHERE is_published = 1")->fetchColumn();
$draftProj   = $totalProj - $pubProj;

$totalAch    = (int)$pdo->query("SELECT COUNT(*) FROM achievements")->fetchColumn();

$totalCreations = (int)$pdo->query("SELECT COUNT(*) FROM creations")->fetchColumn();
$creationsVisible = (get_setting($pdo, 'show_creations_section', '1') === '1');

$totalGuest  = (int)$pdo->query("SELECT COUNT(*) FROM guestbook")->fetchColumn();
$newGuest    = (int)$pdo->query("SELECT COUNT(*) FROM guestbook WHERE DATE(created_at) = CURDATE()")->fetchColumn();

$totalLinks  = (int)$pdo->query("SELECT COUNT(*) FROM links WHERE is_active = 1")->fetchColumn();

// ── Profil ──────────────────────────────────────────────────────────────────
$profile = get_profile($pdo);
$hasCv   = !empty($profile['cv_file']) && file_exists(UPLOAD_DIR_CV . '/' . basename($profile['cv_file']));
$hasPhoto= !empty($profile['photo'])   && file_exists(UPLOAD_DIR_PHOTOS . '/' . basename($profile['photo']));

// Hitung % kelengkapan profil
$profileFields = ['name','tagline','bio','location','email','github_url'];
$filled = 0;
foreach ($profileFields as $f) if (!empty($profile[$f])) $filled++;
if ($hasCv)    $filled++;
if ($hasPhoto) $filled++;
$profileCompletion = (int)round(($filled / (count($profileFields) + 2)) * 100);

// ── Visitor Telemetry Stats ──────────────────────────────────────────────────
$visitor = get_visitor_stats($pdo);

// ── Proyek Terkini & Pesan Terbaru ──────────────────────────────────────────
$recentProjects  = $pdo->query("SELECT id, title, category, is_published, sort_order FROM projects ORDER BY id DESC LIMIT 5")->fetchAll();
$recentGuestbook = $pdo->query("SELECT * FROM guestbook ORDER BY id DESC LIMIT 4")->fetchAll();
$recentAch       = $pdo->query("SELECT id, title FROM achievements ORDER BY id DESC LIMIT 3")->fetchAll();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<style>
  /* ── Bento Grid ── */
  .bento-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .bento-card {
    background: var(--color-bg-alt);
    border: 1px solid var(--color-line);
    border-radius: 10px;
    padding: 1.35rem 1.45rem;
    position: relative;
    overflow: hidden;
    transition: border-color 0.2s ease, transform 0.2s ease;
  }
  .bento-card:hover {
    border-color: var(--color-accent);
    transform: translateY(-1px);
  }
  .bento-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(76,141,255,0.04) 0%, transparent 60%);
    pointer-events: none;
  }

  /* Grid spans */
  .col-3  { grid-column: span 3; }
  .col-4  { grid-column: span 4; }
  .col-6  { grid-column: span 6; }
  .col-8  { grid-column: span 8; }
  .col-12 { grid-column: span 12; }

  @media (max-width: 1100px) {
    .col-3 { grid-column: span 6; }
    .col-4 { grid-column: span 6; }
    .col-8 { grid-column: span 12; }
  }
  @media (max-width: 640px) {
    .bento-grid { grid-template-columns: 1fr 1fr; gap: 0.75rem; }
    .col-3, .col-4, .col-6, .col-8, .col-12 { grid-column: span 2; }
  }

  /* Stat card */
  .stat-label {
    font-size: 0.67rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: var(--color-accent-bright);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }
  .stat-value {
    font-family: var(--font-display);
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--color-text);
    line-height: 1;
    margin-bottom: 0.3rem;
  }
  .stat-sub {
    font-size: 0.74rem;
    color: var(--color-text-faint);
  }
  .stat-icon {
    position: absolute;
    bottom: 0.85rem;
    right: 1.15rem;
    opacity: 0.07;
    font-size: 3.5rem;
    pointer-events: none;
  }

  /* Chart area */
  .chart-container {
    position: relative;
    height: 140px;
    margin-top: 0.5rem;
  }

  /* Progress bar */
  .progress-bar-bg {
    background: var(--color-bg-surface);
    border-radius: 999px;
    height: 6px;
    overflow: hidden;
    margin-top: 0.35rem;
  }
  .progress-bar-fill {
    height: 100%;
    border-radius: 999px;
    background: linear-gradient(90deg, var(--color-accent), #4FC3F7);
    transition: width 0.6s ease;
  }

  /* Quick actions */
  .quick-actions {
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
  }
  .quick-link {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.55rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--color-text-dim);
    border: 1px solid transparent;
    transition: all 0.15s ease;
    text-decoration: none;
  }
  .quick-link:hover {
    background: var(--color-bg-surface);
    border-color: var(--color-line);
    color: var(--color-accent-bright);
  }
  .quick-link svg { flex-shrink: 0; opacity: 0.7; }

  /* Guestbook items */
  .gb-item {
    padding: 0.65rem 0;
    border-bottom: 1px solid var(--color-line);
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
  }
  .gb-item:last-child { border-bottom: none; }
  .gb-name { font-size: 0.82rem; font-weight: 600; color: var(--color-text); }
  .gb-msg  { font-size: 0.76rem; color: var(--color-text-faint); }
  .gb-time { font-size: 0.68rem; color: var(--color-text-faint); opacity: 0.6; }

  /* Visitor table */
  .vis-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.76rem;
  }
  .vis-table th {
    text-align: left;
    padding: 0.45rem 0.6rem;
    font-weight: 600;
    color: var(--color-text-faint);
    border-bottom: 1px solid var(--color-line);
    font-size: 0.68rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .vis-table td {
    padding: 0.55rem 0.6rem;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    color: var(--color-text-dim);
    vertical-align: middle;
  }
  .vis-table tr:hover td {
    background: rgba(255,255,255,0.02);
  }
  .vis-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.15rem 0.5rem;
    border-radius: 4px;
    font-size: 0.68rem;
    font-weight: 500;
    background: var(--color-bg-surface);
    border: 1px solid var(--color-line);
    color: var(--color-text);
  }
  .vis-device-icon {
    font-size: 0.85rem;
  }

  /* Metric mini bar */
  .metric-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.45rem;
    font-size: 0.76rem;
  }
  .metric-name {
    color: var(--color-text-dim);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }
  .metric-bar-wrap {
    flex: 1;
    margin: 0 0.75rem;
    background: var(--color-bg-surface);
    height: 5px;
    border-radius: 999px;
    overflow: hidden;
  }
  .metric-bar {
    height: 100%;
    border-radius: 999px;
    background: var(--color-accent);
  }
  .metric-count {
    font-weight: 600;
    color: var(--color-text);
    min-width: 32px;
    text-align: right;
  }
</style>

<!-- Page Header -->
<div style="margin-bottom: 1.5rem;">
  <div class="stat-label" style="font-size: 0.7rem; margin-bottom: 0.25rem;">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="4"/></svg>
    <?= date('D, d M Y — H:i') ?> WIB
  </div>
  <h2 style="font-family: var(--font-display); font-size: 1.5rem; color: var(--color-text); font-weight: 700; margin: 0 0 0.2rem;">
    Selamat datang, <?= e($_SESSION['admin_username'] ?? 'Admin') ?> 👋
  </h2>
  <p style="color: var(--color-text-faint); font-size: 0.83rem; margin: 0;">
    Ringkasan performa portofolio &amp; telemetri pengunjung live.
  </p>
</div>

<!-- ══════════════════════════════════════════════════════════════ -->
<!--  BENTO GRID                                                   -->
<!-- ══════════════════════════════════════════════════════════════ -->
<div class="bento-grid">

  <!-- ① Proyek -->
  <div class="bento-card col-3">
    <div class="stat-label">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
      Proyek
    </div>
    <div class="stat-value"><?= $totalProj ?></div>
    <div class="stat-sub"><?= $pubProj ?> published · <?= $draftProj ?> draft</div>
    <div class="stat-icon">📁</div>
  </div>

  <!-- ② Pencapaian -->
  <div class="bento-card col-3">
    <div class="stat-label">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
      Pencapaian
    </div>
    <div class="stat-value"><?= $totalAch ?></div>
    <div class="stat-sub">Sertifikat &amp; penghargaan</div>
    <div class="stat-icon">🏅</div>
  </div>

  <!-- ③ Pengunjung Hari Ini (Page Views & Unique IP) -->
  <div class="bento-card col-3">
    <div class="stat-label">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
      Pengunjung Hari Ini
    </div>
    <div class="stat-value"><?= number_format($visitor['today']) ?></div>
    <div class="stat-sub"><strong style="color:var(--color-accent-bright);"><?= $visitor['unique_today'] ?> IP unik</strong> · kemarin <?= $visitor['yesterday'] ?> hits</div>
    <div class="stat-icon">📈</div>
  </div>

  <!-- ④ Total Pengunjung Unik -->
  <div class="bento-card col-3">
    <div class="stat-label">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Total IP Unik
    </div>
    <div class="stat-value"><?= number_format($visitor['unique_total']) ?></div>
    <div class="stat-sub">dari <?= number_format($visitor['total']) ?> total pageviews</div>
    <div class="stat-icon">🌐</div>
  </div>

  <!-- ⑤ Chart Visitor 7 Hari -->
  <div class="bento-card col-8">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 0.5rem;">
      <div class="stat-label" style="margin-bottom: 0;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        Trafik 7 Hari Terakhir (Page Views vs IP Unik)
      </div>
      <div style="font-size: 0.7rem; color: var(--color-text-faint);">
        <span style="display:inline-block; width:8px; height:8px; background:rgba(76,141,255,0.6); border-radius:2px; margin-right:3px;"></span> Hits
        <span style="display:inline-block; width:8px; height:8px; background:#10B981; border-radius:50%; margin:0 3px 0 8px;"></span> IP Unik
      </div>
    </div>
    <?php if ($visitor['table_exists'] && !empty($visitor['trend'])): ?>
    <div class="chart-container">
      <canvas id="visitorChart"></canvas>
    </div>
    <?php else: ?>
    <div style="height:140px; display:flex; align-items:center; justify-content:center; color: var(--color-text-faint); font-size:0.8rem;">
      Belum ada data trafik.
    </div>
    <?php endif; ?>
  </div>

  <!-- ⑥ Pintasan Cepat -->
  <div class="bento-card col-4">
    <div class="stat-label" style="margin-bottom: 0.75rem;">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
      Pintasan Cepat
    </div>
    <div class="quick-actions">
      <a href="<?= BASE_URL ?>/admin/project-form" class="quick-link">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Proyek Baru
      </a>
      <a href="<?= BASE_URL ?>/admin/achievement_form" class="quick-link">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Pencapaian
      </a>
      <a href="<?= BASE_URL ?>/admin/guestbook" class="quick-link">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        Moderasi Buku Tamu
        <?php if ($newGuest > 0): ?>
          <span style="margin-left: auto; background: var(--color-accent); color: #fff; font-size: 0.65rem; padding: 0.1rem 0.5rem; border-radius: 999px;"><?= $newGuest ?> baru</span>
        <?php endif; ?>
      </a>
      <a href="<?= BASE_URL ?>/admin/profile" class="quick-link">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Edit Profil
      </a>
      <a href="<?= BASE_URL ?>/" target="_blank" class="quick-link">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Lihat Website Publik ↗
      </a>
    </div>
  </div>

  <!-- ⑦ Asal Negara & Kota Pengunjung -->
  <div class="bento-card col-4">
    <div class="stat-label" style="margin-bottom: 0.75rem;">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
      Asal Negara Pengunjung
    </div>
    <?php if (empty($visitor['top_countries'])): ?>
      <p style="color: var(--color-text-faint); font-size: 0.8rem;">Belum ada data negara.</p>
    <?php else: ?>
      <?php 
      $maxHits = max(array_column($visitor['top_countries'], 'hits')) ?: 1;
      foreach ($visitor['top_countries'] as $c): 
        $pct = round(($c['hits'] / $maxHits) * 100);
      ?>
      <div class="metric-row">
        <div class="metric-name">
          <span>🌍</span> <?= e(mb_strimwidth($c['country'], 0, 16, '…')) ?>
        </div>
        <div class="metric-bar-wrap">
          <div class="metric-bar" style="width: <?= $pct ?>%;"></div>
        </div>
        <div class="metric-count"><?= $c['hits'] ?> <span style="font-size:0.65rem; color:var(--color-text-faint); font-weight:normal;">(<?= $c['unique_ips'] ?> IP)</span></div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($visitor['top_cities'])): ?>
    <div style="margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px solid var(--color-line);">
      <div style="font-size: 0.68rem; font-weight: 700; color: var(--color-text-faint); text-transform: uppercase; margin-bottom: 0.35rem;">Kota Teratas:</div>
      <div style="display: flex; flex-wrap: wrap; gap: 0.3rem;">
        <?php foreach ($visitor['top_cities'] as $ct): ?>
        <span class="vis-badge" style="font-size: 0.65rem;">📍 <?= e($ct['city']) ?> (<?= $ct['hits'] ?>)</span>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- ⑧ Device & Browser Breakdown -->
  <div class="bento-card col-4">
    <div class="stat-label" style="margin-bottom: 0.75rem;">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
      Perangkat &amp; Browser
    </div>

    <!-- Devices -->
    <div style="margin-bottom: 0.75rem;">
      <div style="font-size: 0.68rem; font-weight: 700; color: var(--color-text-faint); text-transform: uppercase; margin-bottom: 0.4rem;">Tipe Perangkat:</div>
      <div style="display: flex; gap: 0.5rem;">
        <?php 
        $devIcons = ['Desktop' => '💻', 'Mobile' => '📱', 'Tablet' => '📟'];
        foreach ($visitor['device_breakdown'] as $dev): 
          $icon = $devIcons[$dev['name']] ?? '🖥️';
        ?>
        <div style="flex:1; background:var(--color-bg-surface); border:1px solid var(--color-line); border-radius:6px; padding:0.45rem 0.5rem; text-align:center;">
          <div style="font-size:0.9rem;"><?= $icon ?></div>
          <div style="font-size:0.75rem; font-weight:700; color:var(--color-text); margin:0.15rem 0;"><?= $dev['percentage'] ?>%</div>
          <div style="font-size:0.65rem; color:var(--color-text-faint);"><?= $dev['name'] ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Browsers -->
    <div>
      <div style="font-size: 0.68rem; font-weight: 700; color: var(--color-text-faint); text-transform: uppercase; margin-bottom: 0.35rem;">Browser Populer:</div>
      <?php 
      $bIcons = ['Chrome' => '🌐', 'Safari' => '🧭', 'Edge' => '🌊', 'Firefox' => '🦊', 'Opera' => '🔴', 'Other' => '🔌'];
      foreach (array_slice($visitor['browser_breakdown'], 0, 4) as $br): 
      ?>
      <div class="metric-row" style="margin-bottom: 0.35rem;">
        <div class="metric-name" style="font-size:0.72rem;">
          <span><?= $bIcons[$br['name']] ?? '🌐' ?></span> <?= $br['name'] ?>
        </div>
        <div class="metric-bar-wrap">
          <div class="metric-bar" style="width: <?= $br['percentage'] ?>%; background: #60A5FA;"></div>
        </div>
        <div class="metric-count" style="font-size:0.72rem;"><?= $br['percentage'] ?>%</div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ⑨ Kelengkapan Profil -->
  <div class="bento-card col-4">
    <div class="stat-label" style="margin-bottom: 0.75rem;">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Kelengkapan Profil
    </div>
    <div style="display: flex; align-items: flex-end; gap: 0.5rem; margin-bottom: 0.5rem;">
      <div class="stat-value" style="font-size: 2rem;"><?= $profileCompletion ?>%</div>
      <div class="stat-sub" style="margin-bottom: 0.35rem;"><?= $hasCv ? '✓ CV' : '✗ CV' ?> · <?= $hasPhoto ? '✓ Foto' : '✗ Foto' ?></div>
    </div>
    <div class="progress-bar-bg">
      <div class="progress-bar-fill" style="width: <?= $profileCompletion ?>%;"></div>
    </div>
    <div style="margin-top: 0.75rem; display: flex; flex-direction: column; gap: 0.25rem;">
      <?php foreach ([
        ['Email'      , !empty($profile['email'])],
        ['Bio'        , !empty($profile['bio'])],
        ['GitHub URL' , !empty($profile['github_url'])],
        ['Tagline'    , !empty($profile['tagline'])],
      ] as [$label, $ok]): ?>
      <div style="display:flex; align-items:center; gap:0.4rem; font-size:0.73rem; color: var(--color-text-faint);">
        <span style="color: <?= $ok ? '#34D399' : '#EF4444' ?>; font-weight:700;"><?= $ok ? '✓' : '✗' ?></span>
        <?= $label ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ⑩ Live Log Pengunjung Terkini (Real-time Feed) -->
  <div class="bento-card col-8">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 0.65rem;">
      <div class="stat-label" style="margin-bottom: 0;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
        Log Kunjungan Terbaru (Live Telemetry)
      </div>
      <span style="font-size: 0.7rem; color: var(--color-accent-bright); background: rgba(76,141,255,0.1); padding: 0.15rem 0.5rem; border-radius: 999px;">Realtime</span>
    </div>

    <?php if (empty($visitor['recent_visitors'])): ?>
      <p style="color: var(--color-text-faint); font-size: 0.8rem; padding: 1rem 0;">Belum ada log kunjungan.</p>
    <?php else: ?>
      <div style="overflow-x: auto;">
        <table class="vis-table">
          <thead>
            <tr>
              <th>IP Masked</th>
              <th>Lokasi</th>
              <th>Device &amp; OS</th>
              <th>Browser</th>
              <th>Halaman</th>
              <th>Waktu</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($visitor['recent_visitors'] as $v): ?>
            <tr>
              <td><code style="font-size:0.7rem; color:var(--color-accent-bright); background:transparent; border:none; padding:0;"><?= e($v['ip_masked']) ?></code></td>
              <td>
                <div style="font-weight:600; color:var(--color-text);"><?= e($v['country'] ?: 'Unknown') ?></div>
                <div style="font-size:0.66rem; color:var(--color-text-faint);"><?= e($v['city'] ?: '') ?></div>
              </td>
              <td>
                <span class="vis-badge">
                  <?= $v['device'] === 'Mobile' ? '📱' : ($v['device'] === 'Tablet' ? '📟' : '💻') ?>
                  <?= e($v['os'] ?: 'OS') ?>
                </span>
              </td>
              <td><?= e($v['browser'] ?: 'Browser') ?></td>
              <td><span title="<?= e($v['page']) ?>" style="max-width:90px; display:inline-block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= e($v['page']) ?></span></td>
              <td style="font-size:0.68rem; color:var(--color-text-faint); white-space:nowrap;"><?= date('H:i:s', strtotime($v['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- ⑪ Ringkasan Konten -->
  <div class="bento-card col-4">
    <div class="stat-label" style="margin-bottom: 0.75rem;">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Ringkasan Konten
    </div>
    <div style="display: flex; flex-direction: column; gap: 0.55rem;">
      <?php foreach ([
        ['Kreasi TikTok & IG', $totalCreations, $creationsVisible ? '👁 Ditampilkan' : '🚫 Disembunyikan'],
        ['Tautan Aktif'       , $totalLinks     , 'link aktif'],
        ['Pengunjung Minggu Ini', $visitor['this_week'], 'pageviews'],
      ] as [$lbl, $val, $sub]): ?>
      <div style="display:flex; align-items:center; justify-content:space-between; padding:0.45rem 0; border-bottom: 1px solid var(--color-line);">
        <div>
          <div style="font-size:0.77rem; color: var(--color-text-dim); font-weight:500;"><?= $lbl ?></div>
          <div style="font-size:0.68rem; color: var(--color-text-faint);"><?= $sub ?></div>
        </div>
        <div style="font-family: var(--font-display); font-size: 1.3rem; font-weight: 700; color: var(--color-text);"><?= number_format($val) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ⑫ Pesan Buku Tamu Terkini -->
  <div class="bento-card col-6">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 0.75rem;">
      <div class="stat-label" style="margin-bottom:0;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        Pesan Terkini
      </div>
      <a href="<?= BASE_URL ?>/admin/guestbook" class="btn btn-secondary btn-sm" style="font-size:0.72rem; padding:0.25rem 0.65rem;">Lihat Semua</a>
    </div>
    <?php if (empty($recentGuestbook)): ?>
      <p style="color: var(--color-text-faint); font-size: 0.82rem;">Belum ada pesan.</p>
    <?php else: ?>
      <?php foreach ($recentGuestbook as $gb): ?>
      <div class="gb-item">
        <div class="gb-name"><?= e($gb['user_name']) ?></div>
        <div class="gb-msg"><?= e(mb_strimwidth($gb['message'], 0, 90, '…')) ?></div>
        <div class="gb-time"><?= e($gb['created_at'] ?? '') ?></div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- ⑬ Proyek Terkini -->
  <div class="bento-card col-6">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 0.75rem;">
      <div class="stat-label" style="margin-bottom:0;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        Proyek Terkini
      </div>
      <a href="<?= BASE_URL ?>/admin/projects" class="btn btn-secondary btn-sm" style="font-size:0.72rem; padding:0.25rem 0.65rem;">Kelola</a>
    </div>
    <?php if (empty($recentProjects)): ?>
      <p style="color: var(--color-text-faint); font-size: 0.82rem;">Belum ada proyek.</p>
    <?php else: ?>
      <div style="display:flex; flex-direction:column; gap:0.55rem;">
        <?php foreach ($recentProjects as $p): ?>
        <div style="display:flex; align-items:center; gap:0.75rem;">
          <span class="badge-status <?= $p['is_published'] ? 'badge-published' : 'badge-draft' ?>" style="flex-shrink:0;"><?= $p['is_published'] ? 'Live' : 'Draft' ?></span>
          <div style="min-width:0;">
            <div style="font-size:0.8rem; font-weight:600; color:var(--color-text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= e($p['title']) ?></div>
            <div style="font-size:0.7rem; color:var(--color-text-faint);"><?= e($p['category']) ?></div>
          </div>
          <a href="<?= BASE_URL ?>/admin/project-form?id=<?= (int)$p['id'] ?>" style="margin-left:auto; font-size:0.7rem; color: var(--color-accent-bright); flex-shrink:0;">Edit</a>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</div><!-- /.bento-grid -->

<?php if ($visitor['table_exists'] && !empty($visitor['trend'])): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
  const trend  = <?= json_encode($visitor['trend']) ?>;
  const labels = trend.map(d => d.label);
  const views  = trend.map(d => d.views);
  const unique = trend.map(d => d.unique);

  const ctx = document.getElementById('visitorChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [
        {
          label: 'Page Views',
          data: views,
          backgroundColor: 'rgba(76,141,255,0.45)',
          borderColor: 'rgba(76,141,255,0.85)',
          borderWidth: 1,
          borderRadius: 4,
          order: 2,
        },
        {
          label: 'IP Unik',
          data: unique,
          type: 'line',
          borderColor: '#10B981',
          backgroundColor: 'rgba(16,185,129,0.1)',
          borderWidth: 2,
          pointRadius: 3,
          pointBackgroundColor: '#10B981',
          tension: 0.35,
          fill: false,
          order: 1,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1E293B',
          titleColor: '#F8FAFC',
          bodyColor: '#94A3B8',
          borderColor: '#334155',
          borderWidth: 1,
          padding: 8,
          boxPadding: 4,
          callbacks: {
            label: function(ctx) {
              return ` ${ctx.dataset.label}: ${ctx.raw}`;
            }
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: '#64748B', font: { size: 10 } }
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(255,255,255,0.05)' },
          ticks: {
            color: '#64748B',
            font: { size: 10 },
            precision: 0,
            stepSize: 1
          }
        }
      }
    }
  });
})();
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
