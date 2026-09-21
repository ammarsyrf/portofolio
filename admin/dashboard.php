<?php
/**
 * Admin Dashboard — Bento Grid
 * Stats: Proyek, Pencapaian, Kreasi, Buku Tamu, Tautan
 *        Visitor Analytics (7 hari), Profile Kelengkapan, Pesan Terkini
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

// ── Visitor Stats ────────────────────────────────────────────────────────────
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
    padding: 1.4rem 1.5rem;
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
    font-size: 2.4rem;
    font-weight: 700;
    color: var(--color-text);
    line-height: 1;
    margin-bottom: 0.3rem;
  }
  .stat-sub {
    font-size: 0.75rem;
    color: var(--color-text-faint);
  }
  .stat-icon {
    position: absolute;
    bottom: 1rem;
    right: 1.25rem;
    opacity: 0.07;
    font-size: 4rem;
    pointer-events: none;
  }

  /* Chart area */
  .chart-container {
    position: relative;
    height: 130px;
    margin-top: 0.5rem;
  }

  /* Progress bar profil */
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
    gap: 0.5rem;
  }
  .quick-link {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    padding: 0.6rem 0.8rem;
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
    padding: 0.7rem 0;
    border-bottom: 1px solid var(--color-line);
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
  }
  .gb-item:last-child { border-bottom: none; }
  .gb-name { font-size: 0.82rem; font-weight: 600; color: var(--color-text); }
  .gb-msg  { font-size: 0.76rem; color: var(--color-text-faint); }
  .gb-time { font-size: 0.68rem; color: var(--color-text-faint); opacity: 0.6; }

  /* Alert strip */
  .alert-strip {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.55rem 0.9rem;
    border-radius: 6px;
    font-size: 0.78rem;
    margin-bottom: 0.5rem;
  }
  .alert-info    { background: rgba(76,141,255,0.10); border: 1px solid rgba(76,141,255,0.25); color: #90CAF9; }
  .alert-warning { background: rgba(245,158,11,0.10); border: 1px solid rgba(245,158,11,0.25); color: #FCD34D; }
  .alert-success { background: rgba(16,185,129,0.10); border: 1px solid rgba(16,185,129,0.25); color: #6EE7B7; }
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
    Ringkasan portofolio &amp; aktivitas hari ini.
  </p>
</div>

<?php if (!$visitor['table_exists']): ?>
<div class="alert-strip alert-warning" style="margin-bottom: 1.25rem;">
  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
  Tabel <code>page_views</code> belum dibuat. Jalankan <code>database/migrations/002_add_page_views.sql</code> untuk mengaktifkan statistik pengunjung.
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════════════ -->
<!--  BENTO GRID                                                   -->
<!-- ══════════════════════════════════════════════════════════════ -->
<div class="bento-grid">

  <!-- ① Proyeks: Stat Card -->
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

  <!-- ③ Visitor Hari Ini -->
  <div class="bento-card col-3">
    <div class="stat-label">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
      Pengunjung Hari Ini
    </div>
    <div class="stat-value"><?= number_format($visitor['today']) ?></div>
    <div class="stat-sub"><?= $visitor['unique_today'] ?> sesi unik · kemarin <?= $visitor['yesterday'] ?></div>
    <div class="stat-icon">📈</div>
  </div>

  <!-- ④ Buku Tamu -->
  <div class="bento-card col-3">
    <div class="stat-label">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      Buku Tamu
    </div>
    <div class="stat-value"><?= $totalGuest ?></div>
    <div class="stat-sub"><?= $newGuest ?> pesan masuk hari ini</div>
    <div class="stat-icon">💬</div>
  </div>

  <!-- ⑤ Chart Visitor 7 Hari -->
  <div class="bento-card col-8">
    <div class="stat-label" style="margin-bottom: 0.75rem;">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Trafik 7 Hari Terakhir
    </div>
    <?php if ($visitor['table_exists'] && !empty($visitor['trend'])): ?>
    <div class="chart-container">
      <canvas id="visitorChart"></canvas>
    </div>
    <?php else: ?>
    <div style="height:130px; display:flex; align-items:center; justify-content:center; color: var(--color-text-faint); font-size:0.8rem;">
      Belum ada data trafik. Pastikan migration sudah dijalankan &amp; <code>track_page_view()</code> dipanggil di halaman publik.
    </div>
    <?php endif; ?>
  </div>

  <!-- ⑥ Quick Actions -->
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

  <!-- ⑦ Kelengkapan Profil -->
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
    <div style="margin-top: 0.9rem; display: flex; flex-direction: column; gap: 0.3rem;">
      <?php foreach ([
        ['Email'      , !empty($profile['email'])],
        ['Bio'        , !empty($profile['bio'])],
        ['GitHub URL' , !empty($profile['github_url'])],
        ['Tagline'    , !empty($profile['tagline'])],
      ] as [$label, $ok]): ?>
      <div style="display:flex; align-items:center; gap:0.5rem; font-size:0.75rem; color: var(--color-text-faint);">
        <span style="color: <?= $ok ? '#34D399' : '#EF4444' ?>; font-weight:700;"><?= $ok ? '✓' : '✗' ?></span>
        <?= $label ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ⑧ Konten Overview -->
  <div class="bento-card col-4">
    <div class="stat-label" style="margin-bottom: 0.75rem;">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Ringkasan Konten
    </div>
    <div style="display: flex; flex-direction: column; gap: 0.65rem;">
      <?php foreach ([
        ['Kreasi TikTok & IG', $totalCreations, $creationsVisible ? '👁 Ditampilkan' : '🚫 Disembunyikan'],
        ['Tautan Aktif'       , $totalLinks     , 'link aktif'],
        ['Pengunjung Minggu Ini', $visitor['this_week'], 'pageviews'],
      ] as [$lbl, $val, $sub]): ?>
      <div style="display:flex; align-items:center; justify-content:space-between; padding:0.5rem 0; border-bottom: 1px solid var(--color-line);">
        <div>
          <div style="font-size:0.78rem; color: var(--color-text-dim); font-weight:500;"><?= $lbl ?></div>
          <div style="font-size:0.7rem; color: var(--color-text-faint);"><?= $sub ?></div>
        </div>
        <div style="font-family: var(--font-display); font-size: 1.4rem; font-weight: 700; color: var(--color-text);"><?= number_format($val) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ⑨ Pesan Buku Tamu Terkini -->
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

  <!-- ⑩ Proyek Terkini -->
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
          backgroundColor: 'rgba(76,141,255,0.5)',
          borderColor: 'rgba(76,141,255,0.9)',
          borderWidth: 1.5,
          borderRadius: 4,
          order: 2,
        },
        {
          label: 'Sesi Unik',
          data: unique,
          type: 'line',
          borderColor: '#34D399',
          backgroundColor: 'rgba(52,211,153,0.15)',
          borderWidth: 2,
          pointRadius: 3,
          pointBackgroundColor: '#34D399',
          fill: true,
          tension: 0.35,
          order: 1,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: {
          labels: {
            color: 'rgba(243,236,221,0.5)',
            font: { size: 10 },
            boxWidth: 10,
            padding: 12,
          }
        },
        tooltip: {
          backgroundColor: 'rgba(15,20,30,0.92)',
          titleColor: '#fff',
          bodyColor: 'rgba(243,236,221,0.7)',
          borderColor: 'rgba(76,141,255,0.3)',
          borderWidth: 1,
          padding: 10,
          cornerRadius: 6,
        }
      },
      scales: {
        x: {
          grid: { color: 'rgba(255,255,255,0.04)' },
          ticks: { color: 'rgba(243,236,221,0.4)', font: { size: 10 } }
        },
        y: {
          beginAtZero: true,
          grid: { color: 'rgba(255,255,255,0.06)' },
          ticks: {
            color: 'rgba(243,236,221,0.4)',
            font: { size: 10 },
            stepSize: 1,
            precision: 0,
          }
        }
      }
    }
  });
})();
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
