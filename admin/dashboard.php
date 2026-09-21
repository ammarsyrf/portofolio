<?php
/**
 * Admin Dashboard — Supercharged Bento Grid CMS
 * Features:
 *  - Career Telemetry: CV Download Tracker, Referrer Breakdown (LinkedIn, GitHub, IG, TikTok, Google), Top Projects
 *  - Security & System: Server Health, PHP & DB Status, Login Activity Monitor, Bot/Human Ratio
 *  - Productivity & Content: Live Visitor Feed, SEO Health Checker, Scratchpad / To-Do Autosave, Quick WhatsApp/Email Reply
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

// ── Telemetry, Security & Server Data ───────────────────────────────────────
$visitor   = get_visitor_stats($pdo);
$career    = get_career_telemetry($pdo);
$security  = get_security_monitor($pdo);
$health    = get_server_health($pdo);
$seoHealth = get_seo_health($pdo);
$notes     = get_admin_notes($pdo);
$hireStatus= get_hire_status($pdo);


// ── Proyek Terkini & Pesan Terbaru ──────────────────────────────────────────
$recentProjects  = $pdo->query("SELECT id, title, category, is_published, sort_order FROM projects ORDER BY id DESC LIMIT 4")->fetchAll();
$recentGuestbook = $pdo->query("SELECT * FROM guestbook ORDER BY id DESC LIMIT 4")->fetchAll();

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
    margin-bottom: 0.45rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }
  .stat-value {
    font-family: var(--font-display);
    font-size: 2.1rem;
    font-weight: 700;
    color: var(--color-text);
    line-height: 1;
    margin-bottom: 0.25rem;
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
    font-size: 0.78rem;
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

  /* Visitor table */
  .vis-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.75rem;
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
    padding: 0.5rem 0.6rem;
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
    padding: 0.15rem 0.45rem;
    border-radius: 4px;
    font-size: 0.68rem;
    font-weight: 500;
    background: var(--color-bg-surface);
    border: 1px solid var(--color-line);
    color: var(--color-text);
  }

  /* Metric row */
  .metric-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.45rem;
    font-size: 0.75rem;
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

  /* Guestbook item */
  .gb-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--color-line);
  }
  .gb-item:last-child { border-bottom: none; }
  .gb-name { font-size: 0.82rem; font-weight: 600; color: var(--color-text); margin-bottom: 0.2rem; }
  .gb-msg  { font-size: 0.76rem; color: var(--color-text-dim); line-height: 1.4; margin-bottom: 0.4rem; }
  .gb-actions { display: flex; align-items: center; gap: 0.5rem; }

  /* Scratchpad Textarea */
  .scratchpad-area {
    width: 100%;
    background: var(--color-bg-surface);
    border: 1px solid var(--color-line);
    border-radius: 6px;
    padding: 0.65rem 0.75rem;
    color: var(--color-text);
    font-family: inherit;
    font-size: 0.78rem;
    line-height: 1.5;
    resize: vertical;
    min-height: 110px;
    box-sizing: border-box;
    transition: border-color 0.2s;
  }
  .scratchpad-area:focus {
    outline: none;
    border-color: var(--color-accent);
  }
</style>

<!-- Page Header with Export Action -->
<div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:1rem; margin-bottom: 1.5rem;">
  <div>
    <div class="stat-label" style="font-size: 0.7rem; margin-bottom: 0.25rem;">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="4"/></svg>
      <?= date('D, d M Y — H:i') ?> WIB
    </div>
    <h2 style="font-family: var(--font-display); font-size: 1.5rem; color: var(--color-text); font-weight: 700; margin: 0 0 0.2rem;">
      Selamat datang, <?= e($_SESSION['admin_username'] ?? 'Admin') ?> 👋
    </h2>
    <p style="color: var(--color-text-faint); font-size: 0.83rem; margin: 0;">
      Pusat Komando: Karir, Keamanan Sistem &amp; Telemetri Pengunjung Realtime.
    </p>
  </div>

  <!-- Header Action Buttons -->
  <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
    <!-- Live Hire Status Toggle Button -->
    <button type="button" id="btnToggleHireStatus" class="btn btn-secondary btn-sm" style="display:inline-flex; align-items:center; gap:0.45rem; font-size:0.75rem; padding:0.45rem 0.85rem; border-color: <?= $hireStatus['color'] ?>; cursor:pointer;" title="Klik untuk mengubah status ketersediaan kerja (Open to Work / Busy)">
      <span id="hireStatusDot" style="display:inline-block; width:8px; height:8px; border-radius:50%; background:<?= $hireStatus['color'] ?>;"></span>
      <span id="hireStatusText" style="font-weight:600; color:<?= $hireStatus['color'] ?>;"><?= $hireStatus['short'] ?></span>
    </button>

    <!-- 1-Click Database Backup (.sql) -->
    <a href="<?= BASE_URL ?>/admin/backup_db.php" class="btn btn-secondary btn-sm" style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.75rem; padding:0.45rem 0.85rem;" title="Unduh snapshot SQL database portofolio">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
      Backup DB (.sql)
    </a>

    <!-- Export Analytics CSV -->
    <a href="<?= BASE_URL ?>/admin/export_analytics.php?range=30" class="btn btn-secondary btn-sm" style="display:inline-flex; align-items:center; gap:0.4rem; font-size:0.75rem; padding:0.45rem 0.85rem;" title="Unduh log data kunjungan">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
      Laporan CSV
    </a>
  </div>
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

  <!-- ③ Pengunjung Hari Ini -->
  <div class="bento-card col-3">
    <div class="stat-label">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
      Pengunjung Hari Ini
    </div>
    <div class="stat-value"><?= number_format($visitor['today']) ?></div>
    <div class="stat-sub"><strong style="color:var(--color-accent-bright);"><?= $visitor['unique_today'] ?> IP unik</strong> · total <?= number_format($visitor['unique_total']) ?> IP</div>
    <div class="stat-icon">📈</div>
  </div>

  <!-- ④ Unduhan CV / Resume Tracker -->
  <div class="bento-card col-3">
    <div class="stat-label">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="12" y2="18"/><line x1="15" y1="15" x2="12" y2="18"/></svg>
      Unduhan CV / Resume
    </div>
    <div class="stat-value"><?= number_format($career['cv_total']) ?></div>
    <div class="stat-sub"><strong style="color:#10B981;"><?= $career['cv_this_week'] ?> unduhan</strong> minggu ini</div>
    <div class="stat-icon">📄</div>
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

  <!-- ⑥ Status & Kesehatan Server -->
  <div class="bento-card col-4">
    <div class="stat-label" style="margin-bottom: 0.75rem;">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
      Kesehatan Server &amp; Hosting
    </div>
    <div style="display: flex; flex-direction: column; gap: 0.45rem;">
      <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.75rem; padding-bottom:0.4rem; border-bottom:1px solid var(--color-line);">
        <span style="color:var(--color-text-dim);">Database MySQL</span>
        <span class="vis-badge" style="background:rgba(16,185,129,0.1); border-color:rgba(16,185,129,0.3); color:#34D399;">● <?= $health['db_status'] ?> (<?= $health['db_ping_ms'] ?>ms)</span>
      </div>
      <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.75rem; padding-bottom:0.4rem; border-bottom:1px solid var(--color-line);">
        <span style="color:var(--color-text-dim);">PHP Runtime</span>
        <span style="font-weight:600; color:var(--color-text);">PHP <?= $health['php_version'] ?> (<?= $health['server_os'] ?>)</span>
      </div>
      <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.75rem; padding-bottom:0.4rem; border-bottom:1px solid var(--color-line);">
        <span style="color:var(--color-text-dim);">Storage Uploads</span>
        <span style="font-weight:600; color:var(--color-text);"><?= $health['upload_size_mb'] ?> MB</span>
      </div>
      <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.75rem;">
        <span style="color:var(--color-text-dim);">Penggunaan Memori</span>
        <span style="font-weight:600; color:var(--color-text);"><?= $health['memory_usage'] ?> / <?= $health['memory_limit'] ?></span>
      </div>
    </div>
  </div>

  <!-- ⑦ Sumber Referrer / Asal Pengunjung (Career Telemetry) -->
  <div class="bento-card col-4">
    <div class="stat-label" style="margin-bottom: 0.75rem;">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7.1.1l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1"/><path d="M14 11a5 5 0 0 0-7.1-.1l-2 2A5 5 0 0 0 12 20l1.1-1.1"/></svg>
      Sumber Trafik / Referrer
    </div>
    <?php if (empty($career['referrer_sources'])): ?>
      <p style="color:var(--color-text-faint); font-size:0.8rem;">Belum ada data referer.</p>
    <?php else: ?>
      <?php foreach (array_slice($career['referrer_sources'], 0, 5) as $ref): ?>
      <div class="metric-row">
        <div class="metric-name">
          <span><?= $ref['icon'] ?></span> <?= $ref['source'] ?>
        </div>
        <div class="metric-bar-wrap">
          <div class="metric-bar" style="width: <?= max(5, $ref['percentage']) ?>%; background:#8B5CF6;"></div>
        </div>
        <div class="metric-count"><?= $ref['percentage'] ?>%</div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- ⑧ Asal Negara & Kota Pengunjung -->
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
          <span>🌍</span> <?= e(mb_strimwidth($c['country'], 0, 15, '…')) ?>
        </div>
        <div class="metric-bar-wrap">
          <div class="metric-bar" style="width: <?= $pct ?>%;"></div>
        </div>
        <div class="metric-count"><?= $c['hits'] ?> <span style="font-size:0.65rem; color:var(--color-text-faint); font-weight:normal;">(<?= $c['unique_ips'] ?> IP)</span></div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($visitor['top_cities'])): ?>
    <div style="margin-top: 0.65rem; padding-top: 0.45rem; border-top: 1px solid var(--color-line);">
      <div style="display: flex; flex-wrap: wrap; gap: 0.25rem;">
        <?php foreach (array_slice($visitor['top_cities'], 0, 3) as $ct): ?>
        <span class="vis-badge" style="font-size: 0.65rem;">📍 <?= e($ct['city']) ?> (<?= $ct['hits'] ?>)</span>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- ⑨ Perangkat & Browser -->
  <div class="bento-card col-4">
    <div class="stat-label" style="margin-bottom: 0.75rem;">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
      Perangkat &amp; Browser
    </div>
    <!-- Devices -->
    <div style="display: flex; gap: 0.4rem; margin-bottom: 0.65rem;">
      <?php 
      $devIcons = ['Desktop' => '💻', 'Mobile' => '📱', 'Tablet' => '📟'];
      foreach ($visitor['device_breakdown'] as $dev): 
        $icon = $devIcons[$dev['name']] ?? '🖥️';
      ?>
      <div style="flex:1; background:var(--color-bg-surface); border:1px solid var(--color-line); border-radius:6px; padding:0.4rem 0.35rem; text-align:center;">
        <div style="font-size:0.85rem;"><?= $icon ?></div>
        <div style="font-size:0.75rem; font-weight:700; color:var(--color-text); margin:0.1rem 0;"><?= $dev['percentage'] ?>%</div>
        <div style="font-size:0.62rem; color:var(--color-text-faint);"><?= $dev['name'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <!-- Browsers -->
    <div>
      <?php 
      $bIcons = ['Chrome' => '🌐', 'Safari' => '🧭', 'Edge' => '🌊', 'Firefox' => '🦊', 'Opera' => '🔴', 'Other' => '🔌'];
      foreach (array_slice($visitor['browser_breakdown'], 0, 3) as $br): 
      ?>
      <div class="metric-row" style="margin-bottom: 0.3rem;">
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

  <!-- ⑩ Audit Kesehatan SEO & Sitemap -->
  <div class="bento-card col-4">
    <div class="stat-label" style="margin-bottom: 0.75rem;">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      Audit SEO &amp; Sitemap
    </div>
    <div style="display: flex; align-items: flex-end; gap: 0.5rem; margin-bottom: 0.4rem;">
      <div class="stat-value" style="font-size: 1.9rem;"><?= $seoHealth['score'] ?>%</div>
      <div class="stat-sub" style="margin-bottom: 0.25rem;">Kesiapan Search Engine</div>
    </div>
    <div class="progress-bar-bg">
      <div class="progress-bar-fill" style="width: <?= $seoHealth['score'] ?>%; background: linear-gradient(90deg, #10B981, #34D399);"></div>
    </div>
    <div style="margin-top: 0.65rem; display: flex; flex-direction: column; gap: 0.25rem; font-size: 0.73rem;">
      <div style="display:flex; justify-content:space-between; color:var(--color-text-dim);">
        <span>Sitemap XML &amp; Robots:</span>
        <span style="color: <?= $seoHealth['checks']['sitemap'] ? '#34D399' : '#EF4444' ?>;"><?= $seoHealth['checks']['sitemap'] ? '✓ Aktif' : '✗ Belum' ?></span>
      </div>
      <div style="display:flex; justify-content:space-between; color:var(--color-text-dim);">
        <span>Meta Tag &amp; Deskripsi:</span>
        <span style="color: <?= $seoHealth['checks']['meta_bio'] ? '#34D399' : '#EF4444' ?>;"><?= $seoHealth['checks']['meta_bio'] ? '✓ Lengkap' : '✗ Kurang' ?></span>
      </div>
      <div style="display:flex; justify-content:space-between; color:var(--color-text-dim);">
        <span>OpenGraph Social Image:</span>
        <span style="color: <?= $seoHealth['checks']['og_photo'] ? '#34D399' : '#EF4444' ?>;"><?= $seoHealth['checks']['og_photo'] ? '✓ Ada' : '✗ Belum' ?></span>
      </div>
    </div>
  </div>

  <!-- ⑪ Keamanan & Riwayat Login Admin -->
  <div class="bento-card col-4">
    <div class="stat-label" style="margin-bottom: 0.75rem;">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      Keamanan &amp; Aktivitas Login
    </div>
    <div style="margin-bottom: 0.6rem;">
      <span class="vis-badge" style="background:rgba(76,141,255,0.1); border-color:rgba(76,141,255,0.3); color:#90CAF9; font-size:0.7rem;">
        🛡️ Brute-Force Shield Active
      </span>
      <?php if ($security['failed_logins_today'] > 0): ?>
        <span class="vis-badge" style="background:rgba(239,68,68,0.1); border-color:rgba(239,68,68,0.3); color:#F87171; font-size:0.7rem;">
          ⚠️ <?= $security['failed_logins_today'] ?> gagal hari ini
        </span>
      <?php endif; ?>
    </div>
    <div style="font-size:0.68rem; font-weight:700; color:var(--color-text-faint); text-transform:uppercase; margin-bottom:0.3rem;">Riwayat Login Terakhir:</div>
    <div style="display: flex; flex-direction: column; gap: 0.35rem;">
      <?php if (empty($security['recent_logins'])): ?>
        <p style="color:var(--color-text-faint); font-size:0.75rem;">Belum ada catatan login.</p>
      <?php else: ?>
        <?php foreach (array_slice($security['recent_logins'], 0, 3) as $log): ?>
        <div style="display:flex; align-items:center; justify-content:space-between; font-size:0.72rem; padding:0.25rem 0; border-bottom:1px solid rgba(255,255,255,0.03);">
          <div>
            <span style="font-weight:600; color:var(--color-text);"><?= e($log['username']) ?></span>
            <span style="color:var(--color-text-faint); font-size:0.65rem;">(<?= e($log['ip_masked']) ?>)</span>
          </div>
          <span style="font-size:0.65rem; color:<?= $log['status'] === 'SUCCESS' ? '#34D399' : '#EF4444' ?>; font-weight:700;">
            <?= $log['status'] === 'SUCCESS' ? '✓ OK' : '✗ Gagal' ?>
          </span>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- ⑫ Admin Quick Scratchpad / To-Do List (Autosave) -->
  <div class="bento-card col-4">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 0.65rem;">
      <div class="stat-label" style="margin-bottom:0;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
        Admin Scratchpad / Notes
      </div>
      <span id="scratchpadStatus" style="font-size:0.65rem; color:var(--color-text-faint);">Tersimpan</span>
    </div>
    <textarea id="adminScratchpad" class="scratchpad-area" placeholder="Tulis ide proyek baru, catatan meeting, atau to-do list di sini..."><?= e($notes) ?></textarea>
  </div>

  <!-- ⑬ Live Log Pengunjung Terkini (Real-time Feed) -->
  <div class="bento-card col-12">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 0.75rem;">
      <div class="stat-label" style="margin-bottom: 0;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
        Log Kunjungan Terbaru (Live Telemetry Feed)
      </div>
      <div style="display: flex; gap: 0.5rem; align-items: center;">
        <span style="font-size: 0.7rem; color: var(--color-accent-bright); background: rgba(76,141,255,0.1); padding: 0.15rem 0.5rem; border-radius: 999px;">Realtime Data</span>
      </div>
    </div>

    <?php if (empty($visitor['recent_visitors'])): ?>
      <p style="color: var(--color-text-faint); font-size: 0.8rem; padding: 1rem 0;">Belum ada log kunjungan.</p>
    <?php else: ?>
      <div style="overflow-x: auto;">
        <table class="vis-table">
          <thead>
            <tr>
              <th>IP Masked</th>
              <th>Lokasi (Negara / Kota)</th>
              <th>Perangkat &amp; OS</th>
              <th>Browser</th>
              <th>Halaman</th>
              <th>Waktu</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($visitor['recent_visitors'] as $v): ?>
            <tr>
              <td><code style="font-size:0.72rem; color:var(--color-accent-bright); background:transparent; border:none; padding:0;"><?= e($v['ip_masked']) ?></code></td>
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
              <td><span title="<?= e($v['page']) ?>" style="max-width:140px; display:inline-block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= e($v['page']) ?></span></td>
              <td style="font-size:0.68rem; color:var(--color-text-faint); white-space:nowrap;"><?= date('d M — H:i:s', strtotime($v['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- ⑭ Pesan Buku Tamu Terkini dengan Quick Reply WhatsApp & Email -->
  <div class="bento-card col-6">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 0.75rem;">
      <div class="stat-label" style="margin-bottom:0;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        Pesan Buku Tamu &amp; Quick Reply
      </div>
      <a href="<?= BASE_URL ?>/admin/guestbook" class="btn btn-secondary btn-sm" style="font-size:0.72rem; padding:0.25rem 0.65rem;">Semua Pesan</a>
    </div>
    <?php if (empty($recentGuestbook)): ?>
      <p style="color: var(--color-text-faint); font-size: 0.82rem;">Belum ada pesan.</p>
    <?php else: ?>
      <?php foreach ($recentGuestbook as $gb): ?>
      <div class="gb-item">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <div class="gb-name"><?= e($gb['user_name']) ?></div>
          <span style="font-size:0.65rem; color:var(--color-text-faint);"><?= e($gb['created_at'] ?? '') ?></span>
        </div>
        <div class="gb-msg"><?= e(mb_strimwidth($gb['message'], 0, 110, '…')) ?></div>
        <div class="gb-actions">
          <?php if (!empty($gb['user_email'])): ?>
          <a href="mailto:<?= e($gb['user_email']) ?>?subject=Re:%20Pesan%20Portofolio%20Ammar%20Syarif&amp;body=Halo%20<?= urlencode($gb['user_name']) ?>," class="vis-badge" style="text-decoration:none; color:var(--color-accent-bright);">
            ✉️ Balas Email
          </a>
          <?php endif; ?>
          <?php if (!empty($profile['whatsapp_url'])): ?>
          <a href="<?= e($profile['whatsapp_url']) ?>" target="_blank" class="vis-badge" style="text-decoration:none; color:#34D399;">
            💬 WhatsApp
          </a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- ⑮ Proyek Terkini -->
  <div class="bento-card col-6">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 0.75rem;">
      <div class="stat-label" style="margin-bottom:0;">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        Proyek Portofolio
      </div>
      <a href="<?= BASE_URL ?>/admin/projects" class="btn btn-secondary btn-sm" style="font-size:0.72rem; padding:0.25rem 0.65rem;">Kelola Proyek</a>
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
          <a href="<?= BASE_URL ?>/admin/project-form?id=<?= (int)$p['id'] ?>" style="margin-left:auto; font-size:0.72rem; color: var(--color-accent-bright); flex-shrink:0;">Edit</a>
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
  // Chart.js render
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

  // Autosave Scratchpad Notes
  const scratchpad = document.getElementById('adminScratchpad');
  const scratchpadStatus = document.getElementById('scratchpadStatus');
  let saveTimeout = null;

  if (scratchpad) {
    scratchpad.addEventListener('input', function() {
      scratchpadStatus.textContent = 'Menyimpan...';
      scratchpadStatus.style.color = '#FCD34D';
      clearTimeout(saveTimeout);
      saveTimeout = setTimeout(function() {
        const formData = new FormData();
        formData.append('notes', scratchpad.value);
        fetch('<?= BASE_URL ?>/admin/api_notes.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            scratchpadStatus.textContent = '✓ Tersimpan';
            scratchpadStatus.style.color = '#34D399';
            setTimeout(() => {
              scratchpadStatus.textContent = 'Tersimpan';
              scratchpadStatus.style.color = 'var(--color-text-faint)';
            }, 2000);
          } else {
            scratchpadStatus.textContent = '✗ Gagal simpan';
            scratchpadStatus.style.color = '#EF4444';
          }
        })
        .catch(() => {
          scratchpadStatus.textContent = '✗ Offline';
          scratchpadStatus.style.color = '#EF4444';
        });
      }, 800);
    });
  }

  // Toggle Hire Status (Available for Hire / Busy)
  const btnToggleHire = document.getElementById('btnToggleHireStatus');
  const hireDot = document.getElementById('hireStatusDot');
  const hireText = document.getElementById('hireStatusText');

  if (btnToggleHire) {
    btnToggleHire.addEventListener('click', function() {
      btnToggleHire.style.opacity = '0.5';
      btnToggleHire.disabled = true;

      fetch('<?= BASE_URL ?>/admin/api_hire_status.php', {
        method: 'POST'
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          hireDot.style.background = data.color;
          hireText.style.color = data.color;
          hireText.textContent = data.short;
          btnToggleHire.style.borderColor = data.color;
        }
      })
      .catch(err => console.error('Hire toggle error:', err))
      .finally(() => {
        btnToggleHire.style.opacity = '1';
        btnToggleHire.disabled = false;
      });
    });
  }
})();
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
