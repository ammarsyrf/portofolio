<?php
/**
 * Halaman Utama (Home) — Portfolio Ammar Syarif
 * Desain: Dark Dashboard / Glass Interface (DESIGN.md)
 * Navigasi: includes/nav.php | Command Palette: includes/command_palette.php
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Ambil data profil dari database
$profile = get_profile($pdo);

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
        'github' => 'https://github.com/zentokun90',
        'whatsapp' => '',
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
        $cvUrl = upload_url('cv', $profile['cv_file']);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($profile['full_name']) ?> — <?= e($profile['role_title']) ?></title>
  <meta name="description" content="<?= e($profile['tagline']) ?>">
  <meta name="author" content="<?= e($profile['full_name']) ?>">

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
                  <span class="bento-label">Pendidikan</span>
                  <div class="bento-big-stat">S1</div>
                  <span class="bento-sub-stat">Sistem Informasi</span>
                </div>
                <div class="bento-stats-right">
                  <div class="bento-meta-pill">
                    <span class="bento-pill-tag">Spesialisasi</span>
                    <span class="bento-pill-val">Web & Analitik Data</span>
                  </div>
                  <div class="bento-meta-pill">
                    <span class="bento-pill-tag">Ketersediaan</span>
                    <span class="bento-pill-val" style="color: #34D399;">● Siap Rekrutmen</span>
                  </div>
                  <div class="bento-progress-row">
                    <div class="bento-progress-track" title="Tingkat Kesiapan Teknis">
                      <div class="bento-progress-fill" style="width: 88%;"></div>
                    </div>
                    <a href="#about" class="bento-arrow-btn" title="Lihat Profil">➔</a>
                  </div>
                </div>
              </div>

              <!-- Card 2: Featured Project Mini-Player -->
              <div class="bento-card bento-card-player">
                <div class="bento-player-thumb-wrap">
                  <?php if (!empty($featuredImg)): ?>
                    <img src="<?= e($featuredImg) ?>" alt="<?= e($featuredProject['title'] ?? 'Proyek Unggulan') ?>" class="bento-player-thumb">
                  <?php else: ?>
                    <div style="width: 100%; height: 100%; background: var(--color-bg-surface); display: flex; align-items: center; justify-content: center; font-size: 0.65rem; color: var(--color-accent-bright);">
                      VZ
                    </div>
                  <?php endif; ?>
                </div>
                <div class="bento-player-info">
                  <div class="bento-player-header">
                    <span class="bento-player-title"><?= e($featuredProject['title'] ?? 'Villa Zein') ?> <span style="color: var(--color-accent);">♥</span></span>
                    <span class="bento-player-badge">★ Unggulan</span>
                  </div>
                  <div class="bento-player-subtitle">by <?= e($profile['full_name'] ?? 'Ammar Syarif') ?></div>
                  <div class="bento-player-controls">
                    <button type="button" class="player-ctrl-btn" id="btn-prev-proj" title="Proyek Sebelumnya">⏮</button>
                    <button type="button" class="player-ctrl-main btn-detail-trigger" data-id="<?= $featuredId ?>" title="Buka Detail Proyek">▶</button>
                    <button type="button" class="player-ctrl-btn" id="btn-next-proj" title="Proyek Berikutnya">⏭</button>
                  </div>
                  <div class="bento-player-timeline">
                    <div class="bento-player-track">
                      <div class="bento-player-thumb-point"></div>
                    </div>
                  </div>
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
                    <a href="<?= e($cvUrl) ?>" download class="btn-bento-pill primary" id="btn-download-cv">View Profile</a>
                  <?php else: ?>
                    <a href="#about" class="btn-bento-pill primary">View Profile</a>
                  <?php endif; ?>
                  <span class="bento-dots-menu" title="Menu Opsi">•••</span>
                </div>
              </div>

            </div>

            <!-- Right Column: Visual Anchor / Big Hero Typo & Portrait -->
            <div class="bento-hero-visual">
              
              <!-- Large Styled Background Typo -->
              <div class="bento-hero-typography">
                <span class="typo-line">let's</span>
                <span class="typo-line accent">code +</span>
                <span class="typo-line">systems</span>
                <div class="typo-pill-badge">ammar syarif</div>
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
                <span>01. My Profile ♡</span>
              </div>

              <!-- Floating Quick Action Buttons on right edge -->
              <div class="bento-floating-actions">
                <a href="#contact" class="bento-float-btn" title="Rekrut / Kontak">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                  </svg>
                </a>

                <?php if (!empty($profile['whatsapp'])): 
                  $waClean = preg_replace('/[^0-9]/', '', $profile['whatsapp']);
                ?>
                  <a href="https://wa.me/<?= e($waClean) ?>" target="_blank" rel="noopener noreferrer" class="bento-float-btn" title="WhatsApp">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                  </a>
                <?php else: ?>
                  <a href="#contact" class="bento-float-btn" title="Kirim Pesan">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                  </a>
                <?php endif; ?>
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
              <button type="button" class="dock-item" data-deck-index="4" role="tab" aria-selected="false" aria-controls="deck-panel-4" title="Kontak & Kanal Profesional">
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
                        <h4>About Me</h4>
                      </div>
                      <span class="bento-mini-status-pill">
                        <span class="pulse-dot-green"></span> S1 Sistem Informasi
                      </span>
                    </div>

                    <div class="bento-about-body">
                      <p class="bento-about-text">
                        Lulusan <strong>S1 Sistem Informasi</strong> dengan passion mendalam pada arsitektur web modern, rekayasa fullstack yang tangguh, serta pemodelan analitik data cerdas.
                      </p>
                      <p class="bento-about-text">
                        Berpengalaman merancang dan membangun sistem bisnis <em>end-to-end</em> dari nol: mulai dari perancangan database relasional, efisiensi server, hingga antarmuka siap pakai di tingkat produksi.
                      </p>
                    </div>

                    <div class="bento-about-metrics-strip">
                      <div class="about-metric-unit">
                        <span class="m-val">7+</span>
                        <span class="m-lbl">Sistem Nyata</span>
                      </div>
                      <div class="about-metric-sep"></div>
                      <div class="about-metric-unit">
                        <span class="m-val">100%</span>
                        <span class="m-lbl">Bebas SQLi</span>
                      </div>
                      <div class="about-metric-sep"></div>
                      <div class="about-metric-unit">
                        <span class="m-val">Fullstack</span>
                        <span class="m-lbl">&amp; Analitik</span>
                      </div>
                    </div>
                  </div>

                  <!-- Widget 2: Media Showcase (Vertical Capsules) -->
                  <div class="bento-card bento-card-media">
                    <div class="bento-media-header">
                      <div class="media-title-wrap">
                        <h4>Media</h4>
                        <span class="media-count-badge"><?= count($mediaProjects) ?> Karya</span>
                      </div>
                      <a href="#projects" class="bento-see-all">See All ➔</a>
                    </div>

                    <div class="bento-media-cards-row">
                      <?php foreach ($mediaProjects as $mp): 
                        $mpImg = !empty($mp['image']) && file_exists(UPLOAD_DIR_PROJECTS . '/' . basename($mp['image']))
                          ? upload_url('projects', $mp['image'])
                          : '';
                      ?>
                        <div class="bento-media-mini-card btn-detail-trigger" data-id="<?= (int)$mp['id'] ?>" role="button" tabindex="0" title="Buka detail <?= e($mp['title']) ?>">
                          <?php if (!empty($mpImg)): ?>
                            <img src="<?= e($mpImg) ?>" alt="<?= e($mp['title']) ?>" loading="lazy" />
                          <?php else: ?>
                            <div class="media-thumb-placeholder"><?= strtoupper(substr($mp['title'], 0, 2)) ?></div>
                          <?php endif; ?>
                          <div class="bento-media-overlay">
                            <span class="bento-media-title"><?= e($mp['title']) ?></span>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>

                  <!-- Widget 3: Categorized Tech Stack Widget (Fixed Height & Consistent Dimensions) -->
                  <div class="bento-card bento-card-skills-widget">
                    <div class="bento-skills-header">
                      <div class="skills-widget-heading">
                        <h4>Tech Stack</h4>
                        <span class="skills-widget-dot">● Active</span>
                      </div>
                      <div class="skills-cat-tabs" id="skillCatTabs" role="tablist">
                        <button type="button" class="cat-tab-btn active" data-cat="all">Semua</button>
                        <button type="button" class="cat-tab-btn" data-cat="frontend">Frontend</button>
                        <button type="button" class="cat-tab-btn" data-cat="backend">Backend</button>
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
                      <div class="skill-icon-chip" data-cat="backend" data-highlight="true" title="Laravel — Enterprise PHP Framework">
                        <div class="chip-svg-wrap laravel">
                          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5 12 2"></polygon>
                            <line x1="12" y1="22" x2="12" y2="12"></line>
                            <polyline points="22 8.5 12 12 2 8.5"></polyline>
                          </svg>
                        </div>
                        <div class="chip-detail">
                          <span class="chip-name">Laravel</span>
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
                      <span class="skills-footer-tag">✦ Teruji di Produksi &amp; Riset</span>
                      <a href="#skills" class="bento-see-all">Semua Skill ➔</a>
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
                     PANEL 4: KONTAK (Kanal Hubungi Langsung)
                     ============================================== -->
                <div class="bento-deck-panel" id="deck-panel-4" role="tabpanel" data-panel="4">
                  
                  <!-- Email -->
                  <div class="bento-card bento-card-deck deck-contact-card deck-contact-email-card">
                    <div class="deck-contact-heading">
                      <span class="deck-contact-icon">✉</span>
                      <div>
                        <span class="deck-eyebrow">Jalur formal</span>
                        <h4>Email Langsung</h4>
                      </div>
                    </div>
                    <p class="deck-contact-lead">
                      Terbuka untuk tawaran posisi Fullstack Developer, Data Analyst, atau proyek konsultasi sistem.
                    </p>
                    <div class="deck-contact-action-area">
                      <div class="deck-contact-pill-val"><span>Email utama</span><strong><?= e($profile['email']) ?></strong></div>
                      <a href="mailto:<?= e($profile['email']) ?>" class="deck-contact-action-btn">
                        <span>Kirim Email</span>
                      </a>
                    </div>
                  </div>

                  <!-- WhatsApp -->
                  <div class="bento-card bento-card-deck deck-contact-card deck-contact-whatsapp-card">
                    <div class="deck-contact-heading">
                      <span class="deck-contact-icon">◌</span>
                      <div>
                        <span class="deck-eyebrow">Diskusi cepat</span>
                        <h4>WhatsApp Chat</h4>
                      </div>
                    </div>
                    <p class="deck-contact-lead">
                      Hubungi langsung melalui pesan instan untuk diskusi cepat seputar kebutuhan pengembangan web Anda.
                    </p>
                    <div class="deck-contact-action-area">
                      <?php 
                        $waNum = !empty($profile['whatsapp']) ? preg_replace('/[^0-9]/', '', $profile['whatsapp']) : '';
                      ?>
                      <?php if ($waNum): ?>
                        <a href="https://wa.me/<?= e($waNum) ?>" target="_blank" rel="noopener noreferrer" class="deck-contact-action-btn">
                          <span>Chat via WhatsApp</span>
                        </a>
                      <?php else: ?>
                        <span class="deck-contact-unavailable">Nomor WhatsApp akan segera diperbarui.</span>
                      <?php endif; ?>
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
                      <a href="<?= BASE_URL ?>/pages/guestbook.php" class="deck-social-row">
                        <span>Buku Tamu</span><small>Tinggalkan pesan</small>
                      </a>
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
          <div class="section-caption">Tentang Saya</div>
          <h2 class="section-title">Membangun Sistem yang Andal dari Hulu ke Hilir</h2>
        </div>

        <div class="about-grid">
          <div class="about-text-panel glass-panel">
            <div class="about-panel-intro">
              <span class="about-kicker">Full-Stack Web Developer</span>
              <h3>Dari rancangan sistem hingga aplikasi siap digunakan.</h3>
            </div>
            <p>
              Saya adalah seorang Full-Stack Web Developer yang berfokus pada pengembangan aplikasi web menggunakan PHP, Laravel, MySQL, React, dan Next.js. Saya terbiasa mengembangkan website dan sistem berbasis web mulai dari perancangan database, pembuatan REST API, implementasi fitur backend dan frontend, hingga proses deployment ke server.
            </p>
            <p>
              Dalam pengembangan aplikasi, saya tidak hanya berfokus pada tampilan dan fungsi, tetapi juga memperhatikan struktur sistem, performa, keamanan, skalabilitas, dan kemudahan maintenance. Saya juga memiliki pengalaman dalam penggunaan Git, Linux, VPS, Nginx, Docker, Redis, queue, serta integrasi berbagai layanan dan API.
            </p>
            <p>
              Saya memiliki ketertarikan besar pada pengembangan sistem yang efisien, otomatis, dan dapat menyelesaikan kebutuhan nyata pengguna. Saat ini saya terus memperdalam kemampuan di bidang full-stack development, system architecture, dan server infrastructure dengan tujuan berkembang sebagai Web Developer profesional dan dapat berkontribusi pada berbagai project secara remote maupun kolaboratif.
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
         SECTION 3: SKILLS (Flat Badges ala satriabahari.my.id)
         ==================================================================== -->
    <section id="skills" class="section-padding">
      <div class="site-container">
        <div class="section-header">
          <div class="section-caption">Kompetensi & Toolkit</div>
          <h2 class="section-title">Keahlian Teknis & Domain Kerja</h2>
        <p class="section-lead" style="color: var(--color-text-dim); max-width: 680px; margin-top: 0.5rem; font-size: var(--text-sm); line-height: 1.6;">
          Kombinasi rekayasa backend modular, performa antarmuka bersih tanpa bloatware, serta pemodelan analitik data berbasis riset sistem informasi yang telah teruji pada berbagai aplikasi produksi nyata.
        </p>
      </div>

      <p class="skills-mobile-swipe-hint" aria-hidden="true">Geser kartu untuk melihat domain lainnya</p>
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
                <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
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
         SECTION 4: PROJECTS (Scroll Reveal Stagger + Accessible Modal)
         ==================================================================== -->
    <section id="projects" class="section-padding">
      <div class="site-container">
        <div class="section-header">
          <div class="section-caption">Koleksi Proyek</div>
          <h2 class="section-title">Hasil Karya & Sistem Bisnis</h2>
        </div>

        <p class="projects-mobile-swipe-hint" aria-hidden="true">Geser kartu untuk melihat proyek lainnya</p>
        <div class="projects-grid">
          <?php if (empty($projects)): ?>
            <p style="color: var(--color-text-dim);">Belum ada proyek yang dipublikasikan.</p>
          <?php else: ?>
            <?php foreach ($projects as $proj): 
              $hasImg = !empty($proj['image']) && file_exists(UPLOAD_DIR_PROJECTS . '/' . basename($proj['image']));
              $techTags = array_filter(array_map('trim', explode(',', (string)$proj['tech_stack'])));
            ?>
              <article class="glass-panel project-card reveal-card" data-id="<?= (int)$proj['id'] ?>" tabindex="0" role="button" aria-label="Buka rincian proyek <?= e($proj['title']) ?>">
                
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
                </div>

                <div class="project-card-content">
                  <div class="project-top-meta">
                    <span class="project-category-tag"><?= e($proj['category'] ?: 'Web Application') ?></span>
                    <span class="project-role-caption"><?= e($proj['my_role'] ?: 'Developer') ?></span>
                  </div>

                  <h3 class="project-card-title"><?= e($proj['title']) ?></h3>
                  <p class="project-card-summary"><?= e($proj['summary']) ?></p>

                  <div class="project-stack-tags">
                    <?php foreach (array_slice($techTags, 0, 3) as $tag): ?>
                      <span class="project-tag-micro"><?= e($tag) ?></span>
                    <?php endforeach; ?>
                  </div>

                  <div class="project-card-footer">
                    <button type="button" class="btn-open-detail btn-detail-trigger" data-id="<?= (int)$proj['id'] ?>">
                      Rincian Proyek
                    </button>
                    <?php if (!empty($proj['demo_link'])): ?>
                      <a href="<?= e($proj['demo_link']) ?>" target="_blank" rel="noopener noreferrer" class="btn-open-detail" style="color: var(--color-text-dim);">
                        Demo
                      </a>
                    <?php endif; ?>
                  </div>
                </div>

              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <hr class="section-divider" />

    <!-- ====================================================================
         SECTION 5: CONTACT (Glass Card)
         ==================================================================== -->
    <section id="contact" class="section-padding">
      <div class="site-container">
        <div class="section-header">
          <div class="section-caption">Jalur Komunikasi</div>
          <h2 class="section-title">Mari Membangun Solusi Bersama</h2>
        </div>

        <div class="contact-glass-box glass-panel-glow">
          <h3 style="font-family: var(--font-display); font-size: clamp(1.4rem, 3vw, 2rem); font-weight: 700; color: var(--color-text); margin-bottom: 0.75rem;">
            Terbuka untuk diskusi proyek dan rekrutmen profesional.
          </h3>
          <p style="color: var(--color-text-dim); max-width: var(--max-text-width); line-height: 1.7;">
            Silakan hubungi saya melalui jalur resmi di bawah ini untuk kolaborasi pengembangan platform web atau peninjauan kualifikasi kandidat.
          </p>

          <div class="contact-channels-grid">
            <?php if (!empty($profile['email'])): ?>
              <a href="mailto:<?= e($profile['email']) ?>" class="contact-card-item">
                <span class="contact-channel-label">Email Resmi</span>
                <span class="contact-channel-val"><?= e($profile['email']) ?></span>
              </a>
            <?php endif; ?>

            <?php if (!empty($profile['whatsapp'])): 
              $waClean = preg_replace('/[^0-9]/', '', $profile['whatsapp']);
            ?>
              <a href="https://wa.me/<?= e($waClean) ?>" target="_blank" rel="noopener noreferrer" class="contact-card-item">
                <span class="contact-channel-label">WhatsApp Langsung</span>
                <span class="contact-channel-val"><?= e($profile['whatsapp']) ?></span>
              </a>
            <?php endif; ?>

            <?php if (!empty($profile['linkedin'])): ?>
              <a href="<?= e($profile['linkedin']) ?>" target="_blank" rel="noopener noreferrer" class="contact-card-item">
                <span class="contact-channel-label">Profil LinkedIn</span>
                <span class="contact-channel-val">linkedin.com/in/ammar</span>
              </a>
            <?php endif; ?>

            <?php if (!empty($profile['github'])): ?>
              <a href="<?= e($profile['github']) ?>" target="_blank" rel="noopener noreferrer" class="contact-card-item">
                <span class="contact-channel-label">Repositori GitHub</span>
                <span class="contact-channel-val">github.com/ammar</span>
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Site Footer -->
  <footer class="site-footer">
    <div class="site-container footer-inner">
      <div>
        &copy; <?= date('Y') ?> <?= e($profile['full_name']) ?>. Dark Glassmorphism Portfolio.
      </div>
      <div style="display: flex; flex-wrap: wrap; gap: 1rem 1.5rem; align-items: center; justify-content: center;">
        <a href="<?= BASE_URL ?>/pages/links.php" class="footer-admin-link">Semua Tautan</a>
        <a href="<?= BASE_URL ?>/pages/guestbook.php" class="footer-admin-link">Buku Tamu</a>
        <a href="<?= BASE_URL ?>/admin/login.php" class="footer-admin-link">Portal Admin</a>
      </div>
    </div>
  </footer>

  <!-- Modal Dialog Rincian Proyek -->
  <div id="project-modal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="modal-dialog">
      <button type="button" class="modal-close-btn" id="modal-close" aria-label="Tutup jendela rincian">
        &times;
      </button>

      <span id="modal-cat" class="project-category-tag" style="margin-bottom: 0.75rem; display: inline-block;">Kategori</span>
      <h3 class="modal-title" id="modal-title" style="font-family: var(--font-display); font-size: var(--text-2xl); font-weight: 700; color: var(--color-text); margin-bottom: 1.25rem;">Judul Proyek</h3>

      <div id="modal-img-wrap" style="width: 100%; max-height: 320px; border-radius: 8px; overflow: hidden; margin-bottom: 1.5rem; display: none; border: 1px solid var(--color-line);">
        <img src="" alt="" id="modal-img" style="width: 100%; height: 100%; object-fit: cover;">
      </div>

      <div style="display: flex; flex-direction: column; gap: 1.25rem; margin-bottom: 1.75rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; background-color: var(--color-bg-alt); padding: 1rem; border-radius: 8px; border: 1px solid var(--color-line);">
          <div>
            <div style="font-size: var(--text-xs); color: var(--color-accent-bright); font-weight: 600;">Peran Pengembang</div>
            <div id="modal-role" style="font-size: var(--text-sm); color: var(--color-text);">-</div>
          </div>
          <div>
            <div style="font-size: var(--text-xs); color: var(--color-accent-bright); font-weight: 600;">Teknologi</div>
            <div id="modal-stack" style="font-size: var(--text-sm); color: var(--color-text);">-</div>
          </div>
        </div>

        <div>
          <h4 style="font-family: var(--font-display); font-size: var(--text-sm); color: var(--color-text); margin-bottom: 0.4rem; font-weight: 600;">Deskripsi</h4>
          <p id="modal-desc" style="font-size: var(--text-sm); color: var(--color-text-dim); line-height: 1.7;">-</p>
        </div>

        <div id="modal-impact-box" style="display: none;">
          <h4 style="font-family: var(--font-display); font-size: var(--text-sm); color: var(--color-text); margin-bottom: 0.4rem; font-weight: 600;">Dampak & Hasil</h4>
          <p id="modal-impact" style="font-size: var(--text-sm); color: var(--color-text-dim); line-height: 1.7;">-</p>
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
