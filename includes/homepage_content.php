<?php
/**
 * Salinan statis & dinamis halaman utama. Disimpan sebagai satu dokumen JSON di
 * site_settings agar dapat dikelola dari admin tanpa mengubah source code.
 */
function homepage_content_defaults(): array
{
    return [
        // Navigation & Brand
        'nav_brand_full' => 'Ammar Syarif',
        'nav_brand_short' => 'A//S',
        'nav_hire_text' => 'Rekrut Saya',

        // Hero Section
        'hero_education_label' => 'Pendidikan',
        'hero_degree' => 'S1',
        'hero_field' => 'Sistem Informasi',
        'hero_specialty_label' => 'Spesialisasi',
        'hero_specialty_value' => 'Web Developer',
        'hero_availability_label' => 'Ketersediaan',
        'hero_availability_value' => '● Siap Rekrutmen & Freelance',
        'hero_readiness' => '88',
        'hero_line_one' => "let's",
        'hero_line_two' => 'code +',
        'hero_line_three' => 'systems',
        'hero_badge' => 'ammar syarif',
        'hero_vertical_badge' => '01. MY PROFILE ♡',

        // Hero Terminal Card
        'terminal_title' => 'ammar@zen:~$',
        'terminal_status_badge' => 'ACTIVE',
        'terminal_user' => 'zen@dev',
        'terminal_status_val' => '● Available for Hire',
        'terminal_role_val' => 'Fullstack & AI-Augmented Dev',
        'terminal_focus_val' => 'Laravel 13 • MySQL • Next.js',
        'terminal_ai_val' => 'Claude • Gemini • OpenAI • Prompting',
        'terminal_workflow_val' => 'Agentic Acceleration (3x Speed)',
        'terminal_location_val' => 'Jakarta, ID (GMT+7)',
        'terminal_cmd_test' => 'pest test && eval-prompt',
        'terminal_cmd_pass' => 'Pest v3 • 18 passed (0.04s)',
        'terminal_summary_line1' => '✓ 100% Bebas SQLi & Siap Produksi',
        'terminal_summary_line2' => '✓ Prompt Token Efficiency: 98.4% (Optimal Reasoning)',

        // Brand Zenerie
        'brand_title' => 'Our Brand',
        'brand_badge' => 'Zenerie',
        'brand_description' => 'Zenerie adalah brand kreatif yang kami rintis untuk menghadirkan identitas dan pengalaman digital yang berkesan.',
        'brand_url' => 'https://www.zenerie.my.id',
        'brand_button' => 'Kunjungi Zenerie',
        'identity_button' => 'View Profile',
        'media_title' => 'Media',
        'media_button' => 'See All ➔',

        // Tech Stack Bento Widget
        'tech_widget_title' => 'Tech Stack',
        'tech_widget_status' => '● Active',
        'tech_widget_footer' => '✦ Teruji di Produksi & Riset',
        'tech_widget_button' => 'Semua Skill ➔',
        'tech_chips_json' => json_encode([
            // Frontend
            ['name' => 'Next.js', 'category' => 'frontend', 'role' => 'React SSR', 'highlight' => true, 'icon' => 'nextjs'],
            ['name' => 'React', 'category' => 'frontend', 'role' => 'UI Library', 'highlight' => true, 'icon' => 'react'],
            ['name' => 'TypeScript', 'category' => 'frontend', 'role' => 'Type-Safe JS', 'highlight' => false, 'icon' => 'ts'],
            ['name' => 'Tailwind CSS', 'category' => 'frontend', 'role' => 'Utility CSS', 'highlight' => false, 'icon' => 'tailwind'],
            ['name' => 'shadcn/ui', 'category' => 'frontend', 'role' => 'UI Component', 'highlight' => false, 'icon' => 'shadcn'],
            ['name' => 'Bootstrap', 'category' => 'frontend', 'role' => 'Grid & UI', 'highlight' => false, 'icon' => 'bootstrap'],
            ['name' => 'JavaScript', 'category' => 'frontend', 'role' => 'ES6+ Logic', 'highlight' => false, 'icon' => 'js'],
            ['name' => 'HTML5 / CSS3', 'category' => 'frontend', 'role' => 'Semantic UI', 'highlight' => false, 'icon' => 'html'],
            // Backend
            ['name' => 'Laravel 13', 'category' => 'backend', 'role' => 'Framework', 'highlight' => true, 'icon' => 'laravel'],
            ['name' => 'PHP 8.x', 'category' => 'backend', 'role' => 'Backend Core', 'highlight' => false, 'icon' => 'php'],
            ['name' => 'Blade Engine', 'category' => 'backend', 'role' => 'Templating', 'highlight' => false, 'icon' => 'blade'],
            ['name' => 'Inertia.js', 'category' => 'backend', 'role' => 'Monolith SPA', 'highlight' => false, 'icon' => 'inertia'],
            ['name' => 'REST API', 'category' => 'backend', 'role' => 'JSON Endpoints', 'highlight' => false, 'icon' => 'api'],
            ['name' => 'Python', 'category' => 'backend', 'role' => 'Scripting & ML', 'highlight' => false, 'icon' => 'python'],
            // AI
            ['name' => 'Prompt Eng.', 'category' => 'ai', 'role' => 'Context & Logic', 'highlight' => false, 'icon' => 'prompteng'],
            ['name' => 'Claude & Gemini', 'category' => 'ai', 'role' => 'Architecture AI', 'highlight' => false, 'icon' => 'claude'],
            ['name' => 'OpenAI / LLMs', 'category' => 'ai', 'role' => 'Reasoning & APIs', 'highlight' => false, 'icon' => 'openai'],
            ['name' => 'Agentic Dev', 'category' => 'ai', 'role' => 'Cursor & Tools', 'highlight' => false, 'icon' => 'cursor'],
            // Database
            ['name' => 'MySQL ⭐', 'category' => 'database', 'role' => 'Utama / RDBMS', 'highlight' => true, 'icon' => 'mysql'],
            ['name' => 'MariaDB', 'category' => 'database', 'role' => 'Relasional', 'highlight' => false, 'icon' => 'mariadb'],
            ['name' => 'PostgreSQL', 'category' => 'database', 'role' => 'Advanced SQL', 'highlight' => false, 'icon' => 'postgres'],
            ['name' => 'Redis', 'category' => 'database', 'role' => 'In-Memory Cache', 'highlight' => false, 'icon' => 'redis'],
            // DevOps
            ['name' => 'Docker', 'category' => 'devops', 'role' => 'Containers', 'highlight' => true, 'icon' => 'docker'],
            ['name' => 'Git / GitHub', 'category' => 'devops', 'role' => 'Version Control', 'highlight' => false, 'icon' => 'git'],
            ['name' => 'Linux / Ubuntu', 'category' => 'devops', 'role' => 'Server OS', 'highlight' => false, 'icon' => 'linux'],
            ['name' => 'Apache & Nginx', 'category' => 'devops', 'role' => 'Web Server', 'highlight' => false, 'icon' => 'nginx'],
            // Testing
            ['name' => 'Pest PHP', 'category' => 'testing', 'role' => 'Modern Testing', 'highlight' => false, 'icon' => 'pest'],
            ['name' => 'PHPUnit', 'category' => 'testing', 'role' => 'Unit Testing', 'highlight' => false, 'icon' => 'phpunit'],
            ['name' => 'Larastan', 'category' => 'testing', 'role' => 'Static Analysis', 'highlight' => false, 'icon' => 'larastan'],
            // Mobile
            ['name' => 'Flutter', 'category' => 'mobile', 'role' => 'Mobile SDK', 'highlight' => false, 'icon' => 'flutter'],
            ['name' => 'Firebase', 'category' => 'mobile', 'role' => 'Cloud Backend', 'highlight' => false, 'icon' => 'firebase'],
            ['name' => 'Figma', 'category' => 'mobile', 'role' => 'UI Prototype', 'highlight' => false, 'icon' => 'figma'],
            // Analytics
            ['name' => 'Data Mining', 'category' => 'analytics', 'role' => 'Klasifikasi', 'highlight' => false, 'icon' => 'datamining'],
            ['name' => 'Algoritma C4.5', 'category' => 'analytics', 'role' => 'Decision Tree', 'highlight' => false, 'icon' => 'c45'],
            ['name' => 'RapidMiner', 'category' => 'analytics', 'role' => 'Data Analytics', 'highlight' => false, 'icon' => 'rapidminer']
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),

        // About (Bento Card)
        'about_widget_title' => 'About Me',
        'about_widget_status' => 'S1 Sistem Informasi',
        'about_widget_text_one' => 'Mahasiswa semester 6 S1 Sistem Informasi yang memiliki ketertarikan kuat pada pengembangan arsitektur web modern, rekayasa sistem fullstack.',
        'about_widget_text_two' => 'Berpengalaman merancang dan membangun sistem bisnis end-to-end dari nol: mulai dari perancangan database relasional, efisiensi server, hingga antarmuka siap pakai di tingkat produksi.',
        'about_metric_one_value' => '7+',
        'about_metric_one_label' => 'Sistem Nyata',
        'about_metric_two_value' => '100%',
        'about_metric_two_label' => 'Bebas SQLi',
        'about_metric_three_value' => 'Fullstack',
        'about_metric_three_label' => '& Analitik',

        // Deck Panel: Cara Saya Bekerja & Standar Rekayasa
        'deck_vision_eyebrow' => 'Cara saya bekerja',
        'deck_vision_title' => 'Sistem yang siap dipakai, bukan sekadar selesai.',
        'deck_vision_lead' => 'Saya menerjemahkan kebutuhan bisnis menjadi alur yang jelas, data yang rapi, dan antarmuka yang nyaman digunakan.',
        'deck_s1_title' => 'Pahami alur bisnis',
        'deck_s1_desc' => 'Mulai dari masalah pengguna dan proses yang ingin dipermudah.',
        'deck_s2_title' => 'Bangun fondasi yang rapi',
        'deck_s2_desc' => 'Struktur data, backend, dan UI dirancang agar mudah dikembangkan.',
        'deck_s3_title' => 'Validasi sampai siap pakai',
        'deck_s3_desc' => 'Fokus pada detail yang membuat sistem stabil untuk operasional harian.',
        
        'deck_std_title' => 'Standar Rekayasa',
        'deck_std1_icon' => '🛡️',
        'deck_std1_title' => 'Aman dari Fondasi',
        'deck_std1_desc' => 'Validasi input, prepared statement, dan akses data yang terjaga.',
        'deck_std2_icon' => '⚡',
        'deck_std2_title' => 'Terstruktur & Ringan',
        'deck_std2_desc' => 'Struktur data jelas dan antarmuka yang fokus pada kebutuhan pengguna.',
        'deck_std3_icon' => '🎯',
        'deck_std3_title' => 'Berorientasi Dampak',
        'deck_std3_desc' => 'Data dan metrik dipakai untuk membantu keputusan yang lebih tepat.',

        'deck_collab_title' => 'Siap Berkolaborasi',
        'deck_collab_role_lbl' => 'Fokus peran',
        'deck_collab_role_val' => 'Web Developer & Data Analyst',
        'deck_collab_avail_lbl' => 'Ketersediaan',
        'deck_collab_avail_val' => '● Terbuka untuk peluang kerja',
        'deck_collab_mode_lbl' => 'Cara kerja',
        'deck_collab_mode_val' => 'Onsite atau remote (Indonesia)',
        'deck_collab_btn_text' => 'Hubungi Ammar',

        // About Section Detail
        'about_caption' => 'Tentang Saya',
        'about_title' => 'Membangun Sistem yang Andal dari Hulu ke Hilir',
        'about_kicker' => 'Full-Stack Web Developer',
        'about_heading' => 'Dari rancangan sistem hingga aplikasi siap digunakan.',
        'about_paragraph_one' => 'Saya adalah seorang Full-Stack Web Developer yang berfokus pada pengembangan aplikasi web menggunakan PHP, Laravel, MySQL, React, dan Next.js. Saya terbiasa mengembangkan website dan sistem berbasis web mulai dari perancangan database, pembuatan REST API, implementasi fitur backend dan frontend, hingga proses deployment ke server.',
        'about_paragraph_two' => 'Dalam pengembangan aplikasi, saya tidak hanya berfokus pada tampilan dan fungsi, tetapi juga memperhatikan struktur sistem, performa, keamanan, skalabilitas, dan kemudahan maintenance. Saya juga memiliki pengalaman dalam penggunaan Git, Linux, VPS, Nginx, Docker, Redis, queue, serta integrasi berbagai layanan dan API.',
        'about_paragraph_three' => 'Saya memiliki ketertarikan besar pada pengembangan sistem yang efisien, otomatis, dan dapat menyelesaikan kebutuhan nyata pengguna. Saat ini saya terus memperdalam kemampuan di bidang full-stack development, system architecture, dan server infrastructure dengan tujuan berkembang sebagai Web Developer profesional dan dapat berkontribusi pada berbagai project secara remote maupun kolaboratif.',

        // Skills Section Header
        'skills_caption' => 'Kompetensi & Toolkit',
        'skills_title' => 'Keahlian Teknis & Domain Kerja',
        'skills_lead' => 'Kombinasi rekayasa backend modular, performa antarmuka bersih tanpa bloatware, serta pemodelan analitik data berbasis riset sistem informasi yang telah teruji pada berbagai aplikasi produksi nyata.',

        // Skills Matriks Tingkat Penguasaan
        'matrix_kicker' => '⚡ Tingkat Kenyamanan & Kesiapan Produksi',
        'matrix_title' => 'Matriks Penguasaan Teknologi',
        'matrix_desc' => 'Pemetaan keahlian teknis berdasarkan intensitas penggunaan riil dalam membangun aplikasi siap produksi.',
        
        'tier1_badge' => 'Advanced / Production-Ready',
        'tier1_level' => 'Kenyamanan Penuh • 95%',
        'tier1_title' => 'Fondasi Inti & Arsitektur Sistem',
        'tier1_desc' => 'Sangat nyaman merancang sistem dari nol: database relasional 3NF, backend MVC terstruktur, API aman bebas SQLi, dan logic kompleks.',
        'tier1_percent' => '95',
        'tier1_chips' => '⚡ PHP Native, 🔴 Laravel, 🐬 MySQL, 🔗 REST API, 🟡 JavaScript',

        'tier2_badge' => 'Proficient',
        'tier2_level' => 'Penggunaan Harian • 85%',
        'tier2_title' => 'Web Modern & Infrastruktur Harian',
        'tier2_desc' => 'Terbiasa digunakan dalam alur kerja harian untuk membuat antarmuka responsif cepat, version control, dan konfigurasi web server VPS.',
        'tier2_percent' => '85',
        'tier2_chips' => '⚛️ React, ▲ Next.js, 🌿 Git / GitHub, 🐧 Linux VPS, 🎨 Tailwind CSS, 🟢 Nginx',

        'tier3_badge' => 'Familiar / Exploring',
        'tier3_level' => 'Riset & Utility • 75%',
        'tier3_title' => 'Container, Skrip & Lapisan Caching',
        'tier3_desc' => 'Pemahaman arsitektur solid untuk isolasi container, skrip data mining C4.5 Python, akselerasi caching in-memory, dan perlindungan CDN.',
        'tier3_percent' => '75',
        'tier3_chips' => '🐳 Docker, 🐍 Python, ⚡ Redis, ☁️ Cloudflare',

        // 6 Kartu Domain Keahlian
        // Domain 1: Frontend
        'domain1_badge' => 'High-Fidelity UI',
        'domain1_title' => 'Frontend Engineering',
        'domain1_desc' => 'Antarmuka modern, interaktif, dan ultra-responsif dengan performa rendering tinggi, arsitektur komponen re-usable, dan desain sistem presisi.',
        'domain1_grp1_title' => 'Frameworks & Modern UI',
        'domain1_grp1_tags' => 'React, Next.js (App Router), shadcn/ui, Tailwind CSS, Bootstrap',
        'domain1_grp2_title' => 'Core Scripting & Standards',
        'domain1_grp2_tags' => 'TypeScript, JavaScript (ES6+), HTML5 Semantik, CSS3 Glassmorphism',
        'domain1_b1' => 'SSR, SSG, dan Client-Side Rendering teroptimasi untuk kecepatan First Contentful Paint',
        'domain1_b2' => 'Desain sistem berbasis utility class Tailwind CSS & atomic primitive shadcn/ui',
        'domain1_b3' => 'Interaktivitas 60 FPS dengan micro-interactions, responsive grid, dan transisi fluid',
        'domain1_eco' => 'Eksosistem: Next.js, React, TypeScript, Tailwind, shadcn/ui',

        // Domain 2: Backend
        'domain2_badge' => 'Core Architecture',
        'domain2_title' => 'Backend & Architecture',
        'domain2_desc' => 'Logika bisnis server-side yang tangguh, modular, dan scalable dengan fokus mutlak pada integritas transaksi, asynchronous processing, dan keamanan API.',
        'domain2_grp1_title' => 'Framework & Server Logic',
        'domain2_grp1_tags' => 'Laravel Framework, PHP 8.x Core, Blade Engine, Inertia.js (SPA)',
        'domain2_grp2_title' => 'Asynchronous, API & Services',
        'domain2_grp2_tags' => 'RESTful API (JSON), Webhook Listeners, Queue / Jobs Worker, Scheduler (Cron)',
        'domain2_b1' => 'Arsitektur MVC & Service Repository pattern untuk clean code dan kemudahan perawatan',
        'domain2_b2' => 'Pemrosesan antrean background via Queue/Jobs dan otomasi cron job berkala',
        'domain2_b3' => 'Integrasi webhook aman, autentikasi multi-tier (RBAC), dan session management',
        'domain2_eco' => 'Eksosistem: Laravel, PHP 8.x, Queue/Jobs, Webhook, REST API',

        // Domain 3: Database
        'domain3_badge' => 'Data Persistence',
        'domain3_title' => 'Database & Storage',
        'domain3_desc' => 'Pengelolaan penyimpanan data relasional dan in-memory yang teroptimasi, menjamin kehandalan transaksi (ACID), integritas referensial, dan caching latency rendah.',
        'domain3_grp1_title' => 'Relational Database Engines',
        'domain3_grp1_tags' => 'MySQL ⭐ (Utama), MariaDB, PostgreSQL',
        'domain3_grp2_title' => 'In-Memory & Data Integrity',
        'domain3_grp2_tags' => 'Redis (In-Memory Cache), PDO Prepared Stmt, Query Indexing & ACID, Relational ERD (3NF)',
        'domain3_b1' => 'Perancangan skema relasional 3NF terstruktur dengan composite indexing untuk query cepat',
        'domain3_b2' => 'Proteksi penuh injeksi SQL via PDO parameter binding & sanitasi input ketat',
        'domain3_b3' => 'Redis in-memory key-value caching untuk throttling, session, dan akselerasi data realtime',
        'domain3_eco' => 'Engine Utama: MySQL ⭐, PostgreSQL, MariaDB, Redis',

        // Domain 4: DevOps
        'domain4_badge' => 'Deployment & Infra',
        'domain4_title' => 'DevOps & Server Infra',
        'domain4_desc' => 'Infrastruktur deployment handal dari isolasi container hingga konfigurasi web server produksi, keamanan jaringan CDN, dan otomatisasi process daemon.',
        'domain4_grp1_title' => 'Container & Version Control',
        'domain4_grp1_tags' => 'Docker Containers, Git / GitHub Flow, Composer Package Mgr',
        'domain4_grp2_title' => 'Server OS, Web Server & Cloud',
        'domain4_grp2_tags' => 'Linux / Ubuntu Server, Apache & Nginx, Cloudflare CDN / SSL, Supervisor Daemon, SSH & VPS / Hosting',
        'domain4_b1' => 'Environment isolasi via Docker container dan konfigurasi VPS Linux mandiri',
        'domain4_b2' => 'Reverse proxy Nginx/Apache, SSL otomatis, dan proteksi DNS CDN Cloudflare',
        'domain4_b3' => 'Supervisor process manager untuk monitoring berkelanjutan Queue Workers & Scheduler',
        'domain4_eco' => 'Infrastruktur: Docker, Linux VPS, Nginx, Cloudflare, Git',

        // Domain 5: Quality & Mobile
        'domain5_badge' => 'Quality & Mobile',
        'domain5_title' => 'Testing, QA & Mobile',
        'domain5_desc' => 'Penjaminan mutu kode dengan automated testing ketat, standardisasi kode modern, serta pengembangan aplikasi mobile multiplatform yang terintegrasi cloud.',
        'domain5_grp1_title' => 'Automated Testing & Standards',
        'domain5_grp1_tags' => 'Pest PHP Testing, PHPUnit Suite, PHPStan / Larastan, Laravel Pint',
        'domain5_grp2_title' => 'Mobile SDK & UI Design',
        'domain5_grp2_tags' => 'Flutter SDK, Dart Language, Firebase Backend, Figma Prototyping',
        'domain5_b1' => 'Pengujian unit dan fitur terotomasi via Pest & PHPUnit untuk zero-regression',
        'domain5_b2' => 'Analisis statis level ketat Larastan dan format styling konsisten Laravel Pint',
        'domain5_b3' => 'Aplikasi mobile multiplatform Flutter dengan backend Firebase & desain sistem Figma',
        'domain5_eco' => 'Kualitas & Mobile: Pest, Larastan, Flutter, Firebase, Figma',

        // Domain 6: Analytics
        'domain6_badge' => 'Applied Data Mining',
        'domain6_title' => 'Analitik Data & Sistem',
        'domain6_desc' => 'Penerapan data mining klasifikasi dan rekayasa proses bisnis Sistem Informasi untuk mentransformasikan data operasional menjadi keputusan strategis terukur.',
        'domain6_grp1_title' => 'Data Mining & Pemodelan',
        'domain6_grp1_tags' => 'Algoritma C4.5, RapidMiner Studio, Entropy & Gain Ratio, Confusion Matrix',
        'domain6_grp2_title' => 'Rekayasa Sistem Informasi (S1)',
        'domain6_grp2_tags' => 'S1 Sistem Informasi, DFD Level 0 & 1 / ERD, Data Cleansing Pipeline, Evaluasi Akurasi & Presisi',
        'domain6_b1' => 'Pemodelan prediksi ketepatan waktu kargo logistik (Studi kasus Big Cargo)',
        'domain6_b2' => 'Pembersihan dataset riil: eliminasi missing value, reduksi atribut, & diskretisasi',
        'domain6_b3' => 'Dokumentasi siklus SDLC komprehensif dari analisis kebutuhan bisnis hingga testing',
        'domain6_eco' => 'Riset & Akademik: S1 Sistem Informasi, C4.5, RapidMiner',

        // Projects Section
        'projects_caption' => 'Koleksi Proyek',
        'projects_title' => 'Hasil Karya & Sistem Bisnis',

        // Gear & Workspace Setup
        'gear_hw_laptop_name' => 'AMD Ryzen™ / Intel Core i7',
        'gear_hw_laptop_desc' => '32GB RAM DDR5 • 1TB NVMe PCIe 4.0',
        'gear_hw_display_name' => '27" QHD 165Hz IPS + 24" Portrait',
        'gear_hw_display_desc' => 'Optimal koding vertikal & terminal preview',
        'gear_hw_keyboard_name' => 'Custom 75% Mechanical Keyboard',
        'gear_hw_keyboard_desc' => 'Lubed Linear Switches • Wireless Precision Mouse',
        'gear_hw_audio_name' => 'Studio Monitor Headphones / IEM',
        'gear_hw_audio_desc' => 'Acoustic clarity & passive noise isolation',
        'gear_sw_os_tags' => 'Windows 11 Pro, WSL2 (Ubuntu 24.04), Windows Terminal, PowerShell 7, Starship Prompt, Git CLI',
        'gear_sw_ide_tags' => 'Antigravity IDE, Cursor AI, VS Code, JetBrains Mono',
        'gear_sw_tools_tags' => 'PHP 8.3+, Laravel 13, Node.js 20, MySQL / PostgreSQL, TablePlus, Postman, Docker',
        'gear_ai_models_desc' => 'Claude 3.7 Sonnet (Thinking Mode), OpenAI GPT-4o, Google Gemini 2.5 Pro.',
        'gear_ai_prompt_desc' => 'System prompts, multi-agent chaining, context grounding & function calling.',
        'gear_ai_design_desc' => 'Figma (Design System & UI prototype), Obsidian (Markdown PKM), Notion.',
        'gear_ai_devops_desc' => 'SSH Key-based deployment, SCP automation, Nginx, GitKraken.',

        // Contact Section
        'contact_caption' => 'Jalur Komunikasi',
        'contact_title' => 'Mari Membangun Solusi Bersama',
        'contact_email_title' => 'Mulai percakapan profesional',
        'contact_email_description' => 'Untuk kolaborasi, rekrutmen, dan diskusi proyek',
        'contact_linkedin_title' => 'Terhubung di LinkedIn',
        'contact_linkedin_description' => 'Lihat profil dan pengalaman',
        'contact_github_title' => 'Jelajahi GitHub',
        'contact_github_description' => 'Repository dan eksperimen',
        'contact_instagram_title' => 'Ikuti di Instagram',
        'contact_instagram_description' => 'Update karya & aktivitas terbaru',
        'contact_tiktok_title' => 'Tonton di TikTok',
        'contact_tiktok_description' => 'Konten kreatif & video teknologi',
        'contact_form_title' => 'Ceritakan kebutuhan proyekmu',
        'contact_form_button' => 'Kirim melalui Email',

        // Integrasi & OAuth
        'google_client_id' => '',
        'google_client_secret' => '',
    ];
}

function get_homepage_content(PDO $pdo): array
{
    $defaults = homepage_content_defaults();
    $saved = json_decode(get_setting($pdo, 'homepage_content', '{}'), true);
    if (!is_array($saved)) {
        return $defaults;
    }

    $content = $defaults;
    foreach ($defaults as $key => $value) {
        if (isset($saved[$key]) && is_string($saved[$key])) {
            $content[$key] = trim($saved[$key]);
        }
    }
    $content['hero_readiness'] = (string) max(0, min(100, (int) ($content['hero_readiness'] ?? 88)));
    return $content;
}

function save_homepage_content(PDO $pdo, array $input): void
{
    $content = homepage_content_defaults();
    foreach ($content as $key => $default) {
        if (isset($input[$key])) {
            $value = trim((string) $input[$key]);
            $content[$key] = $value;
        }
    }
    $content['hero_readiness'] = (string) max(0, min(100, (int) ($content['hero_readiness'] ?? 88)));
    set_setting($pdo, 'homepage_content', json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
}

/**
 * Render icon SVG untuk skill chip dinamis
 */
function render_skill_chip_icon(string $icon, string $name = ''): string
{
    $normalized = strtolower(trim($icon));
    if (empty($normalized)) {
        $normalized = strtolower(preg_replace('/[^a-z0-9]/', '', $name));
    }

    $icons = [
        'nextjs' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"></circle><path d="M9 15V9l7.5 9"></path><path d="M15 9v3.5"></path></svg>',
        'react' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="2.5"></circle><ellipse cx="12" cy="12" rx="9" ry="3.8" transform="rotate(30 12 12)"></ellipse><ellipse cx="12" cy="12" rx="9" ry="3.8" transform="rotate(90 12 12)"></ellipse><ellipse cx="12" cy="12" rx="9" ry="3.8" transform="rotate(150 12 12)"></ellipse></svg>',
        'typescript' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M7 8h6m-3 0v8"></path><path d="M15 11c1-1 2-1 2.5 0s0 2-1 2.5 2 1.5 1.5 2.5-2 1-3 0"></path></svg>',
        'ts' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M7 8h6m-3 0v8"></path><path d="M15 11c1-1 2-1 2.5 0s0 2-1 2.5 2 1.5 1.5 2.5-2 1-3 0"></path></svg>',
        'tailwind' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 12c.5-2.5 2.5-4 5-4 3.5 0 4.5 2.5 6 3 1.5.5 2.5 0 3-1-1 3-3 4-5 4-3.5 0-4.5-2.5-6-3-1.5-.5-2.5 0-3 1z"></path></svg>',
        'shadcn' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"></circle><path d="M8 12h8"></path><path d="M12 8v8"></path></svg>',
        'bootstrap' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="4"></rect><path d="M9 8h4a2 2 0 0 1 0 4H9zm0 4h4.5a2 2 0 0 1 0 4H9z"></path></svg>',
        'javascript' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"></path><path d="M10 15v-5"></path><path d="M14 15c0-1.5 2-1.5 2-3s-2-1.5-2-3"></path></svg>',
        'js' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"></path><path d="M10 15v-5"></path><path d="M14 15c0-1.5 2-1.5 2-3s-2-1.5-2-3"></path></svg>',
        'html' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>',
        'laravel' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>',
        'php' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>',
        'blade' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 19 21 12 17 5 21 12 2"></polygon></svg>',
        'inertia' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>',
        'api' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>',
        'python' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a4 4 0 0 0-4 4v1a4 4 0 0 0-4 4 4 4 0 0 0 4 4v1a4 4 0 0 0 4 4 4 4 0 0 0 4-4v-1a4 4 0 0 0 4-4 4 4 0 0 0-4-4V6a4 4 0 0 0-4-4z"></path></svg>',
        'claude' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>',
        'openai' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg>',
        'cursor' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 17 10 11 4 5"></polyline><line x1="12" y1="19" x2="20" y2="19"></line></svg>',
        'prompteng' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a4 4 0 0 0-4 4v1a4 4 0 0 0-4 4 4 4 0 0 0 4 4v1a4 4 0 0 0 4 4 4 4 0 0 0 4-4v-1a4 4 0 0 0 4-4 4 4 0 0 0-4-4V6a4 4 0 0 0-4-4z"></path></svg>',
        'mysql' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>',
        'mariadb' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>',
        'postgres' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>',
        'redis' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>',
        'docker' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="14" width="18" height="7" rx="2"></rect><path d="M7 14v-3h3v3"></path><path d="M11 14v-3h3v3"></path><path d="M15 14v-3h3v3"></path></svg>',
        'git' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="18" r="3"></circle><circle cx="6" cy="6" r="3"></circle><path d="M18 9a9 9 0 0 1-9 9"></path></svg>',
        'linux' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>',
        'nginx' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2"></rect><rect x="2" y="14" width="20" height="8" rx="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>',
        'pest' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>',
        'phpunit' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>',
        'larastan' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>',
        'flutter' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="14 2 2 14 6 18 18 6 14 2"></polygon><polygon points="14 14 10 18 14 22 18 18 14 14"></polygon></svg>',
        'firebase' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 19 21 12 17 5 21 12 2"></polygon></svg>',
        'figma' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M5 5.5A3.5 3.5 0 0 1 8.5 2H12v7H8.5A3.5 3.5 0 0 1 5 5.5z"></path></svg>',
        'datamining' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="3"></circle><circle cx="5" cy="19" r="3"></circle><circle cx="19" cy="19" r="3"></circle><path d="M12 8v4m0 0l-5 4m5-4l5 4"></path></svg>',
        'c45' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="3"></circle><circle cx="6" cy="19" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="12" y1="8" x2="6" y2="16"></line><line x1="12" y1="8" x2="18" y2="16"></line></svg>',
        'rapidminer' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
    ];

    foreach ($icons as $k => $svg) {
        if ($normalized === $k || str_contains($normalized, $k)) {
            return $svg;
        }
    }

    // Default code fallback icon
    return '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>';
}
