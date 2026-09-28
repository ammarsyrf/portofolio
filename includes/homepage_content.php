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

        // Tech Stack Widget
        'tech_widget_title' => 'Tech Stack',
        'tech_widget_status' => '● Active',
        'tech_widget_footer' => '✦ Teruji di Produksi & Riset',
        'tech_widget_button' => 'Semua Skill ➔',
        'tech_chips_json' => json_encode([
            ['name' => 'Next.js', 'category' => 'frontend', 'role' => 'React SSR', 'highlight' => true],
            ['name' => 'Laravel 13', 'category' => 'backend', 'role' => 'Framework', 'highlight' => true],
            ['name' => 'MySQL', 'category' => 'database', 'role' => 'Utama / RDBMS', 'highlight' => true],
            ['name' => 'React', 'category' => 'frontend', 'role' => 'UI Library', 'highlight' => false],
            ['name' => 'Prompt Eng.', 'category' => 'ai', 'role' => 'Context & Logic', 'highlight' => false],
            ['name' => 'Docker', 'category' => 'devops', 'role' => 'Containerize', 'highlight' => false],
            ['name' => 'TypeScript', 'category' => 'frontend', 'role' => 'Type Safety', 'highlight' => false],
            ['name' => 'PHP 8.3+', 'category' => 'backend', 'role' => 'Language', 'highlight' => false],
            ['name' => 'Tailwind CSS', 'category' => 'frontend', 'role' => 'Utility CSS', 'highlight' => false],
            ['name' => 'Redis', 'category' => 'database', 'role' => 'Cache / Queue', 'highlight' => false],
            ['name' => 'Linux / VPS', 'category' => 'devops', 'role' => 'Server & Nginx', 'highlight' => false],
            ['name' => 'Pest PHP', 'category' => 'testing', 'role' => 'Automated Test', 'highlight' => false]
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),

        // About (Bento Card)
        'about_widget_title' => 'About Me',
        'about_widget_status' => 'S1 Sistem Informasi',
        'about_widget_text_one' => 'Lulusan S1 Sistem Informasi dengan passion mendalam pada arsitektur web modern, rekayasa fullstack yang tangguh, serta pemodelan analitik data cerdas.',
        'about_widget_text_two' => 'Berpengalaman merancang dan membangun sistem bisnis end-to-end dari nol: mulai dari perancangan database relasional, efisiensi server, hingga antarmuka siap pakai di tingkat produksi.',
        'about_metric_one_value' => '7+',
        'about_metric_one_label' => 'Sistem Nyata',
        'about_metric_two_value' => '100%',
        'about_metric_two_label' => 'Bebas SQLi',
        'about_metric_three_value' => 'Fullstack',
        'about_metric_three_label' => '& Analitik',

        // About Section Detail
        'about_caption' => 'Tentang Saya',
        'about_title' => 'Membangun Sistem yang Andal dari Hulu ke Hilir',
        'about_kicker' => 'Full-Stack Web Developer',
        'about_heading' => 'Dari rancangan sistem hingga aplikasi siap digunakan.',
        'about_paragraph_one' => 'Saya adalah seorang Full-Stack Web Developer yang berfokus pada pengembangan aplikasi web menggunakan PHP, Laravel, MySQL, React, dan Next.js. Saya terbiasa mengembangkan website dan sistem berbasis web mulai dari perancangan database, pembuatan REST API, implementasi fitur backend dan frontend, hingga proses deployment ke server.',
        'about_paragraph_two' => 'Dalam pengembangan aplikasi, saya tidak hanya berfokus pada tampilan dan fungsi, tetapi juga memperhatikan struktur sistem, performa, keamanan, skalabilitas, dan kemudahan maintenance. Saya juga memiliki pengalaman dalam penggunaan Git, Linux, VPS, Nginx, Docker, Redis, queue, serta integrasi berbagai layanan dan API.',
        'about_paragraph_three' => 'Saya memiliki ketertarikan besar pada pengembangan sistem yang efisien, otomatis, dan dapat menyelesaikan kebutuhan nyata pengguna. Saat ini saya terus memperdalam kemampuan di bidang full-stack development, system architecture, dan server infrastructure dengan tujuan berkembang sebagai Web Developer profesional dan dapat berkontribusi pada berbagai project secara remote maupun kolaboratif.',

        // Skills Section
        'skills_caption' => 'Kompetensi & Toolkit',
        'skills_title' => 'Keahlian Teknis & Domain Kerja',
        'skills_lead' => 'Kombinasi rekayasa backend modular, performa antarmuka bersih tanpa bloatware, serta pemodelan analitik data berbasis riset sistem informasi yang telah teruji pada berbagai aplikasi produksi nyata.',

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
        'tailwindcss' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 12c.5-2.5 2.5-4 5-4 3.5 0 4.5 2.5 6 3 1.5.5 2.5 0 3-1-1 3-3 4-5 4-3.5 0-4.5-2.5-6-3-1.5-.5-2.5 0-3 1z"></path></svg>',
        'laravel' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>',
        'laravel13' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>',
        'php' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="12" rx="10" ry="6"></ellipse><path d="M9 9v6m0-3h2a1.5 1.5 0 0 0 0-3H9zm6 0v6m0-3h2a1.5 1.5 0 0 0 0-3h-2z"></path></svg>',
        'php8' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="12" rx="10" ry="6"></ellipse><path d="M9 9v6m0-3h2a1.5 1.5 0 0 0 0-3H9zm6 0v6m0-3h2a1.5 1.5 0 0 0 0-3h-2z"></path></svg>',
        'mysql' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>',
        'redis' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"></path><path d="M2 17l10 5 10-5"></path><path d="M2 12l10 5 10-5"></path></svg>',
        'docker' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="14" width="18" height="7" rx="2"></rect><path d="M7 14v-3h3v3"></path><path d="M11 14v-3h3v3"></path><path d="M15 14v-3h3v3"></path></svg>',
        'git' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="18" r="3"></circle><circle cx="6" cy="6" r="3"></circle><path d="M18 9a9 9 0 0 1-9 9"></path></svg>',
        'linux' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>',
        'pest' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>',
        'pestphp' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>',
        'prompteng' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a4 4 0 0 0-4 4v1a4 4 0 0 0-4 4 4 4 0 0 0 4 4v1a4 4 0 0 0 4 4 4 4 0 0 0 4-4v-1a4 4 0 0 0 4-4 4 4 0 0 0-4-4V6a4 4 0 0 0-4-4z"></path></svg>',
        'ai' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a4 4 0 0 0-4 4v1a4 4 0 0 0-4 4 4 4 0 0 0 4 4v1a4 4 0 0 0 4 4 4 4 0 0 0 4-4v-1a4 4 0 0 0 4-4 4 4 0 0 0-4-4V6a4 4 0 0 0-4-4z"></path></svg>',
        'flutter' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="14 2 2 14 6 18 18 6 14 2"></polygon><polygon points="14 14 10 18 14 22 18 18 14 14"></polygon></svg>',
        'c45' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="3"></circle><circle cx="6" cy="19" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="12" y1="8" x2="6" y2="16"></line><line x1="12" y1="8" x2="18" y2="16"></line></svg>',
        'figma' => '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M5 5.5A3.5 3.5 0 0 1 8.5 2H12v7H8.5A3.5 3.5 0 0 1 5 5.5z"></path></svg>',
    ];

    foreach ($icons as $k => $svg) {
        if ($normalized === $k || str_contains($normalized, $k)) {
            return $svg;
        }
    }

    // Default code fallback icon
    return '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>';
}
