<?php
/**
 * Halaman Utama (Home) — Portfolio Ammar Syarif
 * Desain: Dark Dashboard / Glass Interface (DESIGN.md)
 * Navigasi: includes/nav.php | Command Palette: includes/command_palette.php
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/homepage_content.php';
require_once __DIR__ . '/includes/seo.php';

// Catat kunjungan halaman untuk statistik dashboard
track_page_view($pdo);

// Ambil data profil dari database
$profile = get_profile($pdo);
$homeContent = get_homepage_content($pdo);
$techChipsRaw = json_decode($homeContent['tech_chips_json'] ?? '[]', true);
$techChips = (is_array($techChipsRaw) && count($techChipsRaw) >= 20) ? $techChipsRaw : json_decode(homepage_content_defaults()['tech_chips_json'], true);

require_once __DIR__ . '/includes/tech_icons.php';

if (!function_exists('render_skill_badges_from_csv')) {
    function render_skill_badges_from_csv(string $csv): string {
        $tags = array_filter(array_map('trim', explode(',', $csv)));
        $html = '';
        foreach ($tags as $tag) {
            $svg = get_tech_svg_icon($tag, 14, 'badge-tech-svg');
            $cleanDisplay = preg_replace('/^[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{25A0}-\x{25FF}\x{2B50}\x{2300}-\x{23FF}\x{2190}-\x{21FF}\x{FE00}-\x{FE0F}\s▲🔴⚡🐬🔗🟡⚛️🌿🐧🎨🟢🐳🐍☁️⭐]+/u', '', $tag);
            $cleanDisplay = trim((string)$cleanDisplay);
            $displayTag = !empty($cleanDisplay) ? $cleanDisplay : $tag;
            $html .= '<span class="skill-badge"><span class="badge-icon-wrap">' . $svg . '</span><span class="badge-tag-text">' . htmlspecialchars($displayTag) . '</span></span>';
        }
        return $html;
    }
}

if (!function_exists('render_tier_chips_from_csv')) {
    function render_tier_chips_from_csv(string $csv, bool $highlight = false): string {
        $tags = array_filter(array_map('trim', explode(',', $csv)));
        $html = '';
        foreach ($tags as $tag) {
            $hl = $highlight ? ' highlight' : '';
            $svg = get_tech_svg_icon($tag, 14, 'tier-tech-svg');
            $cleanDisplay = preg_replace('/^[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{25A0}-\x{25FF}\x{2B50}\x{2300}-\x{23FF}\x{2190}-\x{21FF}\x{FE00}-\x{FE0F}\s▲🔴⚡🐬🔗🟡⚛️🌿🐧🎨🟢🐳🐍☁️⭐]+/u', '', $tag);
            $cleanDisplay = trim((string)$cleanDisplay);
            $displayTag = !empty($cleanDisplay) ? $cleanDisplay : $tag;
            $html .= '<span class="tier-chip' . $hl . '"><span class="tier-icon-wrap">' . $svg . '</span><span>' . htmlspecialchars($displayTag) . '</span></span>';
        }
        return $html;
    }
}


// Fallback profil jika database kosong
if (!$profile) {
    $profile = [
        'full_name' => 'Ammar Syarif',
        'role_title' => 'Web Developer & Data Analyst',
        'tagline' => 'Membangun sistem yang rapi dan mengubah data jadi keputusan.',
        'about' => 'Lulusan S1 Sistem Informasi dengan pengalaman membangun berbagai sistem bisnis dari nol — mulai dari platform booking, sistem akademik, hingga riset klasifikasi data. Terbiasa mengerjakan proyek end-to-end: dari desain database, backend, sampai tampilan yang siap dipakai klien.',
        'photo' => '',
        'cv_file' => '',
        'email' => 'zentokun90@gmail.com',
        'linkedin' => 'https://linkedin.com/in/zentokun90',
        'github' => 'https://github.com/ammarsyrf',
        'instagram' => 'https://instagram.com/zentokun90',
        'tiktok' => 'https://tiktok.com/@zentokun90',
        'skills' => 'Laravel, PHP, MySQL, Redis, Next.js, React, Tailwind CSS, TypeScript, Docker, Linux, Pest, Flutter, Algoritma C4.5, S1 Sistem Informasi'
    ];
}

// Ambil proyek berstatus publish
$projects = get_published_projects($pdo);

// Persiapkan array JSON untuk modal dialog
$projectsPayload = [];
foreach ($projects as $proj) {
    $projectsPayload[] = [
        'id' => (int)$proj['id'],
        'title' => $proj['title'],
        'category' => $proj['category'],
        'summary' => $proj['summary'],
        'description' => $proj['description'],
        'tech_stack' => $proj['tech_stack'],
        'my_role' => $proj['my_role'],
        'result_impact' => $proj['result_impact'],
        'image_url' => !empty($proj['image']) ? upload_url('projects', $proj['image']) : '',
        'demo_link' => $proj['demo_link'],
        'repo_link' => $proj['repo_link']
    ];
}

// Persiapkan featured project & media projects untuk Bento Grid
$featuredProject = !empty($projects) ? $projects[0] : null;
$featuredId = $featuredProject ? (int)$featuredProject['id'] : 0;
$featuredImg = ($featuredProject && !empty($featuredProject['image']) && file_exists(UPLOAD_DIR_PROJECTS . '/' . basename($featuredProject['image'])))
    ? upload_url('projects', $featuredProject['image'])
    : '';

$mediaProjects = array_slice($projects, 0, 3);
$heroPhoto = !empty($profile['photo']) && file_exists(UPLOAD_DIR_PHOTOS . '/' . basename($profile['photo']))
    ? upload_url('photos', $profile['photo'])
    : '';

// Cek file CV
$hasCv = false;
$cvUrl = '#';
if (!empty($profile['cv_file'])) {
    $cvPath = UPLOAD_DIR_CV . '/' . basename($profile['cv_file']);
    if (file_exists($cvPath)) {
        $hasCv = true;
        $cvUrl = BASE_URL . '/download-cv.php';
    }
}
$hireStatus = get_hire_status($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($profile['full_name']) ?> — <?= e($profile['role_title']) ?> | Zenerie</title>
  <meta name="description" content="Portofolio resmi <?= e($profile['full_name']) ?> di Zenerie. <?= e($profile['role_title']) ?> — <?= e($profile['tagline']) ?>">
  <meta name="author" content="<?= e($profile['full_name']) ?>">
  <?php render_seo(
      $profile,
      $profile['full_name'] . ' — ' . $profile['role_title'] . ' | Zenerie',
      'Portofolio resmi ' . $profile['full_name'] . ' di Zenerie. ' . $profile['role_title'] . ' — ' . $profile['tagline'],
      '/'
  ); ?>

  <!-- Google Fonts: Space Grotesk (Heading) & Inter (Body/UI) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">

  <!-- Main Stylesheet (Dark Glassmorphism Bento) -->
  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

  <!-- Global Sticky Nav -->
  <?php require_once __DIR__ . '/includes/nav.php'; ?>

<main id="top">
    <h1 class="sr-only"><?= e($profile['full_name']) ?> — <?= e($profile['role_title']) ?></h1>
    <!-- ====================================================================
         SECTION 1: BENTO DASHBOARD HERO (Inspired by Bento Widget Layout)
         ==================================================================== -->
    <section id="hero" class="bento-master-canvas">
      <div class="site-container">
        <div class="bento-frame">
          
          <!-- TOP BENTO TIER -->
          <div class="bento-top-tier">
            
            <!-- Left Column: Stack of 3 Bento Cards -->
            <div class="bento-left-col">
              
              <!-- Card 1: Stats & Info Widget -->
              <div class="bento-card bento-card-stats">
                <div class="bento-stats-left">
                  <span class="bento-label"><?= e($homeContent['hero_education_label']) ?></span>
                  <div class="bento-big-stat"><?= e($homeContent['hero_degree']) ?></div>
                  <span class="bento-sub-stat"><?= e($homeContent['hero_field']) ?></span>
                </div>
                <div class="bento-stats-right">
                  <div class="bento-meta-pill">
                    <span class="bento-pill-tag"><?= e($homeContent['hero_specialty_label']) ?></span>
                    <span class="bento-pill-val"><?= e($homeContent['hero_specialty_value']) ?></span>
                  </div>
                  <div class="bento-meta-pill">
                    <span class="bento-pill-tag"><?= e($homeContent['hero_availability_label']) ?></span>
                    <span class="bento-pill-val" id="publicHireStatus" style="color: <?= e($hireStatus['color']) ?>; font-weight:600;"><?= e($hireStatus['label']) ?></span>
                  </div>
                  <div class="bento-progress-row">
                    <div class="bento-progress-track" title="Tingkat Kesiapan Teknis">
                      <div class="bento-progress-fill" style="width: <?= e($homeContent['hero_readiness']) ?>%;"></div>
                    </div>
                    <a href="#about" class="bento-arrow-btn" title="Lihat Profil">➔</a>
                  </div>
                </div>
              </div>

              <!-- Card 2: Our Brand — Zenerie -->
              <div class="bento-card bento-card-player bento-card-brand">
                <a href="<?= e($homeContent['brand_url']) ?>" target="_blank" rel="noopener noreferrer" class="bento-player-thumb-wrap bento-brand-logo-link" aria-label="Kunjungi website Zenerie">
                  <img src="<?= asset('uploads/photos/zenerie-logo.jpg') ?>" alt="Logo Zenerie" class="bento-player-thumb bento-brand-logo">
                </a>
                <div class="bento-player-info">
                  <div class="bento-player-header">
                    <span class="bento-player-title"><?= e($homeContent['brand_title']) ?> <span style="color: var(--color-accent);">♥</span></span>
                    <span class="bento-player-badge"><?= e($homeContent['brand_badge']) ?></span>
                  </div>
                  <div class="bento-player-subtitle"><?= e($homeContent['brand_description']) ?></div>
                  <a href="<?= e($homeContent['brand_url']) ?>" target="_blank" rel="noopener noreferrer" class="bento-brand-visit"><?= e($homeContent['brand_button']) ?> <span aria-hidden="true">↗</span></a>
                </div>
              </div>

              <!-- Card 3: Identity & Bio Card (✦ Ammar Syarif ✦) -->
              <div class="bento-card bento-card-identity">
                <div class="bento-identity-title">
                  <span>✦</span>
                  <h3><?= e($profile['full_name']) ?></h3>
                  <span>✦</span>
                </div>
                <p class="bento-identity-desc">
                  ♡ <?= e($profile['tagline']) ?>
                </p>
                <div class="bento-identity-actions">
                  <?php if ($hasCv): ?>
                    <a href="<?= e($cvUrl) ?>" download class="btn-bento-pill primary" id="btn-download-cv"><?= e($homeContent['identity_button']) ?></a>
                  <?php else: ?>
                    <a href="#about" class="btn-bento-pill primary"><?= e($homeContent['identity_button']) ?></a>
                  <?php endif; ?>
                  <span class="bento-dots-menu" title="Menu Opsi">•••</span>
                </div>
              </div>

            </div>

            <!-- Right Column: Visual Anchor / Big Hero Typo & Portrait -->
            <div class="bento-hero-visual">
              
              <!-- Large Styled Background Typo -->
              <div class="bento-hero-typography">
                <span class="typo-line"><?= e($homeContent['hero_line_one']) ?></span>
                <span class="typo-line accent"><?= e($homeContent['hero_line_two']) ?></span>
                <span class="typo-line"><?= e($homeContent['hero_line_three']) ?></span>
                <div class="typo-pill-badge"><?= e($homeContent['hero_badge']) ?></div>
              </div>

              <!-- Portrait Frame -->
              <div class="bento-portrait-frame">
                <?php if (!empty($heroPhoto)): ?>
                  <img src="<?= e($heroPhoto) ?>" alt="<?= e($profile['full_name']) ?>" class="bento-portrait-img">
                <?php else: ?>
                  <div class="bento-portrait-placeholder">
                    <div class="bento-avatar-circle">AS</div>
                    <div style="font-family: var(--font-display); font-weight: 700; color: #fff; font-size: 1.1rem;"><?= e($profile['full_name']) ?></div>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Decorative plus accents & dot matrix -->
              <div class="bento-decor-plus top-right">+</div>
              <div class="bento-decor-plus mid-left">+</div>
              <div class="bento-decor-dots">:::</div>

              <!-- Vertical Right Badge (01. My Profile ♡) -->
              <div class="bento-vertical-badge">
                <span><?= e($homeContent['hero_vertical_badge']) ?></span>
              </div>

              <!-- Floating Quick Action Buttons on right edge -->
              <div class="bento-floating-actions">
                <a href="#contact" class="bento-float-btn" title="Rekrut / Kontak">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                  </svg>
                </a>

                <a href="#contact" class="bento-float-btn" title="Kirim Pesan">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                  </a>
              </div>

            </div>

          </div>

          <!-- BOTTOM BENTO TIER (Mini Dock + Interactive Sliding Deck Stage) -->
          <div class="bento-bottom-tier">
            
            <!-- Mini Dock Pill (Leftmost Nav Sidebar) -->
            <div class="bento-mini-dock" id="bentoMiniDock" role="tablist" aria-label="Navigasi Bento Deck">
              <button type="button" class="dock-item active" data-deck-index="0" role="tab" aria-selected="true" aria-controls="deck-panel-0" title="Ringkasan (Beranda)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                  <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
              </button>
              <button type="button" class="dock-item" data-deck-index="1" role="tab" aria-selected="false" aria-controls="deck-panel-1" title="Tentang Saya & Nilai Rekayasa">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
              </button>
              <button type="button" class="dock-item" data-deck-index="2" role="tab" aria-selected="false" aria-controls="deck-panel-2" title="Keahlian & Domain Teknis">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="20" x2="18" y2="10"/>
                  <line x1="12" y1="20" x2="12" y2="4"/>
                  <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
              </button>
              <button type="button" class="dock-item" data-deck-index="3" role="tab" aria-selected="false" aria-controls="deck-panel-3" title="Karya & Proyek Terpilih">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
              </button>
              <button type="button" class="dock-item" data-deck-index="4" role="tab" aria-selected="false" aria-controls="deck-panel-4" title="Gear & Workspace Setup (Hardware & Software)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="12" rx="2"/>
                  <line x1="2" y1="20" x2="22" y2="20"/>
                </svg>
              </button>
              <button type="button" class="dock-item" data-deck-index="5" role="tab" aria-selected="false" aria-controls="deck-panel-5" title="Kontak & Kanal Profesional">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                  <polyline points="22,6 12,13 2,6"/>
                </svg>
              </button>
            </div>

            <!-- Sliding Deck Viewport -->
            <div class="bento-deck-stage" id="bentoDeckStage">
              <div class="bento-deck-track" id="bentoDeckTrack">

                <!-- ==============================================
                     PANEL 0: OVERVIEW (Default Dashboard)
                     ============================================== -->
                <div class="bento-deck-panel" id="deck-panel-0" role="tabpanel" data-panel="0">
                  
                  <!-- Widget 1: About Me (Clean Bio & Credentials) -->
                  <div class="bento-card bento-card-about">
                    <div class="bento-about-header">
                      <div class="bento-card-heading">
                        <h4><?= e($homeContent['about_widget_title']) ?></h4>
                      </div>
                      <span class="bento-mini-status-pill">
                        <span class="pulse-dot-green"></span> <?= e($homeContent['about_widget_status']) ?>
                      </span>
                    </div>

                    <div class="bento-about-body">
                      <p class="bento-about-text">
                        <?= e($homeContent['about_widget_text_one']) ?>
                      </p>
                      <p class="bento-about-text">
                        <?= e($homeContent['about_widget_text_two']) ?>
                      </p>
                    </div>

                    <div class="bento-about-metrics-strip">
                      <div class="about-metric-unit">
                        <span class="m-val"><?= e($homeContent['about_metric_one_value']) ?></span>
                        <span class="m-lbl"><?= e($homeContent['about_metric_one_label']) ?></span>
                      </div>
                      <div class="about-metric-sep"></div>
                      <div class="about-metric-unit">
                        <span class="m-val"><?= e($homeContent['about_metric_two_value']) ?></span>
                        <span class="m-lbl"><?= e($homeContent['about_metric_two_label']) ?></span>
                      </div>
                      <div class="about-metric-sep"></div>
                      <div class="about-metric-unit">
                        <span class="m-val">Fullstack</span>
                        <span class="m-lbl">&amp; Analitik</span>
                      </div>
                    </div>
                  </div>

                  <!-- Widget 2: Live Terminal & Work Status Console -->
                  <div class="bento-card bento-card-terminal" id="bentoTerminalCard">
                    <!-- Terminal Window Top Bar -->
                    <div class="terminal-topbar">
                      <div class="terminal-window-controls">
                        <span class="t-dot t-dot-red" title="Close"></span>
                        <span class="t-dot t-dot-yellow" title="Minimize"></span>
                        <span class="t-dot t-dot-green" title="Expand"></span>
                      </div>
                      <div class="terminal-title">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="terminal-shell-icon">
                          <polyline points="4 17 10 11 4 5"></polyline>
                          <line x1="12" y1="19" x2="20" y2="19"></line>
                        </svg>
                        <span><?= e($homeContent['terminal_title']) ?></span>
                      </div>
                      <div class="terminal-status-pill">
                        <span class="terminal-pulse-dot"></span>
                        <span><?= e($homeContent['terminal_status_badge']) ?></span>
                      </div>
                    </div>

                    <!-- Terminal Interactive Console Screen -->
                    <div class="terminal-screen" id="terminalScreen">
                      <div class="term-line term-prompt-line">
                        <span class="term-user"><?= e($homeContent['terminal_user']) ?></span><span class="term-sep">:</span><span class="term-path">~</span><span class="term-char">$</span>
                        <span class="term-cmd-text">status --live</span>
                      </div>

                      <div class="term-code-block">
                        <div class="term-row">
                          <span class="term-k">status</span>
                          <span class="term-op">:</span>
                          <span class="term-v-status">"<?= e($homeContent['terminal_status_val']) ?>"</span>
                        </div>
                        <div class="term-row">
                          <span class="term-k">role</span>
                          <span class="term-op">:</span>
                          <span class="term-v-str">"<?= e($homeContent['terminal_role_val']) ?>"</span>
                        </div>
                        <div class="term-row">
                          <span class="term-k">focus</span>
                          <span class="term-op">:</span>
                          <span class="term-v-str">"<?= e($homeContent['terminal_focus_val']) ?>"</span>
                        </div>
                        <div class="term-row">
                          <span class="term-k">ai_stack</span>
                          <span class="term-op">:</span>
                          <span class="term-v-str">"<?= e($homeContent['terminal_ai_val']) ?>"</span>
                        </div>
                        <div class="term-row">
                          <span class="term-k">workflow</span>
                          <span class="term-op">:</span>
                          <span class="term-v-str">"<?= e($homeContent['terminal_workflow_val']) ?>"</span>
                        </div>
                        <div class="term-row">
                          <span class="term-k">location</span>
                          <span class="term-op">:</span>
                          <span class="term-v-str">"<?= e($homeContent['terminal_location_val']) ?>"</span>
                        </div>
                      </div>

                      <!-- Interactive Command Output Simulation -->
                      <div class="term-interactive-zone" id="termInteractiveZone">
                        <div class="term-line term-prompt-line">
                          <span class="term-user"><?= e($homeContent['terminal_user']) ?></span><span class="term-sep">:</span><span class="term-path">~</span><span class="term-char">$</span>
                          <span class="term-active-cmd" id="termActiveCmd"><?= e($homeContent['terminal_cmd_test']) ?></span>
                          <span class="term-cursor" id="termCursor">▋</span>
                        </div>
                        <div class="term-test-output" id="termTestOutput" style="display: none;">
                          <div class="term-test-pass">
                            <span class="term-badge-pass">PASS</span>
                            <span><?= e($homeContent['terminal_cmd_pass']) ?></span>
                          </div>
                          <div class="term-test-summary" style="display: flex; flex-direction: column; gap: 0.15rem; margin-top: 0.2rem;">
                            <span style="color: #34d399;"><?= e($homeContent['terminal_summary_line1']) ?></span>
                            <span style="color: #c084fc;"><?= e($homeContent['terminal_summary_line2']) ?></span>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Terminal Bottom Bar / Quick Action Strip -->
                    <div class="terminal-bottom-bar">
                      <button type="button" class="btn-term-run" id="btnRunTerminalTest" title="Jalankan simulasi tes performa">
                        <span class="run-icon">▶</span>
                        <span id="btnRunTestLabel">Run Test</span>
                      </button>
                      <div class="terminal-quick-actions">
                        <?php if ($hasCv): ?>
                          <a href="<?= e($cvUrl) ?>" class="term-btn-action" target="_blank" download title="Unduh Curriculum Vitae (PDF)">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                              <polyline points="7 10 12 15 17 10"></polyline>
                              <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            <span>CV</span>
                          </a>
                        <?php endif; ?>
                        <a href="#contact" class="term-btn-action term-btn-highlight" title="Kirim pesan langsung">
                          <span>Hire Me ↗</span>
                        </a>
                      </div>
                    </div>
                  </div>

                  <!-- Widget 3: Categorized Tech Stack Widget (Fixed Height & Consistent Dimensions) -->
                  <div class="bento-card bento-card-skills-widget">
                    <div class="bento-skills-header">
                      <div class="skills-widget-heading">
                        <h4><?= e($homeContent['tech_widget_title']) ?></h4>
                        <span class="skills-widget-dot"><?= e($homeContent['tech_widget_status']) ?></span>
                      </div>
                      <div class="skills-cat-tabs" id="skillCatTabs" role="tablist">
                        <button type="button" class="cat-tab-btn active" data-cat="all">Semua</button>
                        <button type="button" class="cat-tab-btn" data-cat="frontend">Frontend</button>
                        <button type="button" class="cat-tab-btn" data-cat="backend">Backend</button>
                        <button type="button" class="cat-tab-btn" data-cat="ai">AI &amp; Prompting</button>
                        <button type="button" class="cat-tab-btn" data-cat="database">Database</button>
                        <button type="button" class="cat-tab-btn" data-cat="devops">DevOps</button>
                        <button type="button" class="cat-tab-btn" data-cat="testing">Testing</button>
                        <button type="button" class="cat-tab-btn" data-cat="mobile">Mobile &amp; UI</button>
                        <button type="button" class="cat-tab-btn" data-cat="analytics">Analitik Data</button>
                      </div>
                    </div>

                    <div class="skills-icon-grid" id="skillsIconGrid">
                      <?php foreach ($techChips as $chip): 
                        $chipCat = htmlspecialchars($chip['category'] ?? 'frontend');
                        $chipHighlight = !empty($chip['highlight']) ? 'true' : 'false';
                        $chipName = htmlspecialchars($chip['name'] ?? '');
                        $chipRole = htmlspecialchars($chip['role'] ?? '');
                        $chipIcon = $chip['icon'] ?? $chipName;
                        $chipIconSvg = render_skill_chip_icon($chipIcon, $chipName);
                        $iconClass = strtolower(preg_replace('/[^a-z0-9]/', '', $chipIcon));
                      ?>
                        <div class="skill-icon-chip" data-cat="<?= $chipCat ?>" data-highlight="<?= $chipHighlight ?>" title="<?= $chipName ?> — <?= $chipRole ?>">
                          <div class="chip-svg-wrap <?= $iconClass ?>">
                            <?= $chipIconSvg ?>
                          </div>
                          <div class="chip-detail">
                            <span class="chip-name"><?= $chipName ?></span>
                            <span class="chip-sub"><?= $chipRole ?></span>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>

                    <div class="skills-widget-footer">
                      <span class="skills-footer-tag"><?= e($homeContent['tech_widget_footer']) ?></span>
                      <a href="#skills" class="bento-see-all"><?= e($homeContent['tech_widget_button']) ?></a>
                    </div>
                  </div>

                </div>

                <!-- ==============================================
                     PANEL 1: TENTANG (Profil & Standar Rekayasa)
                     ============================================== -->
                <div class="bento-deck-panel" id="deck-panel-1" role="tabpanel" data-panel="1">
                  
                  <div class="bento-card bento-card-deck deck-vision-card">
                    <div>
                      <span class="deck-eyebrow"><?= e($homeContent['deck_vision_eyebrow']) ?></span>
                      <h4 class="deck-vision-title"><?= e($homeContent['deck_vision_title']) ?></h4>
                      <p class="deck-vision-lead">
                        <?= e($homeContent['deck_vision_lead']) ?>
                      </p>
                    </div>
                    <div class="deck-focus-list">
                      <div class="deck-focus-item">
                        <span class="deck-focus-index">01</span>
                        <div><strong><?= e($homeContent['deck_s1_title']) ?></strong><span><?= e($homeContent['deck_s1_desc']) ?></span></div>
                      </div>
                      <div class="deck-focus-item">
                        <span class="deck-focus-index">02</span>
                        <div><strong><?= e($homeContent['deck_s2_title']) ?></strong><span><?= e($homeContent['deck_s2_desc']) ?></span></div>
                      </div>
                      <div class="deck-focus-item">
                        <span class="deck-focus-index">03</span>
                        <div><strong><?= e($homeContent['deck_s3_title']) ?></strong><span><?= e($homeContent['deck_s3_desc']) ?></span></div>
                      </div>
                    </div>
                    <div class="deck-card-actions">
                      <?php if ($hasCv): ?>
                        <a href="<?= e($cvUrl) ?>" download class="btn-bento-pill primary">Unduh Resume</a>
                      <?php else: ?>
                        <a href="#about" class="btn-bento-pill primary">Detail Profil</a>
                      <?php endif; ?>
                      <span class="bento-status-badge-inline">● <?= e($homeContent['about_widget_status']) ?></span>
                    </div>
                  </div>

                  <div class="bento-card bento-card-deck deck-standards-card">
                    <div class="bento-card-heading">
                      <h4><?= e($homeContent['deck_std_title']) ?></h4>
                    </div>
                    <div class="deck-standards-list">
                      <div class="standard-row">
                        <span class="standard-icon"><?= e($homeContent['deck_std1_icon']) ?></span>
                        <div>
                          <div class="standard-title"><?= e($homeContent['deck_std1_title']) ?></div>
                          <div class="standard-desc"><?= e($homeContent['deck_std1_desc']) ?></div>
                        </div>
                      </div>
                      <div class="standard-row">
                        <span class="standard-icon"><?= e($homeContent['deck_std2_icon']) ?></span>
                        <div>
                          <div class="standard-title"><?= e($homeContent['deck_std2_title']) ?></div>
                          <div class="standard-desc"><?= e($homeContent['deck_std2_desc']) ?></div>
                        </div>
                      </div>
                      <div class="standard-row">
                        <span class="standard-icon"><?= e($homeContent['deck_std3_icon']) ?></span>
                        <div>
                          <div class="standard-title"><?= e($homeContent['deck_std3_title']) ?></div>
                          <div class="standard-desc"><?= e($homeContent['deck_std3_desc']) ?></div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="bento-card bento-card-deck deck-status-card">
                    <div class="bento-card-heading">
                      <h4><?= e($homeContent['deck_collab_title']) ?></h4>
                    </div>
                    <div class="deck-qualifications-list">
                      <div class="qual-item">
                        <span class="qual-label"><?= e($homeContent['deck_collab_role_lbl']) ?></span>
                        <span class="qual-val"><?= e($homeContent['deck_collab_role_val']) ?></span>
                      </div>
                      <div class="qual-item">
                        <span class="qual-label"><?= e($homeContent['deck_collab_avail_lbl']) ?></span>
                        <span class="qual-val highlight"><?= e($homeContent['deck_collab_avail_val']) ?></span>
                      </div>
                      <div class="qual-item">
                        <span class="qual-label"><?= e($homeContent['deck_collab_mode_lbl']) ?></span>
                        <span class="qual-val"><?= e($homeContent['deck_collab_mode_val']) ?></span>
                      </div>
                    </div>
                    <div class="deck-status-action">
                      <a href="#contact" class="btn-bento-pill primary"><?= e($homeContent['deck_collab_btn_text']) ?></a>
                    </div>
                  </div>

                </div>

                <div class="bento-deck-panel" id="deck-panel-2" role="tabpanel" data-panel="2">
                  
                  <div class="bento-card bento-card-deck deck-skill-card">
                    <div>
                      <span class="deck-eyebrow">Fondasi aplikasi</span>
                      <h4 class="deck-skill-title">Backend &amp; Infrastruktur</h4>
                      <p class="deck-skill-lead">Menyusun logika aplikasi dan data agar aman, terukur, serta mudah dikembangkan.</p>
                    </div>
                    <div class="deck-practice-list">
                      <div class="deck-practice-item"><span>Data &amp; API</span><strong>PHP, Laravel, MySQL, REST API</strong></div>
                      <div class="deck-practice-item"><span>Deployment</span><strong>Docker, Linux, Redis</strong></div>
                    </div>
                    <div class="bento-chip-tags">
                      <span class="bento-chip">Laravel</span><span class="bento-chip">PHP 8.x</span><span class="bento-chip">MySQL</span><span class="bento-chip">Docker</span>
                    </div>
                  </div>

                  <div class="bento-card bento-card-deck deck-skill-card">
                    <div>
                      <span class="deck-eyebrow">Pengalaman pengguna</span>
                      <h4 class="deck-skill-title">Web Interface &amp; Mobile</h4>
                      <p class="deck-skill-lead">Membuat pengalaman digital yang responsif, jelas, dan nyaman digunakan di berbagai perangkat.</p>
                    </div>
                    <div class="deck-practice-list">
                      <div class="deck-practice-item"><span>Web interface</span><strong>Next.js, React, Tailwind</strong></div>
                      <div class="deck-practice-item"><span>Mobile &amp; prototipe</span><strong>Flutter, Dart, Figma</strong></div>
                    </div>
                    <div class="bento-chip-tags">
                      <span class="bento-chip">Next.js</span><span class="bento-chip">React</span><span class="bento-chip">Tailwind</span><span class="bento-chip">Flutter</span>
                    </div>
                  </div>

                  <div class="bento-card bento-card-deck deck-skill-card">
                    <div>
                      <span class="deck-eyebrow">Insight &amp; kualitas</span>
                      <h4 class="deck-skill-title">Data Mining &amp; Quality</h4>
                      <p class="deck-skill-lead">Menggunakan data untuk membaca pola sekaligus menjaga kualitas kode sebelum sistem digunakan.</p>
                    </div>
                    <div class="deck-practice-list">
                      <div class="deck-practice-item"><span>Analisis data</span><strong>Algoritma C4.5, RapidMiner</strong></div>
                      <div class="deck-practice-item"><span>Quality check</span><strong>Pest, Larastan, pengujian alur</strong></div>
                    </div>
                    <div class="bento-chip-tags">
                      <span class="bento-chip">Algoritma C4.5</span><span class="bento-chip">RapidMiner</span><span class="bento-chip">Pest</span><span class="bento-chip">Larastan</span>
                    </div>
                  </div>

                </div>

                <!-- ==============================================
                     PANEL 3: PROYEK (Karya & Aplikasi Bisnis)
                     ============================================== -->
                <div class="bento-deck-panel" id="deck-panel-3" role="tabpanel" data-panel="3">
                  
                  <?php 
                    $p1 = $projects[0] ?? null;
                    $p2 = $projects[1] ?? null;
                    $p3 = $projects[2] ?? null;
                    $p1Img = ($p1 && !empty($p1['image']) && file_exists(UPLOAD_DIR_PROJECTS . '/' . basename($p1['image']))) ? upload_url('projects', $p1['image']) : '';
                    $p2Img = ($p2 && !empty($p2['image']) && file_exists(UPLOAD_DIR_PROJECTS . '/' . basename($p2['image']))) ? upload_url('projects', $p2['image']) : '';
                    $p3Img = ($p3 && !empty($p3['image']) && file_exists(UPLOAD_DIR_PROJECTS . '/' . basename($p3['image']))) ? upload_url('projects', $p3['image']) : '';
                  ?>

                  <!-- Project 1 -->
                  <div class="bento-card bento-card-deck deck-project-card">
                    <div class="bento-card-heading">
                      <h4><?= e($p1['title'] ?? 'Villa Zein Booking') ?></h4>
                      <span class="project-tag-pill"><?= e($p1['category'] ?? 'Web App') ?></span>
                    </div>
                    <div class="deck-project-preview">
                      <?php if ($p1Img): ?><img src="<?= e($p1Img) ?>" alt="Preview <?= e($p1['title']) ?>" loading="lazy" /><?php else: ?><span>VZ</span><?php endif; ?>
                    </div>
                    <p class="deck-project-summary">
                      <?= e($p1['summary'] ?? 'Platform reservasi villa dengan manajemen ketersediaan kamar dan invoice.') ?>
                    </p>
                    <div class="deck-project-meta"><span>Kontribusi</span><strong><?= e($p1['my_role'] ?? 'Full-stack development') ?></strong></div>
                    <div class="deck-project-footer">
                      <?php if ($p1): ?>
                        <button type="button" class="btn-bento-pill primary btn-detail-trigger" data-id="<?= (int)$p1['id'] ?>">Lihat Detail ➔</button>
                      <?php endif; ?>
                    </div>
                  </div>

                  <!-- Project 2 -->
                  <div class="bento-card bento-card-deck deck-project-card">
                    <div class="bento-card-heading">
                      <h4><?= e($p2['title'] ?? 'Sistem LMS Akademik') ?></h4>
                      <span class="project-tag-pill"><?= e($p2['category'] ?? 'Sistem Informasi') ?></span>
                    </div>
                    <div class="deck-project-preview">
                      <?php if ($p2Img): ?><img src="<?= e($p2Img) ?>" alt="Preview <?= e($p2['title']) ?>" loading="lazy" /><?php else: ?><span>LMS</span><?php endif; ?>
                    </div>
                    <p class="deck-project-summary">
                      <?= e($p2['summary'] ?? 'Portal akademik terpadu untuk monitoring nilai, absensi, dan jadwal belajar.') ?>
                    </p>
                    <div class="deck-project-meta"><span>Kontribusi</span><strong><?= e($p2['my_role'] ?? 'Web application development') ?></strong></div>
                    <div class="deck-project-footer">
                      <?php if ($p2): ?>
                        <button type="button" class="btn-bento-pill primary btn-detail-trigger" data-id="<?= (int)$p2['id'] ?>">Lihat Detail ➔</button>
                      <?php endif; ?>
                    </div>
                  </div>

                  <!-- Project 3 -->
                  <div class="bento-card bento-card-deck deck-project-card">
                    <div class="bento-card-heading">
                      <h4><?= e($p3['title'] ?? 'Big Cargo & C4.5') ?></h4>
                      <span class="project-tag-pill"><?= e($p3['category'] ?? 'Data Mining') ?></span>
                    </div>
                    <div class="deck-project-preview">
                      <?php if ($p3Img): ?><img src="<?= e($p3Img) ?>" alt="Preview <?= e($p3['title']) ?>" loading="lazy" /><?php else: ?><span>BC</span><?php endif; ?>
                    </div>
                    <p class="deck-project-summary">
                      <?= e($p3['summary'] ?? 'Sistem klasifikasi ketepatan logistik kargo berbasis pohon keputusan Algoritma C4.5.') ?>
                    </p>
                    <div class="deck-project-meta"><span>Kontribusi</span><strong><?= e($p3['my_role'] ?? 'Web application development') ?></strong></div>
                    <div class="deck-project-footer">
                      <?php if ($p3): ?>
                        <button type="button" class="btn-bento-pill primary btn-detail-trigger" data-id="<?= (int)$p3['id'] ?>">Lihat Detail</button>
                      <?php else: ?>
                        <a href="#projects" class="btn-bento-pill primary">Semua Proyek</a>
                      <?php endif; ?>
                    </div>
                  </div>

                </div>

                <!-- ==============================================
                     PANEL 4: GEAR & WORKSPACE SETUP (Hardware & Software)
                     ============================================== -->
                <div class="bento-deck-panel bento-panel-gear" id="deck-panel-4" role="tabpanel" data-panel="4">
                  
                  <!-- Card 1: Hardware & Workstation Rig -->
                  <div class="bento-card bento-card-deck deck-gear-card deck-gear-hardware-card">
                    <div class="bento-card-heading">
                      <div>
                        <span class="deck-eyebrow">Workstation &amp; Rig</span>
                        <h4>Hardware Utama</h4>
                      </div>
                      <span class="gear-status-pill"><span class="gear-status-dot"></span> Aktif</span>
                    </div>
                    <p class="deck-gear-lead">
                      Perangkat keras performa tinggi untuk kompilasi kode cepat, multitasking intensif, dan komputasi AI lokal.
                    </p>
                    <div class="gear-spec-list">
                      <div class="gear-spec-item">
                        <div class="gear-spec-icon">💻</div>
                        <div class="gear-spec-content">
                          <span class="gear-spec-label">Mesin Utama</span>
                          <strong class="gear-spec-name"><?= e($homeContent['gear_hw_laptop_name']) ?></strong>
                          <span class="gear-spec-detail"><?= e($homeContent['gear_hw_laptop_desc']) ?></span>
                        </div>
                      </div>
                      <div class="gear-spec-item">
                        <div class="gear-spec-icon">🖥️</div>
                        <div class="gear-spec-content">
                          <span class="gear-spec-label">Dual Display Setup</span>
                          <strong class="gear-spec-name"><?= e($homeContent['gear_hw_display_name']) ?></strong>
                          <span class="gear-spec-detail"><?= e($homeContent['gear_hw_display_desc']) ?></span>
                        </div>
                      </div>
                      <div class="gear-spec-item">
                        <div class="gear-spec-icon">⌨️</div>
                        <div class="gear-spec-content">
                          <span class="gear-spec-label">Input &amp; Periferal</span>
                          <strong class="gear-spec-name"><?= e($homeContent['gear_hw_keyboard_name']) ?></strong>
                          <span class="gear-spec-detail"><?= e($homeContent['gear_hw_keyboard_desc']) ?></span>
                        </div>
                      </div>
                      <div class="gear-spec-item">
                        <div class="gear-spec-icon">🎧</div>
                        <div class="gear-spec-content">
                          <span class="gear-spec-label">Audio Monitor</span>
                          <strong class="gear-spec-name"><?= e($homeContent['gear_hw_audio_name']) ?></strong>
                          <span class="gear-spec-detail"><?= e($homeContent['gear_hw_audio_desc']) ?></span>
                        </div>
                      </div>
                    </div>
                    <div class="deck-card-actions" style="margin-top: auto;">
                      <span class="bento-status-badge-inline">⚡ Dedicated Development Rig</span>
                    </div>
                  </div>

                  <!-- Card 2: Development & Software Stack -->
                  <div class="bento-card bento-card-deck deck-gear-card deck-gear-software-card">
                    <div class="bento-card-heading">
                      <div>
                        <span class="deck-eyebrow">Development Environment</span>
                        <h4>Software &amp; Toolchain</h4>
                      </div>
                      <span class="gear-cat-badge">Daily Driver</span>
                    </div>
                    <p class="deck-gear-lead">
                      Toolchain harian yang dikonfigurasi untuk kecepatan navigasi kode, otomasi CLI, dan arsitektur stabil.
                    </p>
                    <div class="gear-category-blocks">
                      <div class="gear-cat-block">
                        <span class="gear-block-title">OS &amp; Shell</span>
                        <div class="gear-tag-cluster">
                          <?php 
                          $osList = array_filter(array_map('trim', explode(',', $homeContent['gear_sw_os_tags'] ?? '')));
                          $oi = 0;
                          foreach ($osList as $tag): 
                            $isHi = ($oi < 2);
                            $oi++;
                            $tagIcon = get_tech_svg_icon($tag, 11);
                          ?>
                            <span class="gear-tool-tag <?= $isHi ? 'highlighted' : '' ?>"><?= $tagIcon ?> <span><?= e($tag) ?></span></span>
                          <?php endforeach; ?>
                        </div>
                      </div>
                      <div class="gear-cat-block">
                        <span class="gear-block-title">IDE &amp; Editors</span>
                        <div class="gear-tag-cluster">
                          <?php 
                          $ideList = array_filter(array_map('trim', explode(',', $homeContent['gear_sw_ide_tags'] ?? '')));
                          $ii = 0;
                          foreach ($ideList as $tag): 
                            $isHi = ($ii < 2);
                            $ii++;
                            $tagIcon = get_tech_svg_icon($tag, 11);
                          ?>
                            <span class="gear-tool-tag <?= $isHi ? 'highlighted' : '' ?>"><?= $tagIcon ?> <span><?= e($tag) ?></span></span>
                          <?php endforeach; ?>
                        </div>
                      </div>
                      <div class="gear-cat-block">
                        <span class="gear-block-title">Runtime, DB &amp; API</span>
                        <div class="gear-tag-cluster">
                          <?php 
                          $toolsList = array_filter(array_map('trim', explode(',', $homeContent['gear_sw_tools_tags'] ?? '')));
                          $ti = 0;
                          foreach ($toolsList as $tag): 
                            $isHi = ($ti === 1);
                            $ti++;
                            $tagIcon = get_tech_svg_icon($tag, 11);
                          ?>
                            <span class="gear-tool-tag <?= $isHi ? 'highlighted' : '' ?>"><?= $tagIcon ?> <span><?= e($tag) ?></span></span>
                          <?php endforeach; ?>
                        </div>
                      </div>
                    </div>
                    <div class="deck-card-actions" style="margin-top: auto;">
                      <span class="bento-status-badge-inline">🛠️ Tuned CLI &amp; Hotkeys</span>
                    </div>
                  </div>

                  <!-- Card 3: AI & Productivity Suite -->
                  <div class="bento-card bento-card-deck deck-gear-card deck-gear-ai-card">
                    <div class="bento-card-heading">
                      <div>
                        <span class="deck-eyebrow">Productivity &amp; Intelligence</span>
                        <h4>AI &amp; Alur Kerja</h4>
                      </div>
                      <span class="gear-ai-badge">Agentic AI</span>
                    </div>
                    <p class="deck-gear-lead">
                      Sinergi rekayasa modern: penalaran LLM tingkat lanjut digabungkan dengan desain terstruktur dan otomasi.
                    </p>
                    <div class="gear-ai-feature-list">
                      <div class="gear-ai-item">
                        <div class="gear-ai-icon">🤖</div>
                        <div>
                          <strong>Frontier AI Models</strong>
                          <span><?= e($homeContent['gear_ai_models_desc']) ?></span>
                        </div>
                      </div>
                      <div class="gear-ai-item">
                        <div class="gear-ai-icon">🎯</div>
                        <div>
                          <strong>Prompt &amp; Agent Engineering</strong>
                          <span><?= e($homeContent['gear_ai_prompt_desc']) ?></span>
                        </div>
                      </div>
                      <div class="gear-ai-item">
                        <div class="gear-ai-icon">🎨</div>
                        <div>
                          <strong>Desain &amp; Manajemen Ide</strong>
                          <span><?= e($homeContent['gear_ai_design_desc']) ?></span>
                        </div>
                      </div>
                      <div class="gear-ai-item">
                        <div class="gear-ai-icon">🚀</div>
                        <div>
                          <strong>DevOps &amp; Hosting</strong>
                          <span><?= e($homeContent['gear_ai_devops_desc']) ?></span>
                        </div>
                      </div>
                    </div>
                    <div class="deck-card-actions" style="margin-top: auto;">
                      <a href="#skills" class="btn-bento-pill primary">Lihat Tech Stack ➔</a>
                      <span class="bento-status-badge-inline">● 100% AI-Augmented</span>
                    </div>
                  </div>

                </div>

                <!-- ==============================================
                     PANEL 5: KONTAK (Kanal Hubungi Langsung)
                     ============================================== -->
                <div class="bento-deck-panel bento-panel-contact" id="deck-panel-5" role="tabpanel" data-panel="5">
                  
                  <!-- Email -->
                  <div class="bento-card bento-card-deck deck-contact-card deck-contact-email-card">
                    <div class="deck-contact-heading">
                      <span class="deck-contact-icon">✉</span>
                      <div>
                        <span class="deck-eyebrow">Jalur formal</span>
                        <h4>Email Profesional</h4>
                      </div>
                    </div>
                    <p class="deck-contact-lead">
                      Untuk kolaborasi, rekrutmen, atau konsultasi pengembangan sistem.
                    </p>
                    <div class="deck-contact-action-area">
                      <a href="mailto:<?= e($profile['email']) ?>" class="deck-contact-action-btn">
                        <span>Kirim Email</span><span aria-hidden="true">↗</span>
                      </a>
                    </div>
                  </div>

                  <!-- Jejaring Profesional -->
                  <div class="bento-card bento-card-deck deck-contact-card deck-contact-links-card">
                    <div>
                      <span class="deck-eyebrow">Terhubung online</span>
                      <h4>Tautan Sosial &amp; Kode</h4>
                    </div>
                    <p class="deck-contact-links-lead">Lihat karya, profil profesional, atau tinggalkan pesan.</p>
                    <div class="deck-social-links-list">
                      <?php if (!empty($profile['github'])): ?>
                        <a href="<?= e($profile['github']) ?>" target="_blank" rel="noopener noreferrer" class="deck-social-row">
                          <span>GitHub</span><small>Kode &amp; proyek</small>
                        </a>
                      <?php endif; ?>
                      <?php if (!empty($profile['linkedin'])): ?>
                        <a href="<?= e($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer" class="deck-social-row">
                          <span>LinkedIn</span><small>Profil profesional</small>
                        </a>
                      <?php endif; ?>
                      <?php if (!empty($profile['instagram'])): ?>
                        <a href="<?= e($profile['instagram']) ?>" target="_blank" rel="noopener noreferrer" class="deck-social-row">
                          <span>Instagram</span><small>Update &amp; aktivitas</small>
                        </a>
                      <?php endif; ?>
                      <?php if (!empty($profile['tiktok'])): ?>
                        <a href="<?= e($profile['tiktok']) ?>" target="_blank" rel="noopener noreferrer" class="deck-social-row">
                          <span>TikTok</span><small>Konten &amp; kreasi</small>
                        </a>
                      <?php endif; ?>
                      <a href="<?= BASE_URL ?>/guestbook" class="deck-social-row">
                        <span>Buku Tamu</span><small>Tinggalkan pesan</small>
                      </a>
                    </div>
                  </div>

                  <!-- Kolaborasi & Ketersediaan -->
                  <div class="bento-card bento-card-deck deck-contact-card deck-contact-avail-card">
                    <div>
                      <span class="deck-eyebrow">Status Kerja</span>
                      <h4>Ketersediaan &amp; Zona</h4>
                    </div>
                    <p class="deck-contact-lead">Terbuka untuk proyek lepas (freelance), konsultasi arsitektur, maupun peluang full-time.</p>
                    <div class="deck-social-links-list">
                      <div class="deck-social-row">
                        <span>Zona Waktu</span><small>WIB (UTC+7) • Jakarta</small>
                      </div>
                      <div class="deck-social-row">
                        <span>Ketersediaan</span><small style="color: #10B981; font-weight: 700;">● Open for Opportunities</small>
                      </div>
                      <div class="deck-social-row">
                        <span>Waktu Respons</span><small>&lt; 24 Jam Kerja</small>
                      </div>
                    </div>
                    <div class="deck-card-actions" style="margin-top: auto; padding-top: 0.5rem;">
                      <a href="#contact" class="btn-bento-pill primary" style="width: 100%; text-align: center;">Tinggalkan Pesan ➔</a>
                    </div>
                  </div>

                </div>

              </div>
            </div>

          </div>

          <!-- BOTTOM STATUS STRIP -->
          <div class="bento-status-strip">
            <div class="status-indicator">
              <span class="status-dot-pulse"></span>
              <div>
                <div class="status-title">Active</div>
                <div class="status-caption">Online</div>
              </div>
            </div>

            <div class="status-stat-box">
              <div class="stat-caption">Years Active</div>
              <div class="stat-val">2026</div>
              <div class="stat-sub">- Present</div>
            </div>

            <div class="status-search-box" id="bentoSearchTrigger" tabindex="0" role="button" title="Tekan untuk mencari fitur atau proyek">
              <span class="search-placeholder">find your favorite</span>
              <div class="search-circle-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                  <circle cx="11" cy="11" r="8"></circle>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
              </div>
            </div>

            <a href="mailto:<?= e($profile['email'] ?? 'contact@ammarsyarif.com') ?>" class="bento-mail-float-btn" title="Kirim Email Langsung">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
              </svg>
            </a>
          </div>

        </div>
      </div>
    </section>

    <hr class="section-divider" />

    <!-- ====================================================================
         SECTION 2: ABOUT (Latar Belakang & Filosofi)
         ==================================================================== -->
    <section id="about" class="section-padding">
      <div class="site-container">
        <div class="section-header">
          <div class="section-caption"><?= e($homeContent['about_caption']) ?></div>
          <h2 class="section-title"><?= e($homeContent['about_title']) ?></h2>
        </div>

        <div class="about-grid">
          <div class="about-text-panel glass-panel">
            <div class="about-panel-intro">
              <span class="about-kicker"><?= e($homeContent['about_kicker']) ?></span>
              <h3><?= e($homeContent['about_heading']) ?></h3>
            </div>
            <p>
              <?= e($homeContent['about_paragraph_one']) ?>
            </p>
            <p>
              <?= e($homeContent['about_paragraph_two']) ?>
            </p>
            <p>
              <?= e($homeContent['about_paragraph_three']) ?>
            </p>
          </div>

          <div class="about-stat-panel glass-panel">
            <div>
              <span class="about-kicker">Fokus pengembangan</span>
              <h3 class="about-side-title">Teknis, terstruktur, dan siap berkembang.</h3>
              <div class="about-focus-list">
                <div class="about-focus-item"><span>01</span><div><strong>Full-stack product</strong><small>PHP, Laravel, MySQL, React, dan Next.js.</small></div></div>
                <div class="about-focus-item"><span>02</span><div><strong>System architecture</strong><small>Database, REST API, performa, keamanan, dan skalabilitas.</small></div></div>
                <div class="about-focus-item"><span>03</span><div><strong>Server infrastructure</strong><small>Git, Linux, VPS, Nginx, Docker, Redis, dan queue.</small></div></div>
              </div>
            </div>

            <div class="about-metrics-row">
              <div>
                <div class="metric-number">End-to-End</div>
                <div class="metric-label">Database hingga deployment</div>
              </div>
              <div>
                <div class="metric-number">Remote-ready</div>
                <div class="metric-label">Kolaborasi proyek digital</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <hr class="section-divider" />

    <!-- ====================================================================
         SECTION 3: SKILLS (Flat Badges & Matriks Tingkat Penguasaan)
         ==================================================================== -->
    <section id="skills" class="section-padding">
      <div class="site-container">
        <div class="section-header">
          <div class="section-caption"><?= e($homeContent['skills_caption']) ?></div>
          <h2 class="section-title"><?= e($homeContent['skills_title']) ?></h2>
          <p class="section-lead" style="color: var(--color-text-dim); max-width: 680px; margin-top: 0.5rem; font-size: var(--text-sm); line-height: 1.6;">
            <?= e($homeContent['skills_lead']) ?>
          </p>
        </div>

        <!-- ================================================================
             NEW: MATRIKS TINGKAT PENGUASAAN SKILL (Skill Hierarchy Matrix)
             ================================================================ -->
        <div class="skill-hierarchy-section">
          <div class="hierarchy-header">
            <span class="hierarchy-kicker"><?= e($homeContent['matrix_kicker']) ?></span>
            <h3 class="hierarchy-title"><?= e($homeContent['matrix_title']) ?></h3>
            <p class="hierarchy-desc"><?= e($homeContent['matrix_desc']) ?></p>
          </div>

          <div class="skill-hierarchy-grid">
            <!-- Tier 1: Advanced / Production-Ready -->
            <div class="skill-tier-card tier-advanced">
              <div class="tier-top">
                <div class="tier-badge-pill">
                  <span class="tier-dot-pulse green"></span>
                  <span><?= e($homeContent['tier1_badge']) ?></span>
                </div>
                <span class="tier-level-caption"><?= e($homeContent['tier1_level']) ?></span>
              </div>
              <h4 class="tier-title"><?= e($homeContent['tier1_title']) ?></h4>
              <p class="tier-desc"><?= e($homeContent['tier1_desc']) ?></p>
              
              <div class="tier-progress-track">
                <div class="tier-progress-fill advanced" style="width: <?= (int)$homeContent['tier1_percent'] ?>%;"></div>
              </div>

              <div class="tier-chips-wrap">
                <?= render_tier_chips_from_csv($homeContent['tier1_chips'], true) ?>
              </div>
            </div>

            <!-- Tier 2: Proficient -->
            <div class="skill-tier-card tier-proficient">
              <div class="tier-top">
                <div class="tier-badge-pill blue">
                  <span class="tier-dot-pulse blue"></span>
                  <span><?= e($homeContent['tier2_badge']) ?></span>
                </div>
                <span class="tier-level-caption"><?= e($homeContent['tier2_level']) ?></span>
              </div>
              <h4 class="tier-title"><?= e($homeContent['tier2_title']) ?></h4>
              <p class="tier-desc"><?= e($homeContent['tier2_desc']) ?></p>

              <div class="tier-progress-track">
                <div class="tier-progress-fill proficient" style="width: <?= (int)$homeContent['tier2_percent'] ?>%;"></div>
              </div>

              <div class="tier-chips-wrap">
                <?= render_tier_chips_from_csv($homeContent['tier2_chips'], false) ?>
              </div>
            </div>

            <!-- Tier 3: Familiar / Exploring -->
            <div class="skill-tier-card tier-familiar">
              <div class="tier-top">
                <div class="tier-badge-pill purple">
                  <span class="tier-dot-pulse purple"></span>
                  <span><?= e($homeContent['tier3_badge']) ?></span>
                </div>
                <span class="tier-level-caption"><?= e($homeContent['tier3_level']) ?></span>
              </div>
              <h4 class="tier-title"><?= e($homeContent['tier3_title']) ?></h4>
              <p class="tier-desc"><?= e($homeContent['tier3_desc']) ?></p>

              <div class="tier-progress-track">
                <div class="tier-progress-fill familiar" style="width: <?= (int)$homeContent['tier3_percent'] ?>%;"></div>
              </div>

              <div class="tier-chips-wrap">
                <?= render_tier_chips_from_csv($homeContent['tier3_chips'], false) ?>
              </div>
            </div>
          </div>
        </div>

        <p class="skills-mobile-swipe-hint" aria-hidden="true" style="margin-top: 2.5rem;">Geser kartu untuk melihat domain lainnya</p>
        <div class="skills-category-grid">
          <!-- 1. Frontend Engineering -->
          <div class="skills-domain-card domain-frontend">
            <div class="skills-card-top">
              <div class="skills-icon-badge">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                  <line x1="8" y1="21" x2="16" y2="21"></line>
                  <line x1="12" y1="17" x2="12" y2="21"></line>
                  <polyline points="7 8 10 10 7 12"></polyline>
                  <line x1="13" y1="12" x2="17" y2="12"></line>
                </svg>
              </div>
              <span class="skills-badge-tag"><?= e($homeContent['domain1_badge']) ?></span>
            </div>

            <h3 class="skills-domain-title"><?= e($homeContent['domain1_title']) ?></h3>
            <p class="skills-domain-desc"><?= e($homeContent['domain1_desc']) ?></p>

            <div class="skills-subgroups">
              <div class="skills-subgroup">
                <span class="skills-subgroup-label"><?= e($homeContent['domain1_grp1_title']) ?></span>
                <div class="skills-chips-wrapper">
                  <?= render_skill_badges_from_csv($homeContent['domain1_grp1_tags']) ?>
                </div>
              </div>

              <div class="skills-subgroup">
                <span class="skills-subgroup-label"><?= e($homeContent['domain1_grp2_title']) ?></span>
                <div class="skills-chips-wrapper">
                  <?= render_skill_badges_from_csv($homeContent['domain1_grp2_tags']) ?>
                </div>
              </div>
            </div>

            <div class="skills-highlights">
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain1_b1']) ?></span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain1_b2']) ?></span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain1_b3']) ?></span>
              </div>
            </div>

            <div class="skills-card-footer">
              <span class="footer-indicator-dot"></span>
              <span class="footer-meta-text"><?= e($homeContent['domain1_eco']) ?></span>
            </div>
          </div>

          <!-- 2. Backend & Server Architecture -->
          <div class="skills-domain-card domain-backend">
            <div class="skills-card-top">
              <div class="skills-icon-badge">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="2" y="2" width="20" height="8" rx="2"></rect>
                  <rect x="2" y="14" width="20" height="8" rx="2"></rect>
                  <line x1="6" y1="6" x2="6.01" y2="6"></line>
                  <line x1="6" y1="18" x2="6.01" y2="18"></line>
                </svg>
              </div>
              <span class="skills-badge-tag"><?= e($homeContent['domain2_badge']) ?></span>
            </div>

            <h3 class="skills-domain-title"><?= e($homeContent['domain2_title']) ?></h3>
            <p class="skills-domain-desc"><?= e($homeContent['domain2_desc']) ?></p>

            <div class="skills-subgroups">
              <div class="skills-subgroup">
                <span class="skills-subgroup-label"><?= e($homeContent['domain2_grp1_title']) ?></span>
                <div class="skills-chips-wrapper">
                  <?= render_skill_badges_from_csv($homeContent['domain2_grp1_tags']) ?>
                </div>
              </div>

              <div class="skills-subgroup">
                <span class="skills-subgroup-label"><?= e($homeContent['domain2_grp2_title']) ?></span>
                <div class="skills-chips-wrapper">
                  <?= render_skill_badges_from_csv($homeContent['domain2_grp2_tags']) ?>
                </div>
              </div>
            </div>

            <div class="skills-highlights">
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain2_b1']) ?></span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain2_b2']) ?></span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain2_b3']) ?></span>
              </div>
            </div>

            <div class="skills-card-footer">
              <span class="footer-indicator-dot"></span>
              <span class="footer-meta-text"><?= e($homeContent['domain2_eco']) ?></span>
            </div>
          </div>

          <!-- 3. Database & Storage Engine -->
          <div class="skills-domain-card domain-database">
            <div class="skills-card-top">
              <div class="skills-icon-badge">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                  <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                  <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                </svg>
              </div>
              <span class="skills-badge-tag"><?= e($homeContent['domain3_badge']) ?></span>
            </div>

            <h3 class="skills-domain-title"><?= e($homeContent['domain3_title']) ?></h3>
            <p class="skills-domain-desc"><?= e($homeContent['domain3_desc']) ?></p>

            <div class="skills-subgroups">
              <div class="skills-subgroup">
                <span class="skills-subgroup-label"><?= e($homeContent['domain3_grp1_title']) ?></span>
                <div class="skills-chips-wrapper">
                  <?= render_skill_badges_from_csv($homeContent['domain3_grp1_tags']) ?>
                </div>
              </div>

              <div class="skills-subgroup">
                <span class="skills-subgroup-label"><?= e($homeContent['domain3_grp2_title']) ?></span>
                <div class="skills-chips-wrapper">
                  <?= render_skill_badges_from_csv($homeContent['domain3_grp2_tags']) ?>
                </div>
              </div>
            </div>

            <div class="skills-highlights">
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain3_b1']) ?></span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain3_b2']) ?></span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain3_b3']) ?></span>
              </div>
            </div>

            <div class="skills-card-footer">
              <span class="footer-indicator-dot"></span>
              <span class="footer-meta-text"><?= e($homeContent['domain3_eco']) ?></span>
            </div>
          </div>

          <!-- 4. DevOps, Cloud & Infrastructure -->
          <div class="skills-domain-card domain-devops">
            <div class="skills-card-top">
              <div class="skills-icon-badge">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="14" width="18" height="7" rx="2"></rect>
                  <path d="M7 14v-3h3v3"></path>
                  <path d="M11 14v-3h3v3"></path>
                  <path d="M15 14v-3h3v3"></path>
                </svg>
              </div>
              <span class="skills-badge-tag"><?= e($homeContent['domain4_badge']) ?></span>
            </div>

            <h3 class="skills-domain-title"><?= e($homeContent['domain4_title']) ?></h3>
            <p class="skills-domain-desc"><?= e($homeContent['domain4_desc']) ?></p>

            <div class="skills-subgroups">
              <div class="skills-subgroup">
                <span class="skills-subgroup-label"><?= e($homeContent['domain4_grp1_title']) ?></span>
                <div class="skills-chips-wrapper">
                  <?= render_skill_badges_from_csv($homeContent['domain4_grp1_tags']) ?>
                </div>
              </div>

              <div class="skills-subgroup">
                <span class="skills-subgroup-label"><?= e($homeContent['domain4_grp2_title']) ?></span>
                <div class="skills-chips-wrapper">
                  <?= render_skill_badges_from_csv($homeContent['domain4_grp2_tags']) ?>
                </div>
              </div>
            </div>

            <div class="skills-highlights">
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain4_b1']) ?></span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain4_b2']) ?></span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain4_b3']) ?></span>
              </div>
            </div>

            <div class="skills-card-footer">
              <span class="footer-indicator-dot"></span>
              <span class="footer-meta-text"><?= e($homeContent['domain4_eco']) ?></span>
            </div>
          </div>

          <!-- 5. Testing, QA & Mobile Development -->
          <div class="skills-domain-card domain-quality">
            <div class="skills-card-top">
              <div class="skills-icon-badge">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
              </div>
              <span class="skills-badge-tag"><?= e($homeContent['domain5_badge']) ?></span>
            </div>

            <h3 class="skills-domain-title"><?= e($homeContent['domain5_title']) ?></h3>
            <p class="skills-domain-desc"><?= e($homeContent['domain5_desc']) ?></p>

            <div class="skills-subgroups">
              <div class="skills-subgroup">
                <span class="skills-subgroup-label"><?= e($homeContent['domain5_grp1_title']) ?></span>
                <div class="skills-chips-wrapper">
                  <?= render_skill_badges_from_csv($homeContent['domain5_grp1_tags']) ?>
                </div>
              </div>

              <div class="skills-subgroup">
                <span class="skills-subgroup-label"><?= e($homeContent['domain5_grp2_title']) ?></span>
                <div class="skills-chips-wrapper">
                  <?= render_skill_badges_from_csv($homeContent['domain5_grp2_tags']) ?>
                </div>
              </div>
            </div>

            <div class="skills-highlights">
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain5_b1']) ?></span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain5_b2']) ?></span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain5_b3']) ?></span>
              </div>
            </div>

            <div class="skills-card-footer">
              <span class="footer-indicator-dot"></span>
              <span class="footer-meta-text"><?= e($homeContent['domain5_eco']) ?></span>
            </div>
          </div>

          <!-- 6. Data Mining & Analitik Sistem Informasi -->
          <div class="skills-domain-card domain-data">
            <div class="skills-card-top">
              <div class="skills-icon-badge">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="5" r="3"></circle>
                  <circle cx="5" cy="19" r="3"></circle>
                  <circle cx="19" cy="19" r="3"></circle>
                  <path d="M12 8v4m0 0l-5 4m5-4l5 4"></path>
                </svg>
              </div>
              <span class="skills-badge-tag"><?= e($homeContent['domain6_badge']) ?></span>
            </div>

            <h3 class="skills-domain-title"><?= e($homeContent['domain6_title']) ?></h3>
            <p class="skills-domain-desc"><?= e($homeContent['domain6_desc']) ?></p>

            <div class="skills-subgroups">
              <div class="skills-subgroup">
                <span class="skills-subgroup-label"><?= e($homeContent['domain6_grp1_title']) ?></span>
                <div class="skills-chips-wrapper">
                  <?= render_skill_badges_from_csv($homeContent['domain6_grp1_tags']) ?>
                </div>
              </div>

              <div class="skills-subgroup">
                <span class="skills-subgroup-label"><?= e($homeContent['domain6_grp2_title']) ?></span>
                <div class="skills-chips-wrapper">
                  <?= render_skill_badges_from_csv($homeContent['domain6_grp2_tags']) ?>
                </div>
              </div>
            </div>

            <div class="skills-highlights">
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain6_b1']) ?></span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain6_b2']) ?></span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span><?= e($homeContent['domain6_b3']) ?></span>
              </div>
            </div>

            <div class="skills-card-footer">
              <span class="footer-indicator-dot"></span>
              <span class="footer-meta-text"><?= e($homeContent['domain6_eco']) ?></span>
            </div>
          </div></div>
      </div>
    </section>

    <hr class="section-divider" />

    <!-- ====================================================================
         SECTION 4: PROJECTS (Interactive Filter + Video Demo + Impact Metrics)
         ==================================================================== -->
    <section id="projects" class="section-padding">
      <div class="site-container">
        <div class="section-header">
          <div class="section-caption"><?= e($homeContent['projects_caption']) ?></div>
          <h2 class="section-title"><?= e($homeContent['projects_title']) ?></h2>
          <p class="section-lead" style="color: var(--color-text-dim); max-width: 680px; margin-top: 0.5rem; font-size: var(--text-sm); line-height: 1.6;">
            Koleksi aplikasi web, platform bisnis, dan riset data mining yang dibangun dengan fokus pada performa, keamanan, dan kejelasan arsitektur.
          </p>
        </div>

        <?php
          // Hitung kategori unik untuk tombol filter dinamis
          $categories = [];
          foreach ($projects as $p) {
              $catName = trim($p['category'] ?: 'Web Application');
              $catKey = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $catName));
              if (!isset($categories[$catKey])) {
                  $categories[$catKey] = ['name' => $catName, 'count' => 0];
              }
              $categories[$catKey]['count']++;
          }
        ?>

        <!-- Filter Kategori Proyek Interaktif -->
        <div class="projects-filter-wrapper">
          <div class="projects-filter-bar" id="projectsFilterBar" role="tablist" aria-label="Filter Kategori Proyek">
            <button type="button" class="proj-filter-btn active" data-filter="all" role="tab" aria-selected="true">
              <span>Semua Proyek</span>
              <span class="filter-count-badge"><?= count($projects) ?></span>
            </button>
            <?php foreach ($categories as $k => $cData): ?>
              <button type="button" class="proj-filter-btn" data-filter="<?= e($k) ?>" role="tab" aria-selected="false">
                <span><?= e($cData['name']) ?></span>
                <span class="filter-count-badge"><?= (int)$cData['count'] ?></span>
              </button>
            <?php endforeach; ?>
          </div>
        </div>

        <p class="projects-mobile-swipe-hint" aria-hidden="true">Geser kartu untuk melihat proyek lainnya</p>
        
        <div class="projects-grid" id="projectsGrid">
          <?php if (empty($projects)): ?>
            <p style="color: var(--color-text-dim);">Belum ada proyek yang dipublikasikan.</p>
          <?php else: ?>
            <?php foreach ($projects as $proj): 
              $hasImg = !empty($proj['image']) && file_exists(UPLOAD_DIR_PROJECTS . '/' . basename($proj['image']));
              $techTags = array_filter(array_map('trim', explode(',', (string)$proj['tech_stack'])));
              $catName = trim($proj['category'] ?: 'Web Application');
              $catSlug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $catName));
            ?>
              <article class="glass-panel project-card reveal-card" data-id="<?= (int)$proj['id'] ?>" data-category="<?= e($catSlug) ?>" tabindex="0" role="button" aria-label="Buka rincian proyek <?= e($proj['title']) ?>">
                
                <div class="project-media-wrap">
                  <?php if ($hasImg): ?>
                    <img src="<?= upload_url('projects', $proj['image']) ?>" alt="<?= e($proj['title']) ?>" class="project-img" loading="lazy">
                  <?php else: ?>
                    <div class="project-placeholder-pattern">
                      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                      </svg>
                      <span><?= e($proj['title']) ?></span>
                    </div>
                  <?php endif; ?>
                  
                  <?php if (!empty($proj['demo_link']) && (str_contains($proj['demo_link'], 'youtu') || str_contains($proj['demo_link'], '.mp4'))): ?>
                    <span class="video-badge-corner" title="Tersedia Video Demo Embed">🎬 Video Demo</span>
                  <?php endif; ?>
                </div>

                <div class="project-card-content">
                  <div class="project-top-meta">
                    <span class="project-category-tag"><?= e($proj['category'] ?: 'Web Application') ?></span>
                    <span class="project-role-caption"><?= e($proj['my_role'] ?: 'Developer') ?></span>
                  </div>

                  <h3 class="project-card-title"><?= e($proj['title']) ?></h3>
                  <p class="project-card-summary"><?= e($proj['summary']) ?></p>

                  <div class="project-stack-tags">
                    <?php foreach (array_slice($techTags, 0, 4) as $tag): 
                        $tagIcon = get_tech_svg_icon($tag, 11);
                        $cleanTag = trim(preg_replace('/^[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{25A0}-\x{25FF}\x{2B50}\x{2300}-\x{23FF}\x{2190}-\x{21FF}\x{FE00}-\x{FE0F}\s▲🔴⚡🐬🔗🟡⚛️🌿🐧🎨🟢🐳🐍☁️⭐]+/u', '', $tag));
                        $displayTag = !empty($cleanTag) ? $cleanTag : $tag;
                    ?>
                      <span class="project-tag-micro"><?= $tagIcon ?> <span><?= e($displayTag) ?></span></span>
                    <?php endforeach; ?>
                  </div>

                  <div class="project-card-footer">
                    <button type="button" class="btn-open-detail btn-detail-trigger" data-id="<?= (int)$proj['id'] ?>">
                      Rincian &amp; Impact ➔
                    </button>
                    <?php if (!empty($proj['demo_link'])): ?>
                      <a href="<?= e($proj['demo_link']) ?>" target="_blank" rel="noopener noreferrer" class="btn-open-detail" style="color: var(--color-text-dim);">
                        Live Demo ↗
                      </a>
                    <?php endif; ?>
                  </div>
                </div>

              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Empty state fallback jika filter tidak menemukan hasil -->
        <div id="projectsFilterEmpty" class="projects-empty-alert" style="display: none;">
          <span style="font-size: 1.75rem;">🔍</span>
          <p>Tidak ada proyek dalam kategori yang dipilih.</p>
          <button type="button" class="btn-bento-pill primary" id="btnResetProjectFilter">Tampilkan Semua Proyek</button>
        </div>

      </div>
    </section>

    <hr class="section-divider" />

    <!-- ====================================================================
         SECTION 4.5: GITHUB LIVE REPO SHOWCASE (Open Source & Repositories)
         ==================================================================== -->
    <section id="github-showcase" class="section-padding github-showcase-section">
      <div class="site-container">
        <div class="section-header">
          <div class="section-caption">Open Source &amp; Code Quality</div>
          <h2 class="section-title">GitHub Repository Showcase</h2>
          <p class="section-lead" style="color: var(--color-text-dim); max-width: 680px; margin-top: 0.5rem; font-size: var(--text-sm); line-height: 1.6;">
            Bukti nyata struktur kode bersih, commit history yang konsisten, dan arsitektur siap pakai yang dapat diinspeksi langsung oleh Tech Recruiter &amp; Lead Engineer.
          </p>
        </div>

        <?php
          // Tarik data profil, statistik kontribusi, dan repositori publik secara real-time via GitHub API
          $ghUsername = !empty($profile['github']) ? trim(basename(parse_url($profile['github'], PHP_URL_PATH))) : 'ammarsyrf';
          if (empty($ghUsername) || stripos($ghUsername, 'zentokun') !== false) $ghUsername = 'ammarsyrf';
          $githubStats = get_github_user_stats($ghUsername);
          $githubRepos = get_github_public_repos($ghUsername, 6);
        ?>

        <script id="github-years-data" type="application/json">
          <?= json_encode($githubStats['years_data'] ?? [], JSON_HEX_TAG | JSON_HEX_AMP) ?>
        </script>

        <!-- ================================================================
             GITHUB ACTIVITY & CONTRIBUTION HEATMAP WIDGET
             ================================================================ -->
        <div class="github-activity-card" id="githubActivityCard">
          <div class="github-activity-header">
            <div class="github-activity-stat">
              <div class="github-contrib-badges-wrap">
                <div class="github-contrib-badge">
                  <span class="github-contrib-dot"></span>
                  <span>Live Activity</span>
                </div>
                <a href="?refresh_github=1#github-showcase" class="github-sync-btn" title="Tarik data terbaru dari GitHub API sekarang">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                  <span>Sync Now</span>
                </a>
              </div>
              <div class="github-contrib-counter">
                <span class="github-contrib-number" id="githubContribNumber"><?= e($githubStats['total_contributions'] ?? '1,162') ?></span>
                <span class="github-contrib-caption" id="githubContribCaption">contributions in the last year</span>
              </div>
            </div>
            <div class="github-year-pills" id="githubYearPills" role="tablist" aria-label="Tahun Aktivitas GitHub">
              <button type="button" class="year-pill active" data-year="last" title="1 Tahun Terakhir">Last Year</button>
              <button type="button" class="year-pill" data-year="2026" title="Aktivitas 2026">2026</button>
              <button type="button" class="year-pill" data-year="2025" title="Aktivitas 2025">2025</button>
              <button type="button" class="year-pill" data-year="2024" title="Aktivitas 2024">2024</button>
              <button type="button" class="year-pill" data-year="2023" title="Aktivitas 2023">2023</button>
              <button type="button" class="year-pill" data-year="2022" title="Aktivitas 2022">2022</button>
              <button type="button" class="year-pill" data-year="2021" title="Aktivitas 2021">2021</button>
              <button type="button" class="year-pill" data-year="2020" title="Aktivitas 2020">2020</button>
            </div>
          </div>

          <!-- Heatmap Canvas -->
          <div class="github-heatmap-wrapper">
            <div class="github-heatmap-grid">
              <div class="heatmap-months-row" id="heatmapMonthsRow">
                <span>Sep</span><span>Oct</span><span>Nov</span><span>Dec</span><span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span><span>Jul</span><span>Aug</span>
              </div>
              <div class="heatmap-days-container">
                <div class="heatmap-day-labels">
                  <span>Mon</span>
                  <span>Wed</span>
                  <span>Fri</span>
                </div>
                <div class="heatmap-squares-grid" id="heatmapSquaresGrid">
                  <?php
                    $initDays = !empty($githubStats['days']) ? $githubStats['days'] : ($githubStats['years_data']['2026']['days'] ?? []);
                    if (!empty($initDays)) {
                      foreach ($initDays as $dItem) {
                        $lvl = min(4, max(0, (int)$dItem['level']));
                        $cls = 'lvl-' . $lvl;
                        $countLabel = !empty($dItem['count']) ? $dItem['count'] : ($lvl > 0 ? ($lvl * 3) . " contributions" : "No contributions");
                        echo '<div class="heatmap-sq ' . $cls . '" data-date="' . e($dItem['date']) . '" data-level="' . $lvl . '" data-count="' . e($countLabel) . '" title="' . e($dItem['date'] . ': ' . $countLabel) . '"></div>';
                      }
                    } else {
                      for ($i = 0; $i < 365; $i++) {
                        echo '<div class="heatmap-sq lvl-0" title="No contributions"></div>';
                      }
                    }
                  ?>
                </div>
              </div>
              <div class="heatmap-footer-row">
                <span>Live synchronized with official GitHub API</span>
                <div class="heatmap-legend">
                  <span>Less</span>
                  <div class="heatmap-legend-squares">
                    <span class="heatmap-sq lvl-0"></span>
                    <span class="heatmap-sq lvl-1"></span>
                    <span class="heatmap-sq lvl-2"></span>
                    <span class="heatmap-sq lvl-3"></span>
                    <span class="heatmap-sq lvl-4"></span>
                  </div>
                  <span>More</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Organizations Strip -->
          <div class="github-orgs-strip">
            <div class="orgs-strip-label">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0 0 22 12.017C22 6.484 17.522 2 12 2z"/></svg>
              <span>Organizations:</span>
            </div>
            <div class="orgs-strip-chips">
              <a href="https://github.com/NexusDevWeb" target="_blank" rel="noopener noreferrer" class="github-org-chip nexus" title="Organisasi NexusDevWeb">
                <span class="org-avatar-icon nexus">⚡</span>
                <span class="org-name">@NexusDevWeb</span>
                <span class="org-role-badge">Core Contributor</span>
              </a>
              <a href="https://github.com/NexDigi-Dev" target="_blank" rel="noopener noreferrer" class="github-org-chip nexdigi" title="Organisasi NexDigi-Dev">
                <span class="org-avatar-icon nexdigi">⌘</span>
                <span class="org-name">@NexDigi-Dev</span>
                <span class="org-role-badge">Lead Dev</span>
              </a>
              <a href="https://github.com/zeneriedev" target="_blank" rel="noopener noreferrer" class="github-org-chip zenerie" title="Organisasi zeneriedev">
                <span class="org-avatar-icon zenerie">✦</span>
                <span class="org-name">@zeneriedev</span>
                <span class="org-role-badge">Owner</span>
              </a>
            </div>
          </div>

          <!-- Activity Overview & Code Distribution Panel -->
          <div class="github-activity-overview">
            <div class="activity-summary-col">
              <div class="activity-col-header">
                <svg class="activity-book-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                  <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <h4>Activity Overview &amp; Production Ecosystem</h4>
              </div>
              <p class="activity-bio-text">
                Kontributor aktif dalam ekosistem <strong>NexusDevWeb</strong>, <strong>NexDigi-Dev</strong>, dan proyek open-source mandiri dengan fokus pada <em>clean architecture</em>, sistem backend terstruktur, dan performa tinggi.
              </p>
              <div class="activity-quick-stats">
                <div class="quick-stat-item">
                  <span class="quick-stat-val"><?= e($githubStats['total_contributions'] ?? '1,162+') ?></span>
                  <span class="quick-stat-lbl">Verified Commits</span>
                </div>
                <div class="quick-stat-item">
                  <span class="quick-stat-val"><?= (int)($githubStats['public_repos'] ?? count($githubRepos)) ?></span>
                  <span class="quick-stat-lbl">Public Repos</span>
                </div>
                <div class="quick-stat-item">
                  <span class="quick-stat-val">nexdigicreative.com</span>
                  <span class="quick-stat-lbl">Company / Org</span>
                </div>
              </div>
            </div>

            <div class="activity-metrics-col">
              <div class="tech-dist-header">
                <span>Top Languages &amp; Stacks</span>
                <span class="dist-badge">100% Commits</span>
              </div>
              <div class="tech-dist-bar" title="Language distribution across public repositories">
                <div class="tech-bar-seg php" style="width: 58%;" title="PHP / Laravel (58%)"></div>
                <div class="tech-bar-seg js" style="width: 22%;" title="JavaScript / Frontend (22%)"></div>
                <div class="tech-bar-seg python" style="width: 12%;" title="Python / Automation (12%)"></div>
                <div class="tech-bar-seg other" style="width: 8%;" title="SQL / CSS / Dart (8%)"></div>
              </div>
              <div class="tech-dist-legend">
                <div class="legend-item"><span class="leg-dot php"></span><span>PHP / Laravel <strong>58%</strong></span></div>
                <div class="legend-item"><span class="leg-dot js"></span><span>JavaScript <strong>22%</strong></span></div>
                <div class="legend-item"><span class="leg-dot python"></span><span>Python <strong>12%</strong></span></div>
                <div class="legend-item"><span class="leg-dot other"></span><span>SQL &amp; Others <strong>8%</strong></span></div>
              </div>
              <div class="activity-badge-footer">
                <span class="badge-dot-live"></span>
                <span>Consistent Commit Cadence &bull; Production-Ready Architecture</span>
              </div>
            </div>
          </div>
        </div>

        <div class="github-repos-grid">
          <?php foreach ($githubRepos as $repo): 
            $langName = $repo['language'] ?? 'PHP';
            $langClass = 'php';
            if (stripos($langName, 'laravel') !== false || stripos($langName, 'blade') !== false) {
                $langClass = 'laravel';
            } elseif (stripos($langName, 'python') !== false) {
                $langClass = 'python';
            } elseif (stripos($langName, 'mysql') !== false || stripos($langName, 'sql') !== false) {
                $langClass = 'mysql';
            } elseif (stripos($langName, 'type') !== false || stripos($langName, 'ts') !== false) {
                $langClass = 'typescript';
            } elseif (stripos($langName, 'java') !== false || stripos($langName, 'js') !== false) {
                $langClass = 'javascript';
            } elseif (stripos($langName, 'css') !== false) {
                $langClass = 'css';
            } elseif (stripos($langName, 'html') !== false) {
                $langClass = 'html';
            } elseif (stripos($langName, 'dart') !== false) {
                $langClass = 'dart';
            }
          ?>
            <a href="<?= e($repo['html_url']) ?>" target="_blank" rel="noopener noreferrer" class="github-repo-card glass-panel">
              <div class="repo-card-top">
                <div class="repo-name-group">
                  <svg class="repo-book-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                  </svg>
                  <h3 class="repo-name"><?= e($repo['name']) ?></h3>
                </div>
                <span class="repo-badge-status <?= !empty($repo['is_fork']) ? '' : 'highlight' ?>">
                  <?= !empty($repo['is_fork']) ? 'Forked' : 'Public' ?>
                </span>
              </div>

              <p class="repo-description">
                <?= e($repo['description']) ?>
              </p>

              <div class="repo-meta-row">
                <div class="repo-lang">
                  <span class="lang-dot <?= e($langClass) ?>"></span>
                  <span><?= e($langName) ?></span>
                </div>
                <div class="repo-stats-group">
                  <span class="repo-stat-item" title="Stars"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg> <?= (int)$repo['stars'] ?></span>
                  <span class="repo-stat-item" title="Forks"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><circle cx="18" cy="6" r="3"/><path d="M6 9v1a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V9"/><path d="M12 12v3"/></svg> <?= (int)$repo['forks'] ?></span>
                </div>
                <span class="repo-updated">
                  <?php 
                    if (!empty($repo['updated_at'])) {
                        $diffDays = round((time() - strtotime($repo['updated_at'])) / 86400);
                        if ($diffDays <= 1) {
                            echo 'Updated recently';
                        } elseif ($diffDays < 30) {
                            echo "Updated {$diffDays}d ago";
                        } else {
                            echo 'Maintained';
                        }
                    } else {
                        echo 'Active';
                    }
                  ?>
                </span>
              </div>
            </a>
          <?php endforeach; ?>
        </div>

        <div class="github-footer-cta">
          <a href="<?= !empty($profile['github']) ? e($profile['github']) : 'https://github.com/ammarsyrf' ?>" target="_blank" rel="noopener noreferrer" class="btn-bento-pill primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0 0 22 12.017C22 6.484 17.522 2 12 2z"/></svg>
            <span>Kunjungi Profil GitHub @ammarsyrf (ZenS) ↗</span>
          </a>
        </div>
      </div>
    </section>

    <hr class="section-divider" />

    <!-- ====================================================================
         SECTION 5: CONTACT (Communication Bento)
         ==================================================================== -->
    <section id="contact" class="section-padding">
      <div class="site-container">
        <div class="section-header">
          <div class="section-caption"><?= e($homeContent['contact_caption']) ?></div>
          <h2 class="section-title"><?= e($homeContent['contact_title']) ?></h2>
        </div>

        <div class="contact-bento-grid">
          <?php if (!empty($profile['email'])): ?>
            <a href="mailto:<?= e($profile['email']) ?>" class="contact-bento-card contact-email-card">
              <span class="contact-card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
              </span>
              <span class="contact-card-copy">
                <span class="contact-kicker">Email langsung</span>
                <strong><?= e($homeContent['contact_email_title']) ?></strong>
                <small><?= e($homeContent['contact_email_description']) ?></small>
              </span>
              <span class="contact-card-arrow" aria-hidden="true">↗</span>
            </a>
          <?php endif; ?>

          <?php if (!empty($profile['linkedin'])): ?>
            <a href="<?= e($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer" class="contact-bento-card contact-linkedin-card">
              <span class="contact-card-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 9v9M6 6v.01M10 18v-5a4 4 0 0 1 8 0v5m-8-4v4"/></svg></span>
              <span class="contact-card-copy"><span class="contact-kicker">Jaringan profesional</span><strong><?= e($homeContent['contact_linkedin_title']) ?></strong><small><?= e($homeContent['contact_linkedin_description']) ?></small></span>
              <span class="contact-card-arrow" aria-hidden="true">↗</span>
            </a>
          <?php endif; ?>

          <?php if (!empty($profile['github'])): ?>
            <a href="<?= e($profile['github']) ?>" target="_blank" rel="noopener noreferrer" class="contact-bento-card contact-github-card">
              <span class="contact-card-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M15 22v-3.9c0-1 .1-1.6-.5-2.2 2.4-.3 4.9-1.2 4.9-5.3 0-1.2-.4-2.1-1.1-2.9.1-.3.5-1.4-.1-2.9 0 0-.9-.3-3 1.1a10.2 10.2 0 0 0-5.4 0C7.7 4.6 6.8 4.9 6.8 4.9c-.6 1.5-.2 2.6-.1 2.9-.7.8-1.1 1.7-1.1 2.9 0 4.1 2.5 5 4.9 5.3-.6.6-.6 1.3-.6 2.2V22"/><path d="M9 19c-2 .6-3.5-.5-4-1.5"/></svg></span>
              <span class="contact-card-copy"><span class="contact-kicker">Open source & kode</span><strong><?= e($homeContent['contact_github_title']) ?></strong><small><?= e($homeContent['contact_github_description']) ?></small></span>
              <span class="contact-card-arrow" aria-hidden="true">↗</span>
            </a>
          <?php endif; ?>

          <?php if (!empty($profile['instagram'])): ?>
            <a href="<?= e($profile['instagram']) ?>" target="_blank" rel="noopener noreferrer" class="contact-bento-card contact-instagram-card">
              <span class="contact-card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
              </span>
              <span class="contact-card-copy">
                <span class="contact-kicker">Media sosial</span>
                <strong><?= e($homeContent['contact_instagram_title']) ?></strong>
                <small><?= e($homeContent['contact_instagram_description']) ?></small>
              </span>
              <span class="contact-card-arrow" aria-hidden="true">↗</span>
            </a>
          <?php endif; ?>

          <?php if (!empty($profile['tiktok'])): ?>
            <a href="<?= e($profile['tiktok']) ?>" target="_blank" rel="noopener noreferrer" class="contact-bento-card contact-tiktok-card">
              <span class="contact-card-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
              </span>
              <span class="contact-card-copy">
                <span class="contact-kicker">Video & kreasi</span>
                <strong><?= e($homeContent['contact_tiktok_title']) ?></strong>
                <small><?= e($homeContent['contact_tiktok_description']) ?></small>
              </span>
              <span class="contact-card-arrow" aria-hidden="true">↗</span>
            </a>
          <?php endif; ?>

          <?php if (!empty($profile['email'])): ?>
            <form class="contact-message-card" id="contactQuickForm" data-recipient="<?= e($profile['email']) ?>">
              <div class="contact-form-heading">
                <span class="contact-kicker">Atau kirim pesan singkat</span>
                <strong><?= e($homeContent['contact_form_title']) ?></strong>
              </div>
              <div class="contact-form-row">
                <label><span>Nama</span><input type="text" name="name" placeholder="Nama kamu" required></label>
                <label><span>Email</span><input type="email" name="email" placeholder="email@kamu.com" required></label>
              </div>
              <label class="contact-message-field"><span>Pesan</span><textarea name="message" rows="3" placeholder="Halo Ammar, saya ingin berdiskusi tentang..." required></textarea></label>
              <button type="submit" class="contact-send-btn"><?= e($homeContent['contact_form_button']) ?> <span aria-hidden="true">↗</span></button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main>

  <!-- Site Footer -->
  <footer class="site-footer">
    <div class="site-container footer-inner">
      <div>
        &copy; <?= date('Y') ?> <?= e($profile['full_name']) ?> — Zenerie. Dark Glassmorphism Portfolio.
      </div>
      <div style="display: flex; flex-wrap: wrap; gap: 1rem 1.5rem; align-items: center; justify-content: center;">
        <a href="<?= BASE_URL ?>/links" class="footer-admin-link">Semua Tautan</a>
        <a href="<?= BASE_URL ?>/guestbook" class="footer-admin-link">Buku Tamu</a>
      </div>
    </div>
  </footer>

  <!-- ====================================================================
       MODAL DIALOG RINCIAN PROYEK (Dengan Video Demo & Format Impact Metrics)
       ==================================================================== -->
  <div id="project-modal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="modal-dialog">
      <button type="button" class="modal-close-btn" id="modal-close" aria-label="Tutup jendela rincian">
        &times;
      </button>

      <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap; margin-bottom: 0.75rem;">
        <span id="modal-cat" class="project-category-tag">Kategori</span>
        <span id="modal-role-pill" class="modal-role-pill">Peran</span>
      </div>
      
      <h3 class="modal-title" id="modal-title">Judul Proyek</h3>

      <!-- Media Preview Area (Tabs: Foto / Video Demo) -->
      <div class="modal-media-section">
        <div class="modal-media-tabs" id="modalMediaTabs" style="display: none;">
          <button type="button" class="modal-tab-btn active" id="modalTabPhoto" data-media-target="photo">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            <span>Screenshot Visual</span>
          </button>
          <button type="button" class="modal-tab-btn" id="modalTabVideo" data-media-target="video">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            <span>🎬 Video Demo Embed</span>
          </button>
        </div>

        <!-- Image Wrap -->
        <div id="modal-img-wrap" class="modal-img-box" style="display: none;">
          <img src="" alt="" id="modal-img" class="modal-img-element">
        </div>

        <!-- Video Wrap -->
        <div id="modal-video-wrap" class="modal-video-box" style="display: none;">
          <div class="video-responsive-container" id="modal-video-container">
            <!-- Iframe YouTube atau HTML5 Video di-inject via JS -->
          </div>
        </div>
      </div>

      <div class="modal-details-body">
        
        <!-- Meta Strip: Role & Tech Stack -->
        <div class="modal-meta-grid">
          <div>
            <div class="meta-label">Peran &amp; Kontribusi</div>
            <div id="modal-role" class="meta-value">-</div>
          </div>
          <div>
            <div class="meta-label">Teknologi yang Dipakai</div>
            <div id="modal-stack" class="meta-value">-</div>
          </div>
        </div>

        <!-- Deskripsi Utama -->
        <div class="modal-desc-box">
          <h4 class="modal-section-heading">Ringkasan Sistem</h4>
          <p id="modal-desc" class="modal-text">-</p>
        </div>

        <!-- ================================================================
             FORMAT IMPACT & METRICS (Problem, Solution, Impact)
             ================================================================ -->
        <div id="modal-impact-container" class="modal-impact-wrapper">
          <h4 class="modal-section-heading" style="display: flex; align-items: center; gap: 0.4rem;">
            <span>📊 Problem, Solution &amp; Tangible Impact</span>
          </h4>
          
          <div class="modal-impact-grid">
            <!-- Card 1: Problem -->
            <div class="impact-metric-card problem">
              <div class="impact-card-header">
                <span class="impact-icon">⚡</span>
                <strong>Problem / Latar Belakang</strong>
              </div>
              <p id="modal-impact-problem" class="impact-card-text">
                Proses alur data atau operasional sebelumnya masih manual dan belum terintegrasi secara terpusat.
              </p>
            </div>

            <!-- Card 2: Solution -->
            <div class="impact-metric-card solution">
              <div class="impact-card-header">
                <span class="impact-icon">🛠️</span>
                <strong>Solution / Rekayasa Teknis</strong>
              </div>
              <p id="modal-impact-solution" class="impact-card-text">
                Membangun arsitektur database relasional ternormalisasi (3NF), query indexing, dan alur kontrol RBAC.
              </p>
            </div>

            <!-- Card 3: Result & Impact -->
            <div class="impact-metric-card result">
              <div class="impact-card-header">
                <span class="impact-icon">📈</span>
                <strong>Result &amp; Impact / Metrik Hasil</strong>
              </div>
              <p id="modal-impact-result" class="impact-card-text">
                Meningkatkan efisiensi kerja hingga 70%, bebas kerentanan SQLi, dan siap melayani ratusan pengguna harian.
              </p>
            </div>
          </div>
        </div>

      </div>

      <!-- Action Buttons Footer -->
      <div id="modal-actions" class="modal-actions-footer">
        <!-- Dinamis via JS -->
      </div>
    </div>
  </div>

  <!-- Payload JSON Proyek untuk Modal -->
  <script type="application/json" id="projects-json">
    <?= json_encode($projectsPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
  </script>

  <!-- Command Palette Markup -->
  <?php require_once __DIR__ . '/includes/command_palette.php'; ?>

  <!-- Scripts -->
  <script src="<?= asset('js/main.js') ?>"></script>
  <script src="<?= asset('js/command_palette.js') ?>"></script>
</body>
</html>

