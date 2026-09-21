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
                        <span>ammar@zen:~$</span>
                      </div>
                      <div class="terminal-status-pill">
                        <span class="terminal-pulse-dot"></span>
                        <span>ACTIVE</span>
                      </div>
                    </div>

                    <!-- Terminal Interactive Console Screen -->
                    <div class="terminal-screen" id="terminalScreen">
                      <div class="term-line term-prompt-line">
                        <span class="term-user">zen@dev</span><span class="term-sep">:</span><span class="term-path">~</span><span class="term-char">$</span>
                        <span class="term-cmd-text">status --live</span>
                      </div>

                      <div class="term-code-block">
                        <div class="term-row">
                          <span class="term-k">status</span>
                          <span class="term-op">:</span>
                          <span class="term-v-status">"● Available for Hire"</span>
                        </div>
                        <div class="term-row">
                          <span class="term-k">role</span>
                          <span class="term-op">:</span>
                          <span class="term-v-str">"Fullstack &amp; AI-Augmented Dev"</span>
                        </div>
                        <div class="term-row">
                          <span class="term-k">focus</span>
                          <span class="term-op">:</span>
                          <span class="term-v-str">"Laravel 13 • MySQL • Next.js"</span>
                        </div>
                        <div class="term-row">
                          <span class="term-k">ai_stack</span>
                          <span class="term-op">:</span>
                          <span class="term-v-str">"Claude • Gemini • OpenAI • Prompting"</span>
                        </div>
                        <div class="term-row">
                          <span class="term-k">workflow</span>
                          <span class="term-op">:</span>
                          <span class="term-v-str">"Agentic Acceleration (3x Speed)"</span>
                        </div>
                        <div class="term-row">
                          <span class="term-k">location</span>
                          <span class="term-op">:</span>
                          <span class="term-v-str">"Jakarta, ID (GMT+7)"</span>
                        </div>
                      </div>

                      <!-- Interactive Command Output Simulation -->
                      <div class="term-interactive-zone" id="termInteractiveZone">
                        <div class="term-line term-prompt-line">
                          <span class="term-user">zen@dev</span><span class="term-sep">:</span><span class="term-path">~</span><span class="term-char">$</span>
                          <span class="term-active-cmd" id="termActiveCmd">pest test &amp;&amp; eval-prompt</span>
                          <span class="term-cursor" id="termCursor">▋</span>
                        </div>
                        <div class="term-test-output" id="termTestOutput" style="display: none;">
                          <div class="term-test-pass">
                            <span class="term-badge-pass">PASS</span>
                            <span>Pest v3 &bull; 18 passed (0.04s)</span>
                          </div>
                          <div class="term-test-summary" style="display: flex; flex-direction: column; gap: 0.15rem; margin-top: 0.2rem;">
                            <span style="color: #34d399;">✓ 100% Bebas SQLi &amp; Siap Produksi</span>
                            <span style="color: #c084fc;">✓ Prompt Token Efficiency: 98.4% (Optimal Reasoning)</span>
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
                      <!-- ==================== FRONTEND ==================== -->
                      <div class="skill-icon-chip" data-cat="frontend" data-highlight="true" title="Next.js — Fullstack React & App Router">
                        <div class="chip-svg-wrap nextjs">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M9 15V9l7.5 9"></path>
                            <path d="M15 9v3.5"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Next.js</span>
                          <span class="chip-sub">React SSR</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="frontend" data-highlight="true" title="React — Component-Driven Architecture">
                        <div class="chip-svg-wrap react">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="2.5"></circle>
                            <ellipse cx="12" cy="12" rx="9" ry="3.8" transform="rotate(30 12 12)"></ellipse>
                            <ellipse cx="12" cy="12" rx="9" ry="3.8" transform="rotate(90 12 12)"></ellipse>
                            <ellipse cx="12" cy="12" rx="9" ry="3.8" transform="rotate(150 12 12)"></ellipse>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">React</span>
                          <span class="chip-sub">UI Library</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="frontend" title="TypeScript — Typed JavaScript">
                        <div class="chip-svg-wrap ts">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                            <path d="M7 8h6m-3 0v8"></path>
                            <path d="M15 11c1-1 2-1 2.5 0s0 2-1 2.5 2 1.5 1.5 2.5-2 1-3 0"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">TypeScript</span>
                          <span class="chip-sub">Type-Safe JS</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="frontend" title="Tailwind CSS — Utility-First CSS">
                        <div class="chip-svg-wrap tailwind">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 12c.5-2.5 2.5-4 5-4 3.5 0 4.5 2.5 6 3 1.5.5 2.5 0 3-1-1 3-3 4-5 4-3.5 0-4.5-2.5-6-3-1.5-.5-2.5 0-3 1z"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Tailwind CSS</span>
                          <span class="chip-sub">Utility CSS</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="frontend" title="shadcn/ui — Re-usable UI Primitives">
                        <div class="chip-svg-wrap shadcn">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M8 12h8"></path>
                            <path d="M12 8v8"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">shadcn/ui</span>
                          <span class="chip-sub">UI Component</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="frontend" title="Bootstrap — Responsive Framework">
                        <div class="chip-svg-wrap bootstrap">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="4"></rect>
                            <path d="M9 8h4a2 2 0 0 1 0 4H9zm0 4h4.5a2 2 0 0 1 0 4H9z"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Bootstrap</span>
                          <span class="chip-sub">Grid &amp; UI</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="frontend" title="JavaScript (ES6+) — Dynamic DOM Logic">
                        <div class="chip-svg-wrap js">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16v16H4z"></path>
                            <path d="M10 15v-5"></path>
                            <path d="M14 15c0-1.5 2-1.5 2-3s-2-1.5-2-3"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">JavaScript</span>
                          <span class="chip-sub">ES6+ Logic</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="frontend" title="HTML5 — Semantic Web Markup">
                        <div class="chip-svg-wrap html">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="16 18 22 12 16 6"></polyline>
                            <polyline points="8 6 2 12 8 18"></polyline>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">HTML5</span>
                          <span class="chip-sub">Semantic</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="frontend" title="CSS3 — Modern Styling & Glassmorphism">
                        <div class="chip-svg-wrap css">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                            <polyline points="2 17 12 22 22 17"></polyline>
                            <polyline points="2 12 12 17 22 12"></polyline>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">CSS3</span>
                          <span class="chip-sub">Modern Style</span>
                        </div>
                      </div>

                      <!-- ==================== BACKEND ==================== -->
                      <div class="skill-icon-chip" data-cat="backend" data-highlight="true" title="Laravel 13 — Modern PHP Framework">
                        <div class="chip-svg-wrap laravel">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5 12 2"></polygon>
                            <line x1="12" y1="22" x2="12" y2="12"></line>
                            <polyline points="22 8.5 12 12 2 8.5"></polyline>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Laravel 13</span>
                          <span class="chip-sub">Framework</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="backend" title="PHP 8.x — Core Server Engineering">
                        <div class="chip-svg-wrap php">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="16 18 22 12 16 6"></polyline>
                            <polyline points="8 6 2 12 8 18"></polyline>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">PHP 8.x</span>
                          <span class="chip-sub">Backend Core</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="backend" title="Blade — Template Engine">
                        <div class="chip-svg-wrap laravel">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 19 21 12 17 5 21 12 2"></polygon>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Blade</span>
                          <span class="chip-sub">Templating</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="backend" title="Inertia.js — Modern Monolith Bridge">
                        <div class="chip-svg-wrap react">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Inertia.js</span>
                          <span class="chip-sub">Monolith SPA</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="backend" title="REST API — Modular Endpoints">
                        <div class="chip-svg-wrap api">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="18" cy="5" r="3"></circle>
                            <circle cx="6" cy="12" r="3"></circle>
                            <circle cx="18" cy="19" r="3"></circle>
                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">REST API</span>
                          <span class="chip-sub">JSON Endpoints</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="backend" title="Webhook — Event-Driven Integration">
                        <div class="chip-svg-wrap api">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Webhook</span>
                          <span class="chip-sub">Event Listeners</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="backend" title="Queue / Jobs — Asynchronous Processing">
                        <div class="chip-svg-wrap php">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Queue / Jobs</span>
                          <span class="chip-sub">Async Workers</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="backend" title="Scheduler — Cron Automation">
                        <div class="chip-svg-wrap php">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Scheduler</span>
                          <span class="chip-sub">Cron Tasks</span>
                        </div>
                      </div>

                      <!-- ==================== AI & PROMPTING ==================== -->
                      <div class="skill-icon-chip" data-cat="ai" data-highlight="true" title="Prompt Engineering — Context Priming, Few-Shot &amp; System Directives">
                        <div class="chip-svg-wrap ai">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Prompt Eng.</span>
                          <span class="chip-sub">Context &amp; Logic</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="ai" title="Claude 3.7 &amp; Sonnet — Complex Logic &amp; Architectural Synthesis">
                        <div class="chip-svg-wrap claude">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Claude &amp; Gemini</span>
                          <span class="chip-sub">Architecture AI</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="ai" title="OpenAI / ChatGPT — Reasoning Models &amp; Workflow Automation">
                        <div class="chip-svg-wrap openai">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                            <circle cx="9" cy="9" r="2"></circle>
                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">OpenAI / LLMs</span>
                          <span class="chip-sub">Reasoning &amp; APIs</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="ai" title="Cursor &amp; Agentic AI — Autonomous Multi-File Development">
                        <div class="chip-svg-wrap cursor">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="4 17 10 11 4 5"></polyline>
                            <line x1="12" y1="19" x2="20" y2="19"></line>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Agentic Dev</span>
                          <span class="chip-sub">Cursor &amp; Tools</span>
                        </div>
                      </div>

                      <!-- ==================== DATABASE ==================== -->
                      <div class="skill-icon-chip" data-cat="database" data-highlight="true" title="MySQL — Database Utama">
                        <div class="chip-svg-wrap mysql">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                            <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">MySQL ⭐</span>
                          <span class="chip-sub">Utama / RDBMS</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="database" title="MariaDB — Open-Source RDBMS">
                        <div class="chip-svg-wrap mariadb">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                            <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                            <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">MariaDB</span>
                          <span class="chip-sub">Relasional</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="database" title="PostgreSQL — Advanced Relational Database">
                        <div class="chip-svg-wrap postgres">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">PostgreSQL</span>
                          <span class="chip-sub">Advanced SQL</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="database" title="Redis — In-Memory Data Store & Cache">
                        <div class="chip-svg-wrap redis">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                            <polyline points="2 17 12 22 22 17"></polyline>
                            <polyline points="2 12 12 17 22 12"></polyline>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Redis</span>
                          <span class="chip-sub">In-Memory Cache</span>
                        </div>
                      </div>

                      <!-- ==================== DEVOPS ==================== -->
                      <div class="skill-icon-chip" data-cat="devops" data-highlight="true" title="Docker — Application Containerization">
                        <div class="chip-svg-wrap docker">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="14" width="18" height="7" rx="2"></rect>
                            <path d="M7 14v-3h3v3"></path>
                            <path d="M11 14v-3h3v3"></path>
                            <path d="M15 14v-3h3v3"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Docker</span>
                          <span class="chip-sub">Containers</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="devops" title="Git &amp; GitHub — Version Control">
                        <div class="chip-svg-wrap git">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="18" cy="18" r="3"></circle>
                            <circle cx="6" cy="6" r="3"></circle>
                            <path d="M18 9a9 9 0 0 1-9 9"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Git / GitHub</span>
                          <span class="chip-sub">Version Control</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="devops" title="Linux / Ubuntu Server — CLI & Administration">
                        <div class="chip-svg-wrap linux">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Linux / Ubuntu</span>
                          <span class="chip-sub">Server OS</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="devops" title="Apache &amp; Nginx — Web Servers & Reverse Proxy">
                        <div class="chip-svg-wrap nginx">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="2" width="20" height="8" rx="2"></rect>
                            <rect x="2" y="14" width="20" height="8" rx="2"></rect>
                            <line x1="6" y1="6" x2="6.01" y2="6"></line>
                            <line x1="6" y1="18" x2="6.01" y2="18"></line>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Apache / Nginx</span>
                          <span class="chip-sub">Web Server</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="devops" title="Cloudflare — CDN, SSL & DNS Shield">
                        <div class="chip-svg-wrap cloudflare">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Cloudflare</span>
                          <span class="chip-sub">CDN &amp; Shield</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="devops" title="Composer — PHP Dependency Management">
                        <div class="chip-svg-wrap php">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Composer</span>
                          <span class="chip-sub">Package Manager</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="devops" title="SSH — Secure Shell Access">
                        <div class="chip-svg-wrap linux">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                            <polyline points="8 9 11 12 8 15"></polyline>
                            <line x1="13" y1="15" x2="16" y2="15"></line>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">SSH</span>
                          <span class="chip-sub">Remote Shell</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="devops" title="Supervisor — Process Control Daemon">
                        <div class="chip-svg-wrap linux">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Supervisor</span>
                          <span class="chip-sub">Daemon Monitor</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="devops" title="VPS / Shared Hosting — Production Deployment">
                        <div class="chip-svg-wrap linux">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="2" width="20" height="8" rx="2"></rect>
                            <rect x="2" y="14" width="20" height="8" rx="2"></rect>
                            <line x1="6" y1="6" x2="6.01" y2="6"></line>
                            <line x1="6" y1="18" x2="6.01" y2="18"></line>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">VPS / Hosting</span>
                          <span class="chip-sub">Cloud Infra</span>
                        </div>
                      </div>

                      <!-- ==================== TESTING ==================== -->
                      <div class="skill-icon-chip" data-cat="testing" data-highlight="true" title="Pest — Elegant PHP Testing Framework">
                        <div class="chip-svg-wrap test">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"></polyline>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Pest</span>
                          <span class="chip-sub">Modern Testing</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="testing" title="PHPUnit — Automated Unit &amp; Feature Testing">
                        <div class="chip-svg-wrap test">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">PHPUnit</span>
                          <span class="chip-sub">Unit Tests</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="testing" title="PHPStan / Larastan — Strict Static Analysis">
                        <div class="chip-svg-wrap test">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Larastan</span>
                          <span class="chip-sub">Static Analysis</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="testing" title="Laravel Pint — Code Style Standardizer">
                        <div class="chip-svg-wrap test">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="4 7 4 4 20 4 20 7"></polyline>
                            <line x1="9" y1="20" x2="15" y2="20"></line>
                            <line x1="12" y1="4" x2="12" y2="20"></line>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Laravel Pint</span>
                          <span class="chip-sub">Code Style</span>
                        </div>
                      </div>

                      <!-- ==================== MOBILE & DESIGN ==================== -->
                      <div class="skill-icon-chip" data-cat="mobile" data-highlight="true" title="Flutter — Cross-Platform Mobile SDK">
                        <div class="chip-svg-wrap flutter">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="14 2 2 14 6 18 18 6 14 2"></polygon>
                            <polygon points="14 14 10 18 14 22 18 18 14 14"></polygon>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Flutter</span>
                          <span class="chip-sub">Mobile SDK</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="mobile" title="Dart — Client-Optimized Language">
                        <div class="chip-svg-wrap flutter">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 2 22 12 17 22 22 12 2"></polygon>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Dart</span>
                          <span class="chip-sub">Core Language</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="mobile" title="Firebase — Mobile &amp; Web Backend Services">
                        <div class="chip-svg-wrap firebase">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 18l4-15 4 8 4-4 4 11H4z"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Firebase</span>
                          <span class="chip-sub">Cloud Platform</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="mobile" title="Figma — UI/UX Prototyping &amp; Design Systems">
                        <div class="chip-svg-wrap figma">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M5 5.5A3.5 3.5 0 0 1 8.5 2H12v7H8.5A3.5 3.5 0 0 1 5 5.5z"></path>
                            <path d="M12 2h3.5a3.5 3.5 0 1 1 0 7H12V2z"></path>
                            <path d="M12 12.5a3.5 3.5 0 1 1 7 0 3.5 3.5 0 1 1-7 0z"></path>
                            <path d="M5 19.5A3.5 3.5 0 0 1 8.5 16H12v3.5a3.5 3.5 0 1 1-7 0z"></path>
                            <path d="M5 12.5A3.5 3.5 0 0 1 8.5 9H12v7H8.5A3.5 3.5 0 0 1 5 12.5z"></path>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Figma</span>
                          <span class="chip-sub">UI/UX Design</span>
                        </div>
                      </div>

                      <!-- ==================== ANALITIK DATA & SISTEM ==================== -->
                      <div class="skill-icon-chip" data-cat="analytics" data-highlight="true" title="Algoritma C4.5 — Decision Tree Classification">
                        <div class="chip-svg-wrap c45">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="5" r="3"></circle>
                            <circle cx="6" cy="19" r="3"></circle>
                            <circle cx="18" cy="19" r="3"></circle>
                            <line x1="12" y1="8" x2="6" y2="16"></line>
                            <line x1="12" y1="8" x2="18" y2="16"></line>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Algoritma C4.5</span>
                          <span class="chip-sub">Decision Tree</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="analytics" title="RapidMiner Studio — Data Mining Suite">
                        <div class="chip-svg-wrap rm">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect>
                            <rect x="9" y="9" width="6" height="6"></rect>
                            <line x1="9" y1="1" x2="9" y2="4"></line>
                            <line x1="15" y1="1" x2="15" y2="4"></line>
                            <line x1="9" y1="20" x2="9" y2="23"></line>
                            <line x1="15" y1="20" x2="15" y2="23"></line>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">RapidMiner</span>
                          <span class="chip-sub">Data Mining</span>
                        </div>
                      </div>

                      <div class="skill-icon-chip" data-cat="analytics" title="S1 Sistem Informasi — SDLC &amp; Business Process">
                        <div class="chip-svg-wrap dm">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">S1 Sistem Info</span>
                          <span class="chip-sub">Riset &amp; SDLC</span>
                        </div>
                      </div>

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
                      <span class="deck-eyebrow">Cara saya bekerja</span>
                      <h4 class="deck-vision-title">Sistem yang siap dipakai, bukan sekadar selesai.</h4>
                      <p class="deck-vision-lead">
                        Saya menerjemahkan kebutuhan bisnis menjadi alur yang jelas, data yang rapi, dan antarmuka yang nyaman digunakan.
                      </p>
                    </div>
                    <div class="deck-focus-list">
                      <div class="deck-focus-item">
                        <span class="deck-focus-index">01</span>
                        <div><strong>Pahami alur bisnis</strong><span>Mulai dari masalah pengguna dan proses yang ingin dipermudah.</span></div>
                      </div>
                      <div class="deck-focus-item">
                        <span class="deck-focus-index">02</span>
                        <div><strong>Bangun fondasi yang rapi</strong><span>Struktur data, backend, dan UI dirancang agar mudah dikembangkan.</span></div>
                      </div>
                      <div class="deck-focus-item">
                        <span class="deck-focus-index">03</span>
                        <div><strong>Validasi sampai siap pakai</strong><span>Fokus pada detail yang membuat sistem stabil untuk operasional harian.</span></div>
                      </div>
                    </div>
                    <div class="deck-card-actions">
                      <?php if ($hasCv): ?>
                        <a href="<?= e($cvUrl) ?>" download class="btn-bento-pill primary">Unduh Resume</a>
                      <?php else: ?>
                        <a href="#about" class="btn-bento-pill primary">Detail Profil</a>
                      <?php endif; ?>
                      <span class="bento-status-badge-inline">● S1 Sistem Informasi</span>
                    </div>
                  </div>

                  <div class="bento-card bento-card-deck deck-standards-card">
                    <div class="bento-card-heading">
                      <h4>Standar Rekayasa</h4>
                    </div>
                    <div class="deck-standards-list">
                      <div class="standard-row">
                        <span class="standard-icon">🛡️</span>
                        <div>
                          <div class="standard-title">Aman dari Fondasi</div>
                          <div class="standard-desc">Validasi input, prepared statement, dan akses data yang terjaga.</div>
                        </div>
                      </div>
                      <div class="standard-row">
                        <span class="standard-icon">⚡</span>
                        <div>
                          <div class="standard-title">Terstruktur &amp; Ringan</div>
                          <div class="standard-desc">Struktur data jelas dan antarmuka yang fokus pada kebutuhan pengguna.</div>
                        </div>
                      </div>
                      <div class="standard-row">
                        <span class="standard-icon">🎯</span>
                        <div>
                          <div class="standard-title">Berorientasi Dampak</div>
                          <div class="standard-desc">Data dan metrik dipakai untuk membantu keputusan yang lebih tepat.</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="bento-card bento-card-deck deck-status-card">
                    <div class="bento-card-heading">
                      <h4>Siap Berkolaborasi</h4>
                    </div>
                    <div class="deck-qualifications-list">
                      <div class="qual-item">
                        <span class="qual-label">Fokus peran</span>
                        <span class="qual-val">Web Developer &amp; Data Analyst</span>
                      </div>
                      <div class="qual-item">
                        <span class="qual-label">Ketersediaan</span>
                        <span class="qual-val highlight">● Terbuka untuk peluang kerja</span>
                      </div>
                      <div class="qual-item">
                        <span class="qual-label">Cara kerja</span>
                        <span class="qual-val">Onsite atau remote (Indonesia)</span>
                      </div>
                    </div>
                    <div class="deck-status-action">
                      <a href="#contact" class="btn-bento-pill primary">Hubungi Ammar</a>
                    </div>
                  </div>

                </div>

                <!-- ==============================================
                     PANEL 2: KEAHLIAN (Arsitektur & Domain Teknis)
                     ============================================== -->
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
                          <strong class="gear-spec-name">AMD Ryzen™ / Intel Core i7</strong>
                          <span class="gear-spec-detail">32GB RAM DDR5 • 1TB NVMe PCIe 4.0</span>
                        </div>
                      </div>
                      <div class="gear-spec-item">
                        <div class="gear-spec-icon">🖥️</div>
                        <div class="gear-spec-content">
                          <span class="gear-spec-label">Dual Display Setup</span>
                          <strong class="gear-spec-name">27&quot; QHD 165Hz IPS + 24&quot; Portrait</strong>
                          <span class="gear-spec-detail">Optimal koding vertikal &amp; terminal preview</span>
                        </div>
                      </div>
                      <div class="gear-spec-item">
                        <div class="gear-spec-icon">⌨️</div>
                        <div class="gear-spec-content">
                          <span class="gear-spec-label">Input &amp; Periferal</span>
                          <strong class="gear-spec-name">Custom 75% Mechanical Keyboard</strong>
                          <span class="gear-spec-detail">Lubed Linear Switches • Wireless Precision Mouse</span>
                        </div>
                      </div>
                      <div class="gear-spec-item">
                        <div class="gear-spec-icon">🎧</div>
                        <div class="gear-spec-content">
                          <span class="gear-spec-label">Audio Monitor</span>
                          <strong class="gear-spec-name">Studio Monitor Headphones / IEM</strong>
                          <span class="gear-spec-detail">Acoustic clarity &amp; passive noise isolation</span>
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
                          <span class="gear-tool-tag highlighted">Windows 11 Pro</span>
                          <span class="gear-tool-tag highlighted">WSL2 (Ubuntu 24.04)</span>
                          <span class="gear-tool-tag">Windows Terminal</span>
                          <span class="gear-tool-tag">PowerShell 7</span>
                          <span class="gear-tool-tag">Starship Prompt</span>
                          <span class="gear-tool-tag">Git CLI</span>
                        </div>
                      </div>
                      <div class="gear-cat-block">
                        <span class="gear-block-title">IDE &amp; Editors</span>
                        <div class="gear-tag-cluster">
                          <span class="gear-tool-tag highlighted">Antigravity IDE</span>
                          <span class="gear-tool-tag highlighted">Cursor AI</span>
                          <span class="gear-tool-tag">VS Code</span>
                          <span class="gear-tool-tag">JetBrains Mono</span>
                        </div>
                      </div>
                      <div class="gear-cat-block">
                        <span class="gear-block-title">Runtime, DB &amp; API</span>
                        <div class="gear-tag-cluster">
                          <span class="gear-tool-tag">PHP 8.2</span>
                          <span class="gear-tool-tag highlighted">Laravel 13</span>
                          <span class="gear-tool-tag">Node.js 20</span>
                          <span class="gear-tool-tag">MySQL / PostgreSQL</span>
                          <span class="gear-tool-tag">TablePlus</span>
                          <span class="gear-tool-tag">Postman</span>
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
                          <span>Claude 3.7 Sonnet (Thinking Mode), OpenAI GPT-4o, Google Gemini 2.5 Pro.</span>
                        </div>
                      </div>
                      <div class="gear-ai-item">
                        <div class="gear-ai-icon">🎯</div>
                        <div>
                          <strong>Prompt &amp; Agent Engineering</strong>
                          <span>System prompts, multi-agent chaining, context grounding &amp; function calling.</span>
                        </div>
                      </div>
                      <div class="gear-ai-item">
                        <div class="gear-ai-icon">🎨</div>
                        <div>
                          <strong>Desain &amp; Manajemen Ide</strong>
                          <span>Figma (Design System &amp; UI prototype), Obsidian (Markdown PKM), Notion.</span>
                        </div>
                      </div>
                      <div class="gear-ai-item">
                        <div class="gear-ai-icon">🚀</div>
                        <div>
                          <strong>DevOps &amp; Hosting</strong>
                          <span>SSH Key-based deployment, SCP automation, Nginx, GitKraken.</span>
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
            <span class="hierarchy-kicker">⚡ Tingkat Kenyamanan &amp; Kesiapan Produksi</span>
            <h3 class="hierarchy-title">Matriks Penguasaan Teknologi</h3>
            <p class="hierarchy-desc">Pemetaan keahlian teknis berdasarkan intensitas penggunaan riil dalam membangun aplikasi siap produksi.</p>
          </div>

          <div class="skill-hierarchy-grid">
            <!-- Tier 1: Advanced / Production-Ready -->
            <div class="skill-tier-card tier-advanced">
              <div class="tier-top">
                <div class="tier-badge-pill">
                  <span class="tier-dot-pulse green"></span>
                  <span>Advanced / Production-Ready</span>
                </div>
                <span class="tier-level-caption">Kenyamanan Penuh &bull; 95%</span>
              </div>
              <h4 class="tier-title">Fondasi Inti &amp; Arsitektur Sistem</h4>
              <p class="tier-desc">Sangat nyaman merancang sistem dari nol: database relasional 3NF, backend MVC terstruktur, API aman bebas SQLi, dan logic kompleks.</p>
              
              <div class="tier-progress-track">
                <div class="tier-progress-fill advanced" style="width: 95%;"></div>
              </div>

              <div class="tier-chips-wrap">
                <span class="tier-chip highlight"><i class="tier-chip-icon">⚡</i> PHP Native</span>
                <span class="tier-chip highlight"><i class="tier-chip-icon">🔴</i> Laravel</span>
                <span class="tier-chip highlight"><i class="tier-chip-icon">🐬</i> MySQL</span>
                <span class="tier-chip highlight"><i class="tier-chip-icon">🔗</i> REST API</span>
                <span class="tier-chip highlight"><i class="tier-chip-icon">🟡</i> JavaScript</span>
              </div>
            </div>

            <!-- Tier 2: Proficient -->
            <div class="skill-tier-card tier-proficient">
              <div class="tier-top">
                <div class="tier-badge-pill blue">
                  <span class="tier-dot-pulse blue"></span>
                  <span>Proficient</span>
                </div>
                <span class="tier-level-caption">Penggunaan Harian &bull; 85%</span>
              </div>
              <h4 class="tier-title">Web Modern &amp; Infrastruktur Harian</h4>
              <p class="tier-desc">Terbiasa digunakan dalam alur kerja harian untuk membuat antarmuka responsif cepat, version control, dan konfigurasi web server VPS.</p>

              <div class="tier-progress-track">
                <div class="tier-progress-fill proficient" style="width: 85%;"></div>
              </div>

              <div class="tier-chips-wrap">
                <span class="tier-chip"><i class="tier-chip-icon">⚛️</i> React</span>
                <span class="tier-chip"><i class="tier-chip-icon">▲</i> Next.js</span>
                <span class="tier-chip"><i class="tier-chip-icon">🌿</i> Git / GitHub</span>
                <span class="tier-chip"><i class="tier-chip-icon">🐧</i> Linux VPS</span>
                <span class="tier-chip"><i class="tier-chip-icon">🎨</i> Tailwind CSS</span>
                <span class="tier-chip"><i class="tier-chip-icon">🟢</i> Nginx</span>
              </div>
            </div>

            <!-- Tier 3: Familiar / Exploring -->
            <div class="skill-tier-card tier-familiar">
              <div class="tier-top">
                <div class="tier-badge-pill purple">
                  <span class="tier-dot-pulse purple"></span>
                  <span>Familiar / Exploring</span>
                </div>
                <span class="tier-level-caption">Riset &amp; Utility &bull; 75%</span>
              </div>
              <h4 class="tier-title">Container, Skrip &amp; Lapisan Caching</h4>
              <p class="tier-desc">Pemahaman arsitektur solid untuk isolasi container, skrip data mining C4.5 Python, akselerasi caching in-memory, dan perlindungan CDN.</p>

              <div class="tier-progress-track">
                <div class="tier-progress-fill familiar" style="width: 75%;"></div>
              </div>

              <div class="tier-chips-wrap">
                <span class="tier-chip"><i class="tier-chip-icon">🐳</i> Docker</span>
                <span class="tier-chip"><i class="tier-chip-icon">🐍</i> Python</span>
                <span class="tier-chip"><i class="tier-chip-icon">⚡</i> Redis</span>
                <span class="tier-chip"><i class="tier-chip-icon">☁️</i> Cloudflare</span>
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
              <span class="skills-badge-tag">High-Fidelity UI</span>
            </div>

            <h3 class="skills-domain-title">Frontend Engineering</h3>
            <p class="skills-domain-desc">
              Antarmuka modern, interaktif, dan ultra-responsif dengan performa rendering tinggi, arsitektur komponen re-usable, dan desain sistem presisi.
            </p>

            <div class="skills-subgroups">
              <div class="skills-subgroup">
                <span class="skills-subgroup-label">Frameworks &amp; Modern UI</span>
                <div class="skills-chips-wrapper">
                  <span class="skill-badge"><i class="badge-dot"></i>React</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Next.js (App Router)</span>
                  <span class="skill-badge"><i class="badge-dot"></i>shadcn/ui</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Tailwind CSS</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Bootstrap</span>
                </div>
              </div>

              <div class="skills-subgroup">
                <span class="skills-subgroup-label">Core Scripting &amp; Standards</span>
                <div class="skills-chips-wrapper">
                  <span class="skill-badge"><i class="badge-dot"></i>TypeScript</span>
                  <span class="skill-badge"><i class="badge-dot"></i>JavaScript (ES6+)</span>
                  <span class="skill-badge"><i class="badge-dot"></i>HTML5 Semantik</span>
                  <span class="skill-badge"><i class="badge-dot"></i>CSS3 Glassmorphism</span>
                </div>
              </div>
            </div>

            <div class="skills-highlights">
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>SSR, SSG, dan Client-Side Rendering teroptimasi untuk kecepatan First Contentful Paint</span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Desain sistem berbasis utility class Tailwind CSS &amp; atomic primitive shadcn/ui</span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Interaktivitas 60 FPS dengan micro-interactions, responsive grid, dan transisi fluid</span>
              </div>
            </div>

            <div class="skills-card-footer">
              <span class="footer-indicator-dot"></span>
              <span class="footer-meta-text">Eksosistem: Next.js, React, TypeScript, Tailwind, shadcn/ui</span>
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
              <span class="skills-badge-tag">Core Architecture</span>
            </div>

            <h3 class="skills-domain-title">Backend &amp; Architecture</h3>
            <p class="skills-domain-desc">
              Logika bisnis server-side yang tangguh, modular, dan scalable dengan fokus mutlak pada integritas transaksi, asynchronous processing, dan keamanan API.
            </p>

            <div class="skills-subgroups">
              <div class="skills-subgroup">
                <span class="skills-subgroup-label">Framework &amp; Server Logic</span>
                <div class="skills-chips-wrapper">
                  <span class="skill-badge"><i class="badge-dot"></i>Laravel Framework</span>
                  <span class="skill-badge"><i class="badge-dot"></i>PHP 8.x Core</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Blade Engine</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Inertia.js (SPA)</span>
                </div>
              </div>

              <div class="skills-subgroup">
                <span class="skills-subgroup-label">Asynchronous, API &amp; Services</span>
                <div class="skills-chips-wrapper">
                  <span class="skill-badge"><i class="badge-dot"></i>RESTful API (JSON)</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Webhook Listeners</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Queue / Jobs Worker</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Scheduler (Cron)</span>
                </div>
              </div>
            </div>

            <div class="skills-highlights">
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Arsitektur MVC &amp; Service Repository pattern untuk clean code dan kemudahan perawatan</span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Pemrosesan antrean background via Queue/Jobs dan otomasi cron job berkala</span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Integrasi webhook aman, autentikasi multi-tier (RBAC), dan session management</span>
              </div>
            </div>

            <div class="skills-card-footer">
              <span class="footer-indicator-dot"></span>
              <span class="footer-meta-text">Eksosistem: Laravel, PHP 8.x, Queue/Jobs, Webhook, REST API</span>
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
              <span class="skills-badge-tag">Data Persistence</span>
            </div>

            <h3 class="skills-domain-title">Database &amp; Storage</h3>
            <p class="skills-domain-desc">
              Pengelolaan penyimpanan data relasional dan in-memory yang teroptimasi, menjamin kehandalan transaksi (ACID), integritas referensial, dan caching latency rendah.
            </p>

            <div class="skills-subgroups">
              <div class="skills-subgroup">
                <span class="skills-subgroup-label">Relational Database Engines</span>
                <div class="skills-chips-wrapper">
                  <span class="skill-badge"><i class="badge-dot"></i>MySQL ⭐ (Utama)</span>
                  <span class="skill-badge"><i class="badge-dot"></i>MariaDB</span>
                  <span class="skill-badge"><i class="badge-dot"></i>PostgreSQL</span>
                </div>
              </div>

              <div class="skills-subgroup">
                <span class="skills-subgroup-label">In-Memory &amp; Data Integrity</span>
                <div class="skills-chips-wrapper">
                  <span class="skill-badge"><i class="badge-dot"></i>Redis (In-Memory Cache)</span>
                  <span class="skill-badge"><i class="badge-dot"></i>PDO Prepared Stmt</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Query Indexing &amp; ACID</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Relational ERD (3NF)</span>
                </div>
              </div>
            </div>

            <div class="skills-highlights">
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Perancangan skema relasional 3NF terstruktur dengan composite indexing untuk query cepat</span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Proteksi penuh injeksi SQL via PDO parameter binding &amp; sanitasi input ketat</span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Redis in-memory key-value caching untuk throttling, session, dan akselerasi data realtime</span>
              </div>
            </div>

            <div class="skills-card-footer">
              <span class="footer-indicator-dot"></span>
              <span class="footer-meta-text">Engine Utama: MySQL ⭐, PostgreSQL, MariaDB, Redis</span>
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
              <span class="skills-badge-tag">Deployment &amp; Infra</span>
            </div>

            <h3 class="skills-domain-title">DevOps &amp; Server Infra</h3>
            <p class="skills-domain-desc">
              Infrastruktur deployment handal dari isolasi container hingga konfigurasi web server produksi, keamanan jaringan CDN, dan otomatisasi process daemon.
            </p>

            <div class="skills-subgroups">
              <div class="skills-subgroup">
                <span class="skills-subgroup-label">Container &amp; Version Control</span>
                <div class="skills-chips-wrapper">
                  <span class="skill-badge"><i class="badge-dot"></i>Docker Containers</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Git / GitHub Flow</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Composer Package Mgr</span>
                </div>
              </div>

              <div class="skills-subgroup">
                <span class="skills-subgroup-label">Server OS, Web Server &amp; Cloud</span>
                <div class="skills-chips-wrapper">
                  <span class="skill-badge"><i class="badge-dot"></i>Linux / Ubuntu Server</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Apache &amp; Nginx</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Cloudflare CDN / SSL</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Supervisor Daemon</span>
                  <span class="skill-badge"><i class="badge-dot"></i>SSH &amp; VPS / Hosting</span>
                </div>
              </div>
            </div>

            <div class="skills-highlights">
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Environment isolasi via Docker container dan konfigurasi VPS Linux mandiri</span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Reverse proxy Nginx/Apache, SSL otomatis, dan proteksi DNS CDN Cloudflare</span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Supervisor process manager untuk monitoring berkelanjutan Queue Workers &amp; Scheduler</span>
              </div>
            </div>

            <div class="skills-card-footer">
              <span class="footer-indicator-dot"></span>
              <span class="footer-meta-text">Infrastruktur: Docker, Linux VPS, Nginx, Cloudflare, Git</span>
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
              <span class="skills-badge-tag">Quality &amp; Mobile</span>
            </div>

            <h3 class="skills-domain-title">Testing, QA &amp; Mobile</h3>
            <p class="skills-domain-desc">
              Penjaminan mutu kode dengan automated testing ketat, standardisasi kode modern, serta pengembangan aplikasi mobile multiplatform yang terintegrasi cloud.
            </p>

            <div class="skills-subgroups">
              <div class="skills-subgroup">
                <span class="skills-subgroup-label">Automated Testing &amp; Standards</span>
                <div class="skills-chips-wrapper">
                  <span class="skill-badge"><i class="badge-dot"></i>Pest PHP Testing</span>
                  <span class="skill-badge"><i class="badge-dot"></i>PHPUnit Suite</span>
                  <span class="skill-badge"><i class="badge-dot"></i>PHPStan / Larastan</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Laravel Pint</span>
                </div>
              </div>

              <div class="skills-subgroup">
                <span class="skills-subgroup-label">Mobile SDK &amp; UI Design</span>
                <div class="skills-chips-wrapper">
                  <span class="skill-badge"><i class="badge-dot"></i>Flutter SDK</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Dart Language</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Firebase Backend</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Figma Prototyping</span>
                </div>
              </div>
            </div>

            <div class="skills-highlights">
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Pengujian unit dan fitur terotomasi via Pest &amp; PHPUnit untuk zero-regression</span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Analisis statis level ketat Larastan dan format styling konsisten Laravel Pint</span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Aplikasi mobile multiplatform Flutter dengan backend Firebase &amp; desain sistem Figma</span>
              </div>
            </div>

            <div class="skills-card-footer">
              <span class="footer-indicator-dot"></span>
              <span class="footer-meta-text">Kualitas &amp; Mobile: Pest, Larastan, Flutter, Firebase, Figma</span>
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
              <span class="skills-badge-tag">Applied Data Mining</span>
            </div>

            <h3 class="skills-domain-title">Analitik Data &amp; Sistem</h3>
            <p class="skills-domain-desc">
              Penerapan data mining klasifikasi dan rekayasa proses bisnis Sistem Informasi untuk mentransformasikan data operasional menjadi keputusan strategis terukur.
            </p>

            <div class="skills-subgroups">
              <div class="skills-subgroup">
                <span class="skills-subgroup-label">Data Mining &amp; Pemodelan</span>
                <div class="skills-chips-wrapper">
                  <span class="skill-badge"><i class="badge-dot"></i>Algoritma C4.5</span>
                  <span class="skill-badge"><i class="badge-dot"></i>RapidMiner Studio</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Entropy &amp; Gain Ratio</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Confusion Matrix</span>
                </div>
              </div>

              <div class="skills-subgroup">
                <span class="skills-subgroup-label">Rekayasa Sistem Informasi (S1)</span>
                <div class="skills-chips-wrapper">
                  <span class="skill-badge"><i class="badge-dot"></i>S1 Sistem Informasi</span>
                  <span class="skill-badge"><i class="badge-dot"></i>DFD Level 0 &amp; 1 / ERD</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Data Cleansing Pipeline</span>
                  <span class="skill-badge"><i class="badge-dot"></i>Evaluasi Akurasi &amp; Presisi</span>
                </div>
              </div>
            </div>

            <div class="skills-highlights">
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Pemodelan prediksi ketepatan waktu kargo logistik (Studi kasus Big Cargo)</span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Pembersihan dataset riil: eliminasi missing value, reduksi atribut, &amp; diskretisasi</span>
              </div>
              <div class="skills-highlight-item">
                <svg class="skill-check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Dokumentasi siklus SDLC komprehensif dari analisis kebutuhan bisnis hingga testing</span>
              </div>
            </div>

            <div class="skills-card-footer">
              <span class="footer-indicator-dot"></span>
              <span class="footer-meta-text">Riset &amp; Akademik: S1 Sistem Informasi, C4.5, RapidMiner</span>
            </div>
          </div>
        </div>
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
                    <?php foreach (array_slice($techTags, 0, 4) as $tag): ?>
                      <span class="project-tag-micro"><?= e($tag) ?></span>
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

        <!-- ================================================================
             GITHUB ACTIVITY & CONTRIBUTION HEATMAP WIDGET
             ================================================================ -->
        <div class="github-activity-card">
          <div class="github-activity-header">
            <div class="github-activity-stat">
              <span class="github-contrib-number">1,157</span>
              <span class="github-contrib-caption">contributions in the last year</span>
            </div>
            <div class="github-year-pills" role="tablist" aria-label="Tahun Aktivitas GitHub">
              <button type="button" class="year-pill active">2026</button>
              <button type="button" class="year-pill">2025</button>
              <button type="button" class="year-pill">2024</button>
              <button type="button" class="year-pill">2023</button>
              <button type="button" class="year-pill">2022</button>
              <button type="button" class="year-pill">2021</button>
              <button type="button" class="year-pill">2020</button>
            </div>
          </div>

          <!-- Heatmap Canvas -->
          <div class="github-heatmap-wrapper">
            <div class="github-heatmap-grid">
              <div class="heatmap-months-row">
                <span>Sep</span><span>Oct</span><span>Nov</span><span>Dec</span><span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span><span>Jul</span><span>Aug</span><span>Sep</span>
              </div>
              <div class="heatmap-days-container">
                <div class="heatmap-day-labels">
                  <span>Mon</span>
                  <span>Wed</span>
                  <span>Fri</span>
                </div>
                <div class="heatmap-squares-grid">
                  <?php
                    $pattern = [
                      [2,3,1,0,0,0,0], [1,0,2,0,0,0,0], [0,1,0,3,2,0,0], [2,0,4,1,0,0,0],
                      [4,3,2,0,0,0,0], [1,2,0,0,0,0,0], [0,0,3,1,0,0,0], [2,1,0,0,0,0,0],
                      [0,0,0,0,1,0,0], [1,0,0,2,0,0,0], [0,0,0,0,0,0,0], [2,1,0,0,0,0,0],
                      [1,0,0,0,0,0,0], [0,2,1,0,0,0,0], [3,0,0,0,0,0,0], [0,0,0,0,0,0,0],
                      [0,0,4,2,0,0,0], [1,3,4,1,0,0,0], [3,4,2,0,0,0,0], [0,1,0,0,2,0,0],
                      [0,3,0,0,0,0,0], [2,1,0,0,0,0,0], [0,0,0,1,0,0,0], [0,2,0,0,0,0,0],
                      [1,0,1,0,0,0,0], [0,0,0,2,0,0,0], [2,1,2,0,0,0,0], [1,0,0,0,0,0,0],
                      [3,2,0,0,0,0,0], [1,0,0,1,0,0,0], [0,0,0,0,0,0,0], [0,0,0,0,0,0,0],
                      [1,0,2,0,0,0,0], [0,0,0,0,0,0,0], [0,1,0,0,0,0,0], [0,0,0,0,0,0,0],
                      [2,0,3,1,0,0,0], [1,2,0,0,0,0,0], [0,0,2,0,0,0,0], [0,3,1,0,0,0,0],
                      [2,0,0,0,0,0,0], [1,0,0,0,0,0,0], [0,0,3,2,0,0,0], [1,2,1,0,0,0,0],
                      [3,1,0,0,0,0,0], [2,3,4,2,0,0,0], [1,4,3,1,0,0,0], [4,3,4,2,0,0,0],
                      [3,2,4,4,1,0,0], [2,4,4,4,2,0,0], [4,3,2,4,3,0,0], [3,4,4,2,0,0,0]
                    ];
                    foreach ($pattern as $wIdx => $week) {
                      foreach ($week as $dIdx => $lvl) {
                        $cls = 'lvl-' . min(4, (int)$lvl);
                        $title = $lvl > 0 ? ($lvl * 3) . " contributions" : "No contributions";
                        echo '<div class="heatmap-sq ' . $cls . '" title="' . e($title) . '"></div>';
                      }
                    }
                  ?>
                </div>
              </div>
              <div class="heatmap-footer-row">
                <span>Learn how we count contributions</span>
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
            <a href="https://github.com/NexusDevWeb" target="_blank" rel="noopener noreferrer" class="github-org-chip">
              <span class="org-avatar-icon nexus">⚡</span>
              <span>@NexusDevWeb</span>
            </a>
            <a href="https://github.com/NexDigi-Dev" target="_blank" rel="noopener noreferrer" class="github-org-chip">
              <span class="org-avatar-icon nexdigi">⌘</span>
              <span>@NexDigi-Dev</span>
            </a>
            <a href="https://github.com/zeneriedev" target="_blank" rel="noopener noreferrer" class="github-org-chip">
              <span class="org-avatar-icon zenerie">✦</span>
              <span>@zeneriedev</span>
            </a>
          </div>

          <!-- Activity Overview -->
          <div class="github-activity-overview">
            <div class="activity-summary-col">
              <h4>Activity Overview</h4>
              <div class="activity-repo-list">
                <svg class="activity-book-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                  <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <div>
                  Contributed to 
                  <a href="https://github.com/NexusDevWeb/Ekosistem-NDC" target="_blank" rel="noopener noreferrer" class="activity-repo-link">NexusDevWeb/Ekosistem-NDC</a>, 
                  <a href="https://github.com/NexDigi-Dev/Ekosistem-NDC" target="_blank" rel="noopener noreferrer" class="activity-repo-link">NexDigi-Dev/Ekosistem-NDC</a>, 
                  <a href="https://github.com/NexusDevWeb/lms-assyafiiyah" target="_blank" rel="noopener noreferrer" class="activity-repo-link">NexusDevWeb/lms-assyafiiyah</a>, 
                  and <strong>32 other repositories</strong>.
                </div>
              </div>
            </div>

            <div class="activity-quad-col">
              <div class="activity-quad-chart" title="Activity Profile: 100% Commits">
                <span class="quad-label top">Code review</span>
                <span class="quad-label bottom">Pull requests</span>
                <span class="quad-label left">100% Commits</span>
                <span class="quad-label right">Issues</span>
                <div class="quad-axis-v"></div>
                <div class="quad-axis-h"></div>
                <div class="quad-data-dot" title="Active Commit Profile"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="github-repos-grid">
          
          <!-- Repo 1: Portfolio Bento Glassmorphism -->
          <a href="https://github.com/ammarsyrf/portofolio" target="_blank" rel="noopener noreferrer" class="github-repo-card glass-panel">
            <div class="repo-card-top">
              <div class="repo-name-group">
                <svg class="repo-book-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                  <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <h3 class="repo-name">portofolio</h3>
              </div>
              <span class="repo-badge-status">Public</span>
            </div>

            <p class="repo-description">
              Pure PHP 8.x + MySQL Bento Grid Portfolio with realtime visitor telemetry, geolocation analytics, and glassmorphism interface.
            </p>

            <div class="repo-meta-row">
              <div class="repo-lang">
                <span class="lang-dot php"></span>
                <span>PHP</span>
              </div>
              <div class="repo-stats-group">
                <span class="repo-stat-item" title="Stars"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg> 12</span>
                <span class="repo-stat-item" title="Forks"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><circle cx="18" cy="6" r="3"/><path d="M6 9v1a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V9"/><path d="M12 12v3"/></svg> 4</span>
              </div>
              <span class="repo-updated">Updated recently</span>
            </div>
          </a>

          <!-- Repo 2: LMS Assyafiiyah Academic Portal -->
          <a href="https://github.com/ammarsyrf/lms-assyafiiyah" target="_blank" rel="noopener noreferrer" class="github-repo-card glass-panel">
            <div class="repo-card-top">
              <div class="repo-name-group">
                <svg class="repo-book-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                  <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <h3 class="repo-name">lms-assyafiiyah</h3>
              </div>
              <span class="repo-badge-status highlight">Featured</span>
            </div>

            <p class="repo-description">
              Integrated School &amp; Academic Learning Management System with role-based access control (RBAC), attendance logs, and grade tracking.
            </p>

            <div class="repo-meta-row">
              <div class="repo-lang">
                <span class="lang-dot laravel"></span>
                <span>Laravel / PHP</span>
              </div>
              <div class="repo-stats-group">
                <span class="repo-stat-item" title="Stars"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg> 18</span>
                <span class="repo-stat-item" title="Forks"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><circle cx="18" cy="6" r="3"/><path d="M6 9v1a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V9"/><path d="M12 12v3"/></svg> 6</span>
              </div>
              <span class="repo-updated">Maintained</span>
            </div>
          </a>

          <!-- Repo 3: Villa Zein Booking Platform -->
          <a href="<?= !empty($profile['github']) ? e($profile['github']) : 'https://github.com/ammarsyrf' ?>" target="_blank" rel="noopener noreferrer" class="github-repo-card glass-panel">
            <div class="repo-card-top">
              <div class="repo-name-group">
                <svg class="repo-book-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                  <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <h3 class="repo-name">villa-zein-booking</h3>
              </div>
              <span class="repo-badge-status">Public</span>
            </div>

            <p class="repo-description">
              Fullstack Laravel booking management engine with room availability matrices, dynamic invoice generator, and multi-tier admin dashboard.
            </p>

            <div class="repo-meta-row">
              <div class="repo-lang">
                <span class="lang-dot laravel"></span>
                <span>Laravel / Blade</span>
              </div>
              <div class="repo-stats-group">
                <span class="repo-stat-item" title="Stars"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg> 15</span>
                <span class="repo-stat-item" title="Forks"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><circle cx="18" cy="6" r="3"/><path d="M6 9v1a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V9"/><path d="M12 12v3"/></svg> 5</span>
              </div>
              <span class="repo-updated">Stable</span>
            </div>
          </a>

          <!-- Repo 4: Cargo Decision Tree C4.5 Prediction -->
          <a href="<?= !empty($profile['github']) ? e($profile['github']) : 'https://github.com/ammarsyrf' ?>" target="_blank" rel="noopener noreferrer" class="github-repo-card glass-panel">
            <div class="repo-card-top">
              <div class="repo-name-group">
                <svg class="repo-book-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                  <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
                <h3 class="repo-name">cargo-c45-classification</h3>
              </div>
              <span class="repo-badge-status">Research</span>
            </div>

            <p class="repo-description">
              Decision Tree C4.5 algorithm implementation for logistics shipment timeliness classification &amp; accuracy evaluation (Studi Kasus Big Cargo).
            </p>

            <div class="repo-meta-row">
              <div class="repo-lang">
                <span class="lang-dot python"></span>
                <span>Python / Data Mining</span>
              </div>
              <div class="repo-stats-group">
                <span class="repo-stat-item" title="Stars"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg> 9</span>
                <span class="repo-stat-item" title="Forks"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><circle cx="18" cy="6" r="3"/><path d="M6 9v1a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V9"/><path d="M12 12v3"/></svg> 2</span>
              </div>
              <span class="repo-updated">Published</span>
            </div>
          </a>

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
        <a href="<?= BASE_URL ?>/admin/login" class="footer-admin-link">Portal Admin</a>
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
</html>; line-height: 1.7;">-</p>
        </div>
      </div>

      <div id="modal-actions" style="display: none; gap: 0.75rem; border-top: 1px solid var(--color-line); padding-top: 1.25rem;">
        <!-- Dinamis -->
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
