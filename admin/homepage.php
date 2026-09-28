<?php
/**
 * Pengelola Konten Halaman Utama (Homepage)
 * Desain: 2-Column Master-Detail Sub-Sidebar Editor dengan Sub-Tabs Per Domain, Matrix & Deck
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/homepage_content.php';

$pageTitle = 'Konten Halaman Utama';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errorMessage = 'Sesi form tidak valid. Silakan coba kembali.';
    } else {
        try {
            save_homepage_content($pdo, $_POST);
            set_flash('success', 'Konten halaman utama berhasil diperbarui!');
            redirect(BASE_URL . '/admin/homepage');
        } catch (JsonException $exception) {
            $errorMessage = 'Konten tidak dapat disimpan. Silakan coba kembali.';
        }
    }
}

$content = get_homepage_content($pdo);

// Kategori Terstruktur (Hierarchical Clusters)
$categories = [
    'hero_cluster' => [
        'name' => 'Header & Hero Section',
        'icon' => '🌟',
        'items' => [
            'nav' => [
                'title' => 'Navigasi & Brand',
                'icon'  => '🧭',
                'desc'  => 'Nama brand navbar (lengkap & singkatan), serta teks tombol aksi/rekrutmen.',
                'keys'  => ['nav_brand_full', 'nav_brand_short', 'nav_hire_text']
            ],
            'hero' => [
                'title' => 'Hero & Statistik',
                'icon'  => '⚡',
                'desc'  => 'Teks headline, gelar, ketersediaan rekrutmen, dan badge utama pada hero section.',
                'keys'  => ['hero_education_label','hero_degree','hero_field','hero_specialty_label','hero_specialty_value','hero_availability_label','hero_availability_value','hero_readiness','hero_line_one','hero_line_two','hero_line_three','hero_badge','hero_vertical_badge']
            ],
            'terminal' => [
                'title' => 'Terminal Console',
                'icon'  => '💻',
                'desc'  => 'Teks status live terminal mockup, fokus stack, workflow, dan simulasi pengujian.',
                'keys'  => [
                    'terminal_title', 'terminal_status_badge', 'terminal_user', 
                    'terminal_status_val', 'terminal_role_val', 'terminal_focus_val', 
                    'terminal_ai_val', 'terminal_workflow_val', 'terminal_location_val', 
                    'terminal_cmd_test', 'terminal_cmd_pass', 'terminal_summary_line1', 'terminal_summary_line2'
                ]
            ],
            'brand' => [
                'title' => 'Brand Zenerie',
                'icon'  => '💎',
                'desc'  => 'Kartu profil brand Zenerie, link website, dan tombol aksi.',
                'keys'  => ['brand_title','brand_badge','brand_description','brand_url','brand_button','identity_button','media_title','media_button']
            ],
        ]
    ],
    'profile_cluster' => [
        'name' => 'Profil & Alur Kerja',
        'icon' => '🪪',
        'items' => [
            'about_bento' => [
                'title' => 'Tentang (Bento Hero)',
                'icon'  => '🪪',
                'desc'  => 'Teks ringkasan dan metrik statistik pada kartu bento profil hero.',
                'keys'  => ['about_widget_title','about_widget_status','about_widget_text_one','about_widget_text_two','about_metric_one_value','about_metric_one_label','about_metric_two_value','about_metric_two_label','about_metric_three_value','about_metric_three_label']
            ],
            'deck' => [
                'title' => 'Alur & Standar Kerja',
                'icon'  => '🎯',
                'desc'  => 'Teks bento deck panel: Cara Saya Bekerja, 3 Langkah Alur, Standar Rekayasa, dan Kolaborasi.',
                'keys'  => [
                    'deck_vision_eyebrow', 'deck_vision_title', 'deck_vision_lead',
                    'deck_s1_title', 'deck_s1_desc',
                    'deck_s2_title', 'deck_s2_desc',
                    'deck_s3_title', 'deck_s3_desc',
                    'deck_std_title',
                    'deck_std1_icon', 'deck_std1_title', 'deck_std1_desc',
                    'deck_std2_icon', 'deck_std2_title', 'deck_std2_desc',
                    'deck_std3_icon', 'deck_std3_title', 'deck_std3_desc',
                    'deck_collab_title', 'deck_collab_role_lbl', 'deck_collab_role_val',
                    'deck_collab_avail_lbl', 'deck_collab_avail_val',
                    'deck_collab_mode_lbl', 'deck_collab_mode_val', 'deck_collab_btn_text'
                ]
            ],
            'about_section' => [
                'title' => 'Tentang Saya (Detail)',
                'icon'  => '📖',
                'desc'  => 'Seluruh paragraf narasi profil, rekayasa sistem, dan latar belakang profesional.',
                'keys'  => ['about_caption','about_title','about_kicker','about_heading','about_paragraph_one','about_paragraph_two','about_paragraph_three']
            ],
        ]
    ],
    'skills_cluster' => [
        'name' => 'Keahlian & Teknologi',
        'icon' => '🛠️',
        'items' => [
            'tech_chips' => [
                'title' => 'Tech Stack (Chips Bento)',
                'icon'  => '🧩',
                'desc'  => 'Daftar skill chips interaktif di widget Hero Bento dalam format JSON terstruktur.',
                'keys'  => ['tech_widget_title', 'tech_widget_status', 'tech_widget_footer', 'tech_widget_button', 'tech_chips_json']
            ],
            'skills' => [
                'title' => 'Keahlian Header',
                'icon'  => '🛠️',
                'desc'  => 'Caption, judul, dan lead paragraf pengantar section keahlian & toolkit.',
                'keys'  => ['skills_caption','skills_title','skills_lead']
            ],
            'matrix' => [
                'title' => 'Matriks Keahlian (Tier)',
                'icon'  => '⚡',
                'desc'  => 'Matriks Tingkat Penguasaan Teknologi: Tier 1 (Advanced), Tier 2 (Proficient), Tier 3 (Familiar).',
                'keys'  => [
                    'matrix_kicker', 'matrix_title', 'matrix_desc',
                    'tier1_badge', 'tier1_level', 'tier1_title', 'tier1_desc', 'tier1_percent', 'tier1_chips',
                    'tier2_badge', 'tier2_level', 'tier2_title', 'tier2_desc', 'tier2_percent', 'tier2_chips',
                    'tier3_badge', 'tier3_level', 'tier3_title', 'tier3_desc', 'tier3_percent', 'tier3_chips'
                ]
            ],
            'domains' => [
                'title' => 'Domain Keahlian (6 Kartu)',
                'icon'  => '🗂️',
                'desc'  => '6 Kartu domain spesialisasi: Frontend, Backend, Database, DevOps, Testing & Mobile, Analitik Data.',
                'keys'  => [
                    // Domain 1: Frontend
                    'domain1_badge', 'domain1_title', 'domain1_desc',
                    'domain1_grp1_title', 'domain1_grp1_tags',
                    'domain1_grp2_title', 'domain1_grp2_tags',
                    'domain1_b1', 'domain1_b2', 'domain1_b3', 'domain1_eco',
                    // Domain 2: Backend
                    'domain2_badge', 'domain2_title', 'domain2_desc',
                    'domain2_grp1_title', 'domain2_grp1_tags',
                    'domain2_grp2_title', 'domain2_grp2_tags',
                    'domain2_b1', 'domain2_b2', 'domain2_b3', 'domain2_eco',
                    // Domain 3: Database
                    'domain3_badge', 'domain3_title', 'domain3_desc',
                    'domain3_grp1_title', 'domain3_grp1_tags',
                    'domain3_grp2_title', 'domain3_grp2_tags',
                    'domain3_b1', 'domain3_b2', 'domain3_b3', 'domain3_eco',
                    // Domain 4: DevOps
                    'domain4_badge', 'domain4_title', 'domain4_desc',
                    'domain4_grp1_title', 'domain4_grp1_tags',
                    'domain4_grp2_title', 'domain4_grp2_tags',
                    'domain4_b1', 'domain4_b2', 'domain4_b3', 'domain4_eco',
                    // Domain 5: Testing & Mobile
                    'domain5_badge', 'domain5_title', 'domain5_desc',
                    'domain5_grp1_title', 'domain5_grp1_tags',
                    'domain5_grp2_title', 'domain5_grp2_tags',
                    'domain5_b1', 'domain5_b2', 'domain5_b3', 'domain5_eco',
                    // Domain 6: Analytics
                    'domain6_badge', 'domain6_title', 'domain6_desc',
                    'domain6_grp1_title', 'domain6_grp1_tags',
                    'domain6_grp2_title', 'domain6_grp2_tags',
                    'domain6_b1', 'domain6_b2', 'domain6_b3', 'domain6_eco'
                ]
            ],
        ]
    ],
    'content_cluster' => [
        'name' => 'Portofolio, Setup & Kontak',
        'icon' => '💼',
        'items' => [
            'projects' => [
                'title' => 'Proyek Section',
                'icon'  => '💼',
                'desc'  => 'Caption dan judul pengantar section karya & proyek.',
                'keys'  => ['projects_caption','projects_title']
            ],
            'gear' => [
                'title' => 'Gear & Workspace',
                'icon'  => '⚙️',
                'desc'  => 'Daftar hardware, toolset software, dan suite AI di tab gear & setup.',
                'keys'  => [
                    'gear_hw_laptop_name', 'gear_hw_laptop_desc',
                    'gear_hw_display_name', 'gear_hw_display_desc',
                    'gear_hw_keyboard_name', 'gear_hw_keyboard_desc',
                    'gear_hw_audio_name', 'gear_hw_audio_desc',
                    'gear_sw_os_tags', 'gear_sw_ide_tags', 'gear_sw_tools_tags',
                    'gear_ai_models_desc', 'gear_ai_prompt_desc', 'gear_ai_design_desc', 'gear_ai_devops_desc'
                ]
            ],
            'contact' => [
                'title' => 'Bagian Kontak',
                'icon'  => '📬',
                'desc'  => 'Judul kartu email, jejaring profesional, sosial media, dan teks form pesan singkat.',
                'keys'  => ['contact_caption','contact_title','contact_email_title','contact_email_description','contact_linkedin_title','contact_linkedin_description','contact_github_title','contact_github_description','contact_instagram_title','contact_instagram_description','contact_tiktok_title','contact_tiktok_description','contact_form_title','contact_form_button']
            ],
            'oauth' => [
                'title' => 'Integrasi & OAuth',
                'icon'  => '🔑',
                'desc'  => 'Kredensial Google OAuth 2.0 untuk autentikasi Buku Tamu publik.',
                'keys'  => ['google_client_id', 'google_client_secret']
            ],
        ]
    ]
];

// Flattened groups for processing
$groups = [];
foreach ($categories as $cat) {
    foreach ($cat['items'] as $id => $item) {
        $groups[$id] = $item;
    }
}

$labels = [
    'nav_brand_full' => 'Nama Brand Lengkap (Navbar)',
    'nav_brand_short' => 'Inisial / Brand Singkat (Navbar)',
    'nav_hire_text' => 'Teks Tombol Rekrut (Navbar)',

    'hero_education_label'=>'Label pendidikan','hero_degree'=>'Gelar singkat','hero_field'=>'Bidang pendidikan','hero_specialty_label'=>'Label spesialisasi','hero_specialty_value'=>'Nilai spesialisasi','hero_availability_label'=>'Label ketersediaan','hero_availability_value'=>'Nilai ketersediaan','hero_readiness'=>'Kesiapan teknis (0–100)','hero_line_one'=>'Hero baris 1','hero_line_two'=>'Hero baris 2','hero_line_three'=>'Hero baris 3','hero_badge'=>'Badge hero','hero_vertical_badge'=>'Badge vertikal',

    'terminal_title' => 'Judul Shell Terminal (contoh: ammar@zen:~$)',
    'terminal_status_badge' => 'Badge Status Terminal (contoh: ACTIVE)',
    'terminal_user' => 'User Prompt (contoh: zen@dev)',
    'terminal_status_val' => 'Nilai Status (contoh: ● Available for Hire)',
    'terminal_role_val' => 'Nilai Role (contoh: Fullstack & AI-Augmented Dev)',
    'terminal_focus_val' => 'Nilai Focus (contoh: Laravel 13 • MySQL • Next.js)',
    'terminal_ai_val' => 'Nilai AI Stack (contoh: Claude • Gemini • OpenAI)',
    'terminal_workflow_val' => 'Nilai Workflow (contoh: Agentic Acceleration (3x Speed))',
    'terminal_location_val' => 'Nilai Location (contoh: Jakarta, ID (GMT+7))',
    'terminal_cmd_test' => 'Perintah Simulasi Tes (contoh: pest test && eval-prompt)',
    'terminal_cmd_pass' => 'Teks Hasil Tes Lulus (contoh: Pest v3 • 18 passed (0.04s))',
    'terminal_summary_line1' => 'Baris Ringkasan 1 (contoh: ✓ 100% Bebas SQLi & Siap Produksi)',
    'terminal_summary_line2' => 'Baris Ringkasan 2 (contoh: ✓ Prompt Token Efficiency: 98.4%)',

    'brand_title'=>'Judul brand','brand_badge'=>'Badge brand','brand_description'=>'Deskripsi Zenerie','brand_url'=>'URL website Zenerie','brand_button'=>'Teks tombol',
    'identity_button'=>'Tombol profil','media_title'=>'Judul media','media_button'=>'Tombol media',
    
    'deck_vision_eyebrow' => 'Kicker Alur Kerja (contoh: Cara saya bekerja)',
    'deck_vision_title' => 'Judul Visi Kerja (contoh: Sistem yang siap dipakai...)',
    'deck_vision_lead' => 'Deskripsi Visi Kerja',
    'deck_s1_title' => 'Langkah 1: Judul',
    'deck_s1_desc' => 'Langkah 1: Deskripsi',
    'deck_s2_title' => 'Langkah 2: Judul',
    'deck_s2_desc' => 'Langkah 2: Deskripsi',
    'deck_s3_title' => 'Langkah 3: Judul',
    'deck_s3_desc' => 'Langkah 3: Deskripsi',
    
    'deck_std_title' => 'Judul Standar Rekayasa',
    'deck_std1_icon' => 'Standar 1: Ikon (Emoji)',
    'deck_std1_title' => 'Standar 1: Judul',
    'deck_std1_desc' => 'Standar 1: Deskripsi',
    'deck_std2_icon' => 'Standar 2: Ikon (Emoji)',
    'deck_std2_title' => 'Standar 2: Judul',
    'deck_std2_desc' => 'Standar 2: Deskripsi',
    'deck_std3_icon' => 'Standar 3: Ikon (Emoji)',
    'deck_std3_title' => 'Standar 3: Judul',
    'deck_std3_desc' => 'Standar 3: Deskripsi',

    'deck_collab_title' => 'Judul Kolaborasi',
    'deck_collab_role_lbl' => 'Label Fokus Peran',
    'deck_collab_role_val' => 'Nilai Fokus Peran',
    'deck_collab_avail_lbl' => 'Label Ketersediaan',
    'deck_collab_avail_val' => 'Nilai Ketersediaan',
    'deck_collab_mode_lbl' => 'Label Cara Kerja',
    'deck_collab_mode_val' => 'Nilai Cara Kerja',
    'deck_collab_btn_text' => 'Teks Tombol Kolaborasi',

    'tech_widget_title'=>'Judul widget tech stack','tech_widget_status'=>'Status widget tech stack','tech_widget_footer'=>'Footer widget tech stack','tech_widget_button'=>'Tombol widget tech stack',
    'tech_chips_json' => 'Data JSON Skill Chips (name, category, role, highlight, icon)',

    'gear_hw_laptop_name' => 'Nama Hardware: Mesin Utama (Laptop/PC)',
    'gear_hw_laptop_desc' => 'Spesifikasi Mesin Utama',
    'gear_hw_display_name' => 'Nama Hardware: Monitor / Layar',
    'gear_hw_display_desc' => 'Spesifikasi Monitor / Layar',
    'gear_hw_keyboard_name' => 'Nama Hardware: Keyboard / Input',
    'gear_hw_keyboard_desc' => 'Spesifikasi Keyboard / Mouse',
    'gear_hw_audio_name' => 'Nama Hardware: Audio / Monitor',
    'gear_hw_audio_desc' => 'Spesifikasi Audio / IEM',
    'gear_sw_os_tags' => 'Daftar Tag OS & Shell (pisahkan dengan koma)',
    'gear_sw_ide_tags' => 'Daftar Tag IDE & Editor (pisahkan dengan koma)',
    'gear_sw_tools_tags' => 'Daftar Tag Runtime, DB & API (pisahkan dengan koma)',
    'gear_ai_models_desc' => 'Deskripsi: Frontier AI Models',
    'gear_ai_prompt_desc' => 'Deskripsi: Prompt & Agent Engineering',
    'gear_ai_design_desc' => 'Deskripsi: Desain & Manajemen Ide',
    'gear_ai_devops_desc' => 'Deskripsi: DevOps & Hosting',

    'projects_caption'=>'Caption proyek','projects_title'=>'Judul proyek',
    'about_widget_title'=>'Judul kartu','about_widget_status'=>'Status','about_widget_text_one'=>'Paragraf 1','about_widget_text_two'=>'Paragraf 2','about_metric_one_value'=>'Metrik 1 nilai','about_metric_one_label'=>'Metrik 1 label','about_metric_two_value'=>'Metrik 2 nilai','about_metric_two_label'=>'Metrik 2 label','about_metric_three_value'=>'Metrik 3 nilai','about_metric_three_label'=>'Metrik 3 label',
    'about_caption'=>'Caption section','about_title'=>'Judul section','about_kicker'=>'Kicker','about_heading'=>'Judul panel','about_paragraph_one'=>'Paragraf 1','about_paragraph_two'=>'Paragraf 2','about_paragraph_three'=>'Paragraf 3',
    'skills_caption'=>'Caption section','skills_title'=>'Judul section','skills_lead'=>'Deskripsi section',

    'matrix_kicker' => 'Kicker Matriks',
    'matrix_title' => 'Judul Matriks Penguasaan',
    'matrix_desc' => 'Deskripsi Matriks Penguasaan',
    'tier1_badge' => 'Tier 1: Badge Label',
    'tier1_level' => 'Tier 1: Keterangan Level & Persentase',
    'tier1_title' => 'Tier 1: Judul Domain',
    'tier1_desc' => 'Tier 1: Deskripsi Kompetensi',
    'tier1_percent' => 'Tier 1: Progress Bar (%) (0-100)',
    'tier1_chips' => 'Tier 1: Tag Chips (pisahkan dengan koma)',
    'tier2_badge' => 'Tier 2: Badge Label',
    'tier2_level' => 'Tier 2: Keterangan Level & Persentase',
    'tier2_title' => 'Tier 2: Judul Domain',
    'tier2_desc' => 'Tier 2: Deskripsi Kompetensi',
    'tier2_percent' => 'Tier 2: Progress Bar (%) (0-100)',
    'tier2_chips' => 'Tier 2: Tag Chips (pisahkan dengan koma)',
    'tier3_badge' => 'Tier 3: Badge Label',
    'tier3_level' => 'Tier 3: Keterangan Level & Persentase',
    'tier3_title' => 'Tier 3: Judul Domain',
    'tier3_desc' => 'Tier 3: Deskripsi Kompetensi',
    'tier3_percent' => 'Tier 3: Progress Bar (%) (0-100)',
    'tier3_chips' => 'Tier 3: Tag Chips (pisahkan dengan koma)',

    // Domain 1: Frontend
    'domain1_badge' => 'Badge Tag (High-Fidelity UI)',
    'domain1_title' => 'Judul Domain',
    'domain1_desc' => 'Deskripsi Domain',
    'domain1_grp1_title' => 'Subgroup 1: Judul',
    'domain1_grp1_tags' => 'Subgroup 1: Daftar Tags (pisahkan koma)',
    'domain1_grp2_title' => 'Subgroup 2: Judul',
    'domain1_grp2_tags' => 'Subgroup 2: Daftar Tags (pisahkan koma)',
    'domain1_b1' => 'Highlight Poin 1',
    'domain1_b2' => 'Highlight Poin 2',
    'domain1_b3' => 'Highlight Poin 3',
    'domain1_eco' => 'Teks Footer / Ekosistem',

    // Domain 2: Backend
    'domain2_badge' => 'Badge Tag (Core Architecture)',
    'domain2_title' => 'Judul Domain',
    'domain2_desc' => 'Deskripsi Domain',
    'domain2_grp1_title' => 'Subgroup 1: Judul',
    'domain2_grp1_tags' => 'Subgroup 1: Daftar Tags (pisahkan koma)',
    'domain2_grp2_title' => 'Subgroup 2: Judul',
    'domain2_grp2_tags' => 'Subgroup 2: Daftar Tags (pisahkan koma)',
    'domain2_b1' => 'Highlight Poin 1',
    'domain2_b2' => 'Highlight Poin 2',
    'domain2_b3' => 'Highlight Poin 3',
    'domain2_eco' => 'Teks Footer / Ekosistem',

    // Domain 3: Database
    'domain3_badge' => 'Badge Tag (Data Persistence)',
    'domain3_title' => 'Judul Domain',
    'domain3_desc' => 'Deskripsi Domain',
    'domain3_grp1_title' => 'Subgroup 1: Judul',
    'domain3_grp1_tags' => 'Subgroup 1: Daftar Tags (pisahkan koma)',
    'domain3_grp2_title' => 'Subgroup 2: Judul',
    'domain3_grp2_tags' => 'Subgroup 2: Daftar Tags (pisahkan koma)',
    'domain3_b1' => 'Highlight Poin 1',
    'domain3_b2' => 'Highlight Poin 2',
    'domain3_b3' => 'Highlight Poin 3',
    'domain3_eco' => 'Teks Footer / Ekosistem',

    // Domain 4: DevOps
    'domain4_badge' => 'Badge Tag (Deployment & Infra)',
    'domain4_title' => 'Judul Domain',
    'domain4_desc' => 'Deskripsi Domain',
    'domain4_grp1_title' => 'Subgroup 1: Judul',
    'domain4_grp1_tags' => 'Subgroup 1: Daftar Tags (pisahkan koma)',
    'domain4_grp2_title' => 'Subgroup 2: Judul',
    'domain4_grp2_tags' => 'Subgroup 2: Daftar Tags (pisahkan koma)',
    'domain4_b1' => 'Highlight Poin 1',
    'domain4_b2' => 'Highlight Poin 2',
    'domain4_b3' => 'Highlight Poin 3',
    'domain4_eco' => 'Teks Footer / Ekosistem',

    // Domain 5: Testing & Mobile
    'domain5_badge' => 'Badge Tag (Quality & Mobile)',
    'domain5_title' => 'Judul Domain',
    'domain5_desc' => 'Deskripsi Domain',
    'domain5_grp1_title' => 'Subgroup 1: Judul',
    'domain5_grp1_tags' => 'Subgroup 1: Daftar Tags (pisahkan koma)',
    'domain5_grp2_title' => 'Subgroup 2: Judul',
    'domain5_grp2_tags' => 'Subgroup 2: Daftar Tags (pisahkan koma)',
    'domain5_b1' => 'Highlight Poin 1',
    'domain5_b2' => 'Highlight Poin 2',
    'domain5_b3' => 'Highlight Poin 3',
    'domain5_eco' => 'Teks Footer / Ekosistem',

    // Domain 6: Analytics
    'domain6_badge' => 'Badge Tag (Applied Data Mining)',
    'domain6_title' => 'Judul Domain',
    'domain6_desc' => 'Deskripsi Domain',
    'domain6_grp1_title' => 'Subgroup 1: Judul',
    'domain6_grp1_tags' => 'Subgroup 1: Daftar Tags (pisahkan koma)',
    'domain6_grp2_title' => 'Subgroup 2: Judul',
    'domain6_grp2_tags' => 'Subgroup 2: Daftar Tags (pisahkan koma)',
    'domain6_b1' => 'Highlight Poin 1',
    'domain6_b2' => 'Highlight Poin 2',
    'domain6_b3' => 'Highlight Poin 3',
    'domain6_eco' => 'Teks Footer / Ekosistem',

    'contact_caption'=>'Caption section','contact_title'=>'Judul section','contact_email_title'=>'Judul kartu email','contact_email_description'=>'Deskripsi kartu email','contact_linkedin_title'=>'Judul kartu LinkedIn','contact_linkedin_description'=>'Deskripsi kartu LinkedIn','contact_github_title'=>'Judul kartu GitHub','contact_github_description'=>'Deskripsi kartu GitHub','contact_instagram_title'=>'Judul kartu Instagram','contact_instagram_description'=>'Deskripsi kartu Instagram','contact_tiktok_title'=>'Judul kartu TikTok','contact_tiktok_description'=>'Deskripsi kartu TikTok','contact_form_title'=>'Judul form pesan','contact_form_button'=>'Teks tombol form',

    'google_client_id' => 'Google OAuth Client ID',
    'google_client_secret' => 'Google OAuth Client Secret',
];

// Definisi sub-domain detail
$domainsMeta = [
    1 => ['name' => 'Frontend', 'icon' => '⚛️', 'default_title' => 'Frontend Engineering', 'prefix' => 'domain1_'],
    2 => ['name' => 'Backend', 'icon' => '⚙️', 'default_title' => 'Backend & Architecture', 'prefix' => 'domain2_'],
    3 => ['name' => 'Database', 'icon' => '🗄️', 'default_title' => 'Database & Storage', 'prefix' => 'domain3_'],
    4 => ['name' => 'DevOps', 'icon' => '🚀', 'default_title' => 'DevOps & Server Infra', 'prefix' => 'domain4_'],
    5 => ['name' => 'Testing & Mobile', 'icon' => '🧪', 'default_title' => 'Testing, QA & Mobile', 'prefix' => 'domain5_'],
    6 => ['name' => 'Analitik Data', 'icon' => '📊', 'default_title' => 'Analitik Data & Sistem', 'prefix' => 'domain6_'],
];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<style>
  /* --- 2-Column Master Detail Sub-Sidebar Layout --- */
  .hp-editor-shell {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 1.5rem;
    align-items: start;
    position: relative;
  }

  @media (max-width: 1024px) {
    .hp-editor-shell {
      grid-template-columns: 1fr;
    }
  }

  /* Sub-Sidebar Navigation Column */
  .hp-subnav-sidebar {
    position: sticky;
    top: 5rem;
    background: rgba(14, 18, 27, 0.95);
    border: 1px solid var(--color-line);
    border-radius: 16px;
    padding: 1.1rem;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.45);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    max-height: calc(100vh - 6.5rem);
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(76, 141, 255, 0.3) transparent;
  }

  .hp-subnav-sidebar::-webkit-scrollbar {
    width: 5px;
  }
  .hp-subnav-sidebar::-webkit-scrollbar-thumb {
    background: rgba(76, 141, 255, 0.3);
    border-radius: 4px;
  }

  .hp-search-box {
    position: relative;
    margin-bottom: 1rem;
  }

  .hp-search-input {
    width: 100%;
    background: rgba(8, 11, 19, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 0.55rem 0.75rem 0.55rem 2.2rem;
    font-size: 0.82rem;
    color: #fff;
    outline: none;
    transition: all 0.2s ease;
  }
  .hp-search-input:focus {
    border-color: rgba(76, 141, 255, 0.6);
    box-shadow: 0 0 12px rgba(76, 141, 255, 0.25);
  }
  .hp-search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.85rem;
    color: var(--color-text-dim);
    pointer-events: none;
  }

  /* Cluster Group Headers */
  .hp-cluster-group {
    margin-bottom: 1.1rem;
  }
  .hp-cluster-group:last-child {
    margin-bottom: 0;
  }
  .hp-cluster-title {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--color-accent-bright);
    margin-bottom: 0.45rem;
    padding-left: 0.4rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
  }

  .hp-nav-list {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }

  .hp-nav-item-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    text-align: left;
    background: transparent;
    border: 1px solid transparent;
    border-radius: 10px;
    padding: 0.5rem 0.65rem;
    color: var(--color-text-dim);
    font-size: 0.82rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .hp-nav-item-btn:hover {
    background: rgba(255, 255, 255, 0.06);
    color: #ffffff;
    border-color: rgba(255, 255, 255, 0.08);
  }

  .hp-nav-item-btn.is-active {
    background: rgba(76, 141, 255, 0.18);
    border-color: rgba(76, 141, 255, 0.45);
    color: #ffffff;
    font-weight: 600;
    box-shadow: 0 4px 14px rgba(76, 141, 255, 0.15);
  }

  .hp-nav-item-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .hp-nav-count-badge {
    font-size: 0.68rem;
    padding: 0.1rem 0.4rem;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.08);
    color: var(--color-text-dim);
  }
  .hp-nav-item-btn.is-active .hp-nav-count-badge {
    background: rgba(76, 141, 255, 0.35);
    color: #fff;
  }

  /* Right Editor Column */
  .hp-editor-main {
    min-width: 0;
  }

  .hp-sticky-bar {
    position: sticky;
    top: 5rem;
    z-index: 30;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    background: rgba(14, 18, 27, 0.95);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(76, 141, 255, 0.35);
    padding: 0.85rem 1.25rem;
    border-radius: 14px;
    margin-bottom: 1.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.55);
  }

  .hp-active-label {
    font-family: var(--font-display);
    font-size: 1.05rem;
    font-weight: 700;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .hp-section-panel {
    display: none;
    animation: hpFadeIn 0.22s ease-out;
  }
  .hp-section-panel.is-active {
    display: block;
  }
  .hp-show-all .hp-section-panel {
    display: block !important;
    margin-bottom: 2rem;
  }

  @keyframes hpFadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .hp-panel-card {
    border: 1px solid var(--color-line);
    border-radius: 16px;
    padding: 1.5rem;
    background: rgba(15, 23, 42, 0.45);
    margin-bottom: 1.5rem;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
  }

  .hp-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.25rem;
    padding-bottom: 0.85rem;
    border-bottom: 1px solid var(--color-line);
    gap: 1rem;
  }

  .hp-panel-title {
    font-family: var(--font-display);
    font-size: 1.15rem;
    font-weight: 700;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .hp-panel-desc {
    font-size: 0.82rem;
    color: var(--color-text-dim);
    margin-top: 0.3rem;
    line-height: 1.45;
  }

  /* --- Sub-Tab Bar for Multi-Card Sections (Domains, Matrix, Deck) --- */
  .hp-subtab-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    background: rgba(8, 11, 19, 0.7);
    padding: 0.45rem;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    margin-bottom: 1.25rem;
  }

  .hp-subtab-btn {
    background: transparent;
    border: 1px solid transparent;
    color: var(--color-text-dim);
    padding: 0.45rem 0.85rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.15s ease;
  }
  .hp-subtab-btn:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.06);
  }
  .hp-subtab-btn.is-active {
    background: rgba(76, 141, 255, 0.22);
    border-color: rgba(76, 141, 255, 0.5);
    color: #ffffff;
    box-shadow: 0 0 12px rgba(76, 141, 255, 0.25);
  }

  .hp-subcard-box {
    background: rgba(10, 14, 23, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1rem;
  }
  .hp-subcard-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }

  .domain-subpanel,
  .matrix-subpanel,
  .deck-subpanel {
    display: none;
    animation: hpFadeIn 0.2s ease-out;
  }
  .domain-subpanel.is-active,
  .matrix-subpanel.is-active,
  .deck-subpanel.is-active {
    display: block;
  }

  .hp-tab-nav-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 1.25rem;
    margin-top: 1.25rem;
    border-top: 1px solid var(--color-line);
  }
</style>

<div class="admin-card" style="margin-bottom: 1.25rem;">
  <div class="admin-card-header" style="margin-bottom: 0;">
    <div>
      <div class="admin-card-title">Pengelola Konten Halaman Utama</div>
      <p style="font-size: var(--text-xs); color: var(--color-cream-muted); margin-top: 0.25rem;">
        Pilih bagian halaman dari <strong>Sidebar Sub-Menu</strong> di sebelah kiri untuk mengedit teks &amp; komponen visual secara spesifik.
      </p>
    </div>
    <div>
      <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: var(--text-xs); color: var(--color-text-dim); cursor: pointer;">
        <input type="checkbox" id="toggleShowAll">
        <span>Tampilkan Semua Bagian Sekaligus</span>
      </label>
    </div>
  </div>
</div>

<?php if ($errorMessage): ?>
  <div class="flash-alert flash-danger" style="margin-bottom: 1.25rem;">
    <div><?= e($errorMessage) ?></div>
  </div>
<?php endif; ?>

<form method="post" id="homepageForm">
  <?= csrf_field() ?>

  <!-- 2-Column Shell -->
  <div class="hp-editor-shell">
    
    <!-- LEFT: Sub-Sidebar Category Menu -->
    <aside class="hp-subnav-sidebar">
      <div class="hp-search-box">
        <span class="hp-search-icon">🔍</span>
        <input type="text" id="hpMenuSearch" class="hp-search-input" placeholder="Cari bagian / keyword...">
      </div>

      <?php 
      $groupKeys = array_keys($groups);
      $firstKey = $groupKeys[0];
      foreach ($categories as $catKey => $cat): 
      ?>
        <div class="hp-cluster-group">
          <div class="hp-cluster-title">
            <span><?= $cat['icon'] ?></span>
            <span><?= e($cat['name']) ?></span>
          </div>
          <div class="hp-nav-list">
            <?php foreach ($cat['items'] as $id => $grp): 
              $isFirst = ($id === $firstKey);
            ?>
              <button type="button" 
                      class="hp-nav-item-btn <?= $isFirst ? 'is-active' : '' ?>" 
                      data-tab-target="panel-<?= e($id) ?>"
                      data-tab-title="<?= e($grp['icon'] . ' ' . $grp['title']) ?>"
                      data-keywords="<?= strtolower(e($grp['title'] . ' ' . $grp['desc'] . ' ' . implode(' ', $grp['keys']))) ?>">
                <span class="hp-nav-item-label">
                  <span><?= $grp['icon'] ?></span>
                  <span><?= e($grp['title']) ?></span>
                </span>
                <span class="hp-nav-count-badge"><?= count($grp['keys']) ?></span>
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </aside>

    <!-- RIGHT: Active Editor Panels -->
    <main class="hp-editor-main">
      
      <!-- Sticky Action Bar -->
      <div class="hp-sticky-bar">
        <div class="hp-sticky-info">
          <span class="hp-active-label" id="currentSectionTitle">
            <span>🧭</span> Navigasi &amp; Brand
          </span>
          <span style="font-size: 0.72rem; color: #34d399; font-weight: 600; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.15rem 0.5rem; border-radius: 9999px;">
            Aktif
          </span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <button class="btn btn-primary" type="submit" style="padding: 0.55rem 1.25rem; font-size: 0.85rem; font-weight: 700; box-shadow: 0 0 16px rgba(76, 141, 255, 0.4);">
            💾 Simpan Semua Konten
          </button>
          <a class="btn btn-secondary" target="_blank" href="<?= BASE_URL ?>/" style="padding: 0.55rem 1rem; font-size: 0.85rem;">
            Pratinjau Web ↗
          </a>
        </div>
      </div>

      <!-- Panels Container -->
      <div id="homepagePanelsContainer">
        <?php 
        $totalGroups = count($groups);
        $groupIndex = 0;
        foreach ($groups as $id => $grp): 
          $isFirst = ($id === $firstKey);
          $prevId = $groupIndex > 0 ? $groupKeys[$groupIndex - 1] : null;
          $nextId = $groupIndex < ($totalGroups - 1) ? $groupKeys[$groupIndex + 1] : null;
        ?>
          <div class="hp-section-panel <?= $isFirst ? 'is-active' : '' ?>" id="panel-<?= e($id) ?>">
            <div class="hp-panel-card">
              
              <div class="hp-panel-header">
                <div>
                  <div class="hp-panel-title">
                    <span><?= $grp['icon'] ?></span>
                    <span><?= e($grp['title']) ?></span>
                  </div>
                  <div class="hp-panel-desc"><?= e($grp['desc']) ?></div>
                </div>
                <button class="btn btn-primary btn-sm" type="submit">
                  💾 Simpan Bagian Ini
                </button>
              </div>

              <!-- ==============================================================
                   CUSTOM MODULAR VIEW 1: DOMAIN KEAHLIAN (6 KARTU DENGAN SUB-TABS)
                   ============================================================== -->
              <?php if ($id === 'domains'): ?>
                <div class="hp-subtab-bar" role="tablist">
                  <?php foreach ($domainsMeta as $num => $dmeta): ?>
                    <button type="button" 
                            class="hp-subtab-btn domain-subtab-btn <?= $num === 1 ? 'is-active' : '' ?>" 
                            data-domain-target="domain-card-<?= $num ?>">
                      <span><?= $dmeta['icon'] ?></span>
                      <span><?= $num ?>. <?= e($dmeta['name']) ?></span>
                    </button>
                  <?php endforeach; ?>
                </div>

                <?php foreach ($domainsMeta as $num => $dmeta): 
                  $pfx = $dmeta['prefix'];
                ?>
                  <div class="domain-subpanel <?= $num === 1 ? 'is-active' : '' ?>" id="domain-card-<?= $num ?>">
                    <div class="hp-subcard-box">
                      <div class="hp-subcard-title">
                        <span><?= $dmeta['icon'] ?></span> Kartu Domain <?= $num ?>: <?= e($content[$pfx . 'title'] ?? $dmeta['default_title']) ?>
                      </div>

                      <div class="form-grid">
                        <div class="form-group">
                          <label class="form-label" for="<?= $pfx ?>badge">Badge Tag Aksen</label>
                          <input class="form-input" id="<?= $pfx ?>badge" name="<?= $pfx ?>badge" value="<?= e($content[$pfx . 'badge'] ?? '') ?>" placeholder="misal: High-Fidelity UI">
                        </div>
                        <div class="form-group">
                          <label class="form-label" for="<?= $pfx ?>title">Judul Domain</label>
                          <input class="form-input" id="<?= $pfx ?>title" name="<?= $pfx ?>title" value="<?= e($content[$pfx . 'title'] ?? '') ?>" placeholder="misal: Frontend Engineering">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                          <label class="form-label" for="<?= $pfx ?>desc">Deskripsi Singkat Domain</label>
                          <textarea class="form-textarea" id="<?= $pfx ?>desc" name="<?= $pfx ?>desc" rows="2"><?= e($content[$pfx . 'desc'] ?? '') ?></textarea>
                        </div>
                      </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1rem;">
                      <!-- Subgroups Box -->
                      <div class="hp-subcard-box">
                        <div class="hp-subcard-title">🏷️ Subgroup &amp; Daftar Tag Skill</div>
                        
                        <div class="form-group" style="margin-bottom: 0.85rem;">
                          <label class="form-label" for="<?= $pfx ?>grp1_title">Judul Subgroup 1</label>
                          <input class="form-input" id="<?= $pfx ?>grp1_title" name="<?= $pfx ?>grp1_title" value="<?= e($content[$pfx . 'grp1_title'] ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 1.1rem;">
                          <label class="form-label" for="<?= $pfx ?>grp1_tags">Daftar Tag Subgroup 1 (pisahkan koma)</label>
                          <textarea class="form-textarea" id="<?= $pfx ?>grp1_tags" name="<?= $pfx ?>grp1_tags" rows="2"><?= e($content[$pfx . 'grp1_tags'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group" style="margin-bottom: 0.85rem;">
                          <label class="form-label" for="<?= $pfx ?>grp2_title">Judul Subgroup 2</label>
                          <input class="form-input" id="<?= $pfx ?>grp2_title" name="<?= $pfx ?>grp2_title" value="<?= e($content[$pfx . 'grp2_title'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                          <label class="form-label" for="<?= $pfx ?>grp2_tags">Daftar Tag Subgroup 2 (pisahkan koma)</label>
                          <textarea class="form-textarea" id="<?= $pfx ?>grp2_tags" name="<?= $pfx ?>grp2_tags" rows="2"><?= e($content[$pfx . 'grp2_tags'] ?? '') ?></textarea>
                        </div>
                      </div>

                      <!-- Highlights Box -->
                      <div class="hp-subcard-box">
                        <div class="hp-subcard-title">✨ Highlight Poin &amp; Ekosistem</div>
                        
                        <div class="form-group" style="margin-bottom: 0.65rem;">
                          <label class="form-label" for="<?= $pfx ?>b1">Highlight Poin 1</label>
                          <input class="form-input" id="<?= $pfx ?>b1" name="<?= $pfx ?>b1" value="<?= e($content[$pfx . 'b1'] ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 0.65rem;">
                          <label class="form-label" for="<?= $pfx ?>b2">Highlight Poin 2</label>
                          <input class="form-input" id="<?= $pfx ?>b2" name="<?= $pfx ?>b2" value="<?= e($content[$pfx . 'b2'] ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-bottom: 0.85rem;">
                          <label class="form-label" for="<?= $pfx ?>b3">Highlight Poin 3</label>
                          <input class="form-input" id="<?= $pfx ?>b3" name="<?= $pfx ?>b3" value="<?= e($content[$pfx . 'b3'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                          <label class="form-label" for="<?= $pfx ?>eco">Teks Footer Ekosistem</label>
                          <input class="form-input" id="<?= $pfx ?>eco" name="<?= $pfx ?>eco" value="<?= e($content[$pfx . 'eco'] ?? '') ?>">
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>

              <!-- ==============================================================
                   CUSTOM MODULAR VIEW 2: MATRIKS KEAHLIAN TIER 1, 2, 3
                   ============================================================== -->
              <?php elseif ($id === 'matrix'): ?>
                <div class="hp-subcard-box" style="margin-bottom: 1.25rem;">
                  <div class="hp-subcard-title">⚡ Header Matriks</div>
                  <div class="form-grid">
                    <div class="form-group">
                      <label class="form-label" for="matrix_kicker">Kicker Matriks</label>
                      <input class="form-input" id="matrix_kicker" name="matrix_kicker" value="<?= e($content['matrix_kicker'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                      <label class="form-label" for="matrix_title">Judul Matriks</label>
                      <input class="form-input" id="matrix_title" name="matrix_title" value="<?= e($content['matrix_title'] ?? '') ?>">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                      <label class="form-label" for="matrix_desc">Deskripsi Pengantar Matriks</label>
                      <textarea class="form-textarea" id="matrix_desc" name="matrix_desc" rows="2"><?= e($content['matrix_desc'] ?? '') ?></textarea>
                    </div>
                  </div>
                </div>

                <div class="hp-subtab-bar" role="tablist">
                  <button type="button" class="hp-subtab-btn matrix-subtab-btn is-active" data-matrix-target="tier-card-1">
                    <span>🟢</span> Tier 1: Advanced (95%)
                  </button>
                  <button type="button" class="hp-subtab-btn matrix-subtab-btn" data-matrix-target="tier-card-2">
                    <span>🔵</span> Tier 2: Proficient (85%)
                  </button>
                  <button type="button" class="hp-subtab-btn matrix-subtab-btn" data-matrix-target="tier-card-3">
                    <span>🟣</span> Tier 3: Familiar (75%)
                  </button>
                </div>

                <?php 
                $tiersMeta = [
                    1 => ['color' => '🟢', 'name' => 'Tier 1 (Advanced / Production-Ready)', 'prefix' => 'tier1_'],
                    2 => ['color' => '🔵', 'name' => 'Tier 2 (Proficient / Penggunaan Harian)', 'prefix' => 'tier2_'],
                    3 => ['color' => '🟣', 'name' => 'Tier 3 (Familiar / Riset & Utility)', 'prefix' => 'tier3_'],
                ];
                foreach ($tiersMeta as $tnum => $tmeta): 
                  $tpfx = $tmeta['prefix'];
                ?>
                  <div class="matrix-subpanel <?= $tnum === 1 ? 'is-active' : '' ?>" id="tier-card-<?= $tnum ?>">
                    <div class="hp-subcard-box">
                      <div class="hp-subcard-title"><?= $tmeta['color'] ?> <?= $tmeta['name'] ?></div>
                      <div class="form-grid">
                        <div class="form-group">
                          <label class="form-label" for="<?= $tpfx ?>badge">Badge Label</label>
                          <input class="form-input" id="<?= $tpfx ?>badge" name="<?= $tpfx ?>badge" value="<?= e($content[$tpfx . 'badge'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                          <label class="form-label" for="<?= $tpfx ?>level">Keterangan Level &amp; Persentase</label>
                          <input class="form-input" id="<?= $tpfx ?>level" name="<?= $tpfx ?>level" value="<?= e($content[$tpfx . 'level'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                          <label class="form-label" for="<?= $tpfx ?>title">Judul Domain Tier</label>
                          <input class="form-input" id="<?= $tpfx ?>title" name="<?= $tpfx ?>title" value="<?= e($content[$tpfx . 'title'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                          <label class="form-label" for="<?= $tpfx ?>percent">Progress Bar (%) (0-100)</label>
                          <input class="form-input" type="number" min="0" max="100" id="<?= $tpfx ?>percent" name="<?= $tpfx ?>percent" value="<?= e($content[$tpfx . 'percent'] ?? '') ?>">
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                          <label class="form-label" for="<?= $tpfx ?>desc">Deskripsi Kompetensi</label>
                          <textarea class="form-textarea" id="<?= $tpfx ?>desc" name="<?= $tpfx ?>desc" rows="2"><?= e($content[$tpfx . 'desc'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column: 1 / -1;">
                          <label class="form-label" for="<?= $tpfx ?>chips">Daftar Tag Chips (pisahkan dengan koma)</label>
                          <textarea class="form-textarea" id="<?= $tpfx ?>chips" name="<?= $tpfx ?>chips" rows="2"><?= e($content[$tpfx . 'chips'] ?? '') ?></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>

              <!-- ==============================================================
                   CUSTOM MODULAR VIEW 3: ALUR & STANDAR KERJA (DECK)
                   ============================================================== -->
              <?php elseif ($id === 'deck'): ?>
                <div class="hp-subtab-bar" role="tablist">
                  <button type="button" class="hp-subtab-btn deck-subtab-btn is-active" data-deck-target="deck-sub-1">
                    <span>🎯</span> 1. Cara Saya Bekerja
                  </button>
                  <button type="button" class="hp-subtab-btn deck-subtab-btn" data-deck-target="deck-sub-2">
                    <span>🛡️</span> 2. Standar Rekayasa
                  </button>
                  <button type="button" class="hp-subtab-btn deck-subtab-btn" data-deck-target="deck-sub-3">
                    <span>🤝</span> 3. Siap Berkolaborasi
                  </button>
                </div>

                <!-- Deck 1: Cara Bekerja -->
                <div class="deck-subpanel is-active" id="deck-sub-1">
                  <div class="hp-subcard-box">
                    <div class="hp-subcard-title">🎯 Visi Kerja &amp; 3 Langkah</div>
                    <div class="form-grid">
                      <div class="form-group">
                        <label class="form-label" for="deck_vision_eyebrow">Kicker Eyebrow</label>
                        <input class="form-input" id="deck_vision_eyebrow" name="deck_vision_eyebrow" value="<?= e($content['deck_vision_eyebrow'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="deck_vision_title">Judul Visi</label>
                        <input class="form-input" id="deck_vision_title" name="deck_vision_title" value="<?= e($content['deck_vision_title'] ?? '') ?>">
                      </div>
                      <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label" for="deck_vision_lead">Deskripsi Visi</label>
                        <textarea class="form-textarea" id="deck_vision_lead" name="deck_vision_lead" rows="2"><?= e($content['deck_vision_lead'] ?? '') ?></textarea>
                      </div>

                      <div class="form-group">
                        <label class="form-label" for="deck_s1_title">Langkah 01: Judul</label>
                        <input class="form-input" id="deck_s1_title" name="deck_s1_title" value="<?= e($content['deck_s1_title'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="deck_s1_desc">Langkah 01: Deskripsi</label>
                        <input class="form-input" id="deck_s1_desc" name="deck_s1_desc" value="<?= e($content['deck_s1_desc'] ?? '') ?>">
                      </div>

                      <div class="form-group">
                        <label class="form-label" for="deck_s2_title">Langkah 02: Judul</label>
                        <input class="form-input" id="deck_s2_title" name="deck_s2_title" value="<?= e($content['deck_s2_title'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="deck_s2_desc">Langkah 02: Deskripsi</label>
                        <input class="form-input" id="deck_s2_desc" name="deck_s2_desc" value="<?= e($content['deck_s2_desc'] ?? '') ?>">
                      </div>

                      <div class="form-group">
                        <label class="form-label" for="deck_s3_title">Langkah 03: Judul</label>
                        <input class="form-input" id="deck_s3_title" name="deck_s3_title" value="<?= e($content['deck_s3_title'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="deck_s3_desc">Langkah 03: Deskripsi</label>
                        <input class="form-input" id="deck_s3_desc" name="deck_s3_desc" value="<?= e($content['deck_s3_desc'] ?? '') ?>">
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Deck 2: Standar Rekayasa -->
                <div class="deck-subpanel" id="deck-sub-2">
                  <div class="hp-subcard-box">
                    <div class="hp-subcard-title">🛡️ Standar Rekayasa (3 Pilar)</div>
                    <div class="form-group" style="margin-bottom: 1rem;">
                      <label class="form-label" for="deck_std_title">Judul Section Standar</label>
                      <input class="form-input" id="deck_std_title" name="deck_std_title" value="<?= e($content['deck_std_title'] ?? '') ?>">
                    </div>

                    <div class="form-grid">
                      <div class="form-group" style="grid-column: 1 / -1; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 0.75rem;">
                        <strong>Pilar 1</strong>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="deck_std1_icon">Pilar 1: Ikon Emoji</label>
                        <input class="form-input" id="deck_std1_icon" name="deck_std1_icon" value="<?= e($content['deck_std1_icon'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="deck_std1_title">Pilar 1: Judul</label>
                        <input class="form-input" id="deck_std1_title" name="deck_std1_title" value="<?= e($content['deck_std1_title'] ?? '') ?>">
                      </div>
                      <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label" for="deck_std1_desc">Pilar 1: Deskripsi</label>
                        <input class="form-input" id="deck_std1_desc" name="deck_std1_desc" value="<?= e($content['deck_std1_desc'] ?? '') ?>">
                      </div>

                      <div class="form-group" style="grid-column: 1 / -1; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 0.75rem;">
                        <strong>Pilar 2</strong>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="deck_std2_icon">Pilar 2: Ikon Emoji</label>
                        <input class="form-input" id="deck_std2_icon" name="deck_std2_icon" value="<?= e($content['deck_std2_icon'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="deck_std2_title">Pilar 2: Judul</label>
                        <input class="form-input" id="deck_std2_title" name="deck_std2_title" value="<?= e($content['deck_std2_title'] ?? '') ?>">
                      </div>
                      <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label" for="deck_std2_desc">Pilar 2: Deskripsi</label>
                        <input class="form-input" id="deck_std2_desc" name="deck_std2_desc" value="<?= e($content['deck_std2_desc'] ?? '') ?>">
                      </div>

                      <div class="form-group" style="grid-column: 1 / -1; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 0.75rem;">
                        <strong>Pilar 3</strong>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="deck_std3_icon">Pilar 3: Ikon Emoji</label>
                        <input class="form-input" id="deck_std3_icon" name="deck_std3_icon" value="<?= e($content['deck_std3_icon'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="deck_std3_title">Pilar 3: Judul</label>
                        <input class="form-input" id="deck_std3_title" name="deck_std3_title" value="<?= e($content['deck_std3_title'] ?? '') ?>">
                      </div>
                      <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label" for="deck_std3_desc">Pilar 3: Deskripsi</label>
                        <input class="form-input" id="deck_std3_desc" name="deck_std3_desc" value="<?= e($content['deck_std3_desc'] ?? '') ?>">
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Deck 3: Kolaborasi -->
                <div class="deck-subpanel" id="deck-sub-3">
                  <div class="hp-subcard-box">
                    <div class="hp-subcard-title">🤝 Siap Berkolaborasi</div>
                    <div class="form-grid">
                      <div class="form-group">
                        <label class="form-label" for="deck_collab_title">Judul Kolaborasi</label>
                        <input class="form-input" id="deck_collab_title" name="deck_collab_title" value="<?= e($content['deck_collab_title'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="deck_collab_btn_text">Teks Tombol Aksi</label>
                        <input class="form-input" id="deck_collab_btn_text" name="deck_collab_btn_text" value="<?= e($content['deck_collab_btn_text'] ?? '') ?>">
                      </div>

                      <div class="form-group">
                        <label class="form-label" for="deck_collab_role_lbl">Label Fokus Peran</label>
                        <input class="form-input" id="deck_collab_role_lbl" name="deck_collab_role_lbl" value="<?= e($content['deck_collab_role_lbl'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="deck_collab_role_val">Nilai Fokus Peran</label>
                        <input class="form-input" id="deck_collab_role_val" name="deck_collab_role_val" value="<?= e($content['deck_collab_role_val'] ?? '') ?>">
                      </div>

                      <div class="form-group">
                        <label class="form-label" for="deck_collab_avail_lbl">Label Ketersediaan</label>
                        <input class="form-input" id="deck_collab_avail_lbl" name="deck_collab_avail_lbl" value="<?= e($content['deck_collab_avail_lbl'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="deck_collab_avail_val">Nilai Ketersediaan</label>
                        <input class="form-input" id="deck_collab_avail_val" name="deck_collab_avail_val" value="<?= e($content['deck_collab_avail_val'] ?? '') ?>">
                      </div>

                      <div class="form-group">
                        <label class="form-label" for="deck_collab_mode_lbl">Label Cara Kerja</label>
                        <input class="form-input" id="deck_collab_mode_lbl" name="deck_collab_mode_lbl" value="<?= e($content['deck_collab_mode_lbl'] ?? '') ?>">
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="deck_collab_mode_val">Nilai Cara Kerja</label>
                        <input class="form-input" id="deck_collab_mode_val" name="deck_collab_mode_val" value="<?= e($content['deck_collab_mode_val'] ?? '') ?>">
                      </div>
                    </div>
                  </div>
                </div>

              <!-- ==============================================================
                   GENERAL VIEW FOR OTHER SECTIONS
                   ============================================================== -->
              <?php else: ?>
                <div class="form-grid">
                  <?php foreach ($grp['keys'] as $key): 
                    $isJson = ($key === 'tech_chips_json');
                    $isDesc = str_contains($key, '_desc') || str_contains($key, 'description') || str_contains($key, '_tags') || str_contains($key, 'text_') || str_contains($key, 'paragraph') || str_contains($key, '_lead');
                    $long = $isJson || $isDesc;
                  ?>
                    <div class="form-group" style="<?= $long ? 'grid-column: 1 / -1;' : '' ?>">
                      <label class="form-label" for="<?= e($key) ?>">
                        <?= e($labels[$key] ?? $key) ?>
                        <code style="font-size: 0.7rem; color: var(--color-text-faint); margin-left: 0.35rem; font-weight: 400;">(<?= e($key) ?>)</code>
                      </label>
                      
                      <?php if ($isJson): ?>
                        <div style="font-size: 0.75rem; color: var(--color-text-dim); margin-bottom: 0.5rem;">
                          💡 <strong>Format JSON Array Skill Chips:</strong> Berisi objek dengan properti <code>name</code>, <code>category</code> (<code>frontend</code>, <code>backend</code>, <code>ai</code>, <code>database</code>, <code>devops</code>, <code>testing</code>, <code>mobile</code>, <code>analytics</code>), <code>role</code>, <code>highlight</code> (<code>true</code>/<code>false</code>), dan <code>icon</code>.
                        </div>
                        <textarea class="form-textarea" 
                                  id="<?= e($key) ?>" 
                                  name="<?= e($key) ?>" 
                                  rows="14" 
                                  style="font-family: 'JetBrains Mono', 'Fira Code', Consolas, monospace; font-size: 0.8rem; background: rgba(10,14,23,0.85); color: #38bdf8;"
                                  placeholder="[ { &quot;name&quot;: &quot;Next.js&quot;, ... } ]"><?= e($content[$key] ?? '') ?></textarea>
                      <?php elseif ($long): ?>
                        <textarea class="form-textarea" 
                                  id="<?= e($key) ?>" 
                                  name="<?= e($key) ?>" 
                                  rows="<?= (str_contains($key, 'paragraph') || str_contains($key, '_tags')) ? '3' : '2' ?>" 
                                  placeholder="Masukkan teks..."><?= e($content[$key] ?? '') ?></textarea>
                      <?php else: ?>
                        <input class="form-input" 
                               id="<?= e($key) ?>" 
                               name="<?= e($key) ?>" 
                               value="<?= e($content[$key] ?? '') ?>" 
                               <?= ($key === 'hero_readiness') ? 'type="number" min="0" max="100"' : 'type="text"' ?>
                               placeholder="Masukkan nilai...">
                      <?php endif; ?>

                      <?php if ($key === 'google_client_id' || $key === 'google_client_secret'): ?>
                        <div style="font-size: 0.72rem; color: var(--color-text-dim); margin-top: 0.35rem;">
                          Diambil dari Google Cloud Console &gt; APIs &amp; Services &gt; Credentials (OAuth 2.0 Client IDs). Jika dikosongkan, tombol login Google di Buku Tamu akan otomatis disembunyikan secara aman.
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <!-- Bottom Navigation Bar inside each Panel -->
              <div class="hp-tab-nav-footer">
                <div>
                  <?php if ($prevId): ?>
                    <button type="button" class="btn btn-secondary btn-sm btn-nav-jump" data-jump-target="panel-<?= e($prevId) ?>">
                      &larr; <?= e($groups[$prevId]['title']) ?>
                    </button>
                  <?php endif; ?>
                </div>
                
                <div style="display: flex; gap: 0.75rem;">
                  <button class="btn btn-primary btn-sm" type="submit">
                    💾 Simpan Perubahan
                  </button>
                  <?php if ($nextId): ?>
                    <button type="button" class="btn btn-secondary btn-sm btn-nav-jump" data-jump-target="panel-<?= e($nextId) ?>">
                      <?= e($groups[$nextId]['title']) ?> &rarr;
                    </button>
                  <?php endif; ?>
                </div>
              </div>

            </div>
          </div>
        <?php 
          $groupIndex++;
        endforeach; 
        ?>
      </div>

    </main>
  </div>

</form>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const navButtons = document.querySelectorAll('.hp-nav-item-btn');
    const panels = document.querySelectorAll('.hp-section-panel');
    const activeLabel = document.getElementById('currentSectionTitle');
    const toggleShowAll = document.getElementById('toggleShowAll');
    const editorShell = document.querySelector('.hp-editor-shell');
    const jumpButtons = document.querySelectorAll('.btn-nav-jump');
    const searchInput = document.getElementById('hpMenuSearch');

    function activateTab(targetPanelId, titleHtml) {
      if (toggleShowAll.checked) return;

      navButtons.forEach(btn => {
        const isTarget = btn.getAttribute('data-tab-target') === targetPanelId;
        btn.classList.toggle('is-active', isTarget);
      });

      panels.forEach(p => {
        p.classList.toggle('is-active', p.id === targetPanelId);
      });

      if (titleHtml && activeLabel) {
        activeLabel.innerHTML = titleHtml;
      }

      // Update URL hash without reload
      if (history.replaceState) {
        history.replaceState(null, null, '#' + targetPanelId.replace('panel-', ''));
      }
    }

    navButtons.forEach(btn => {
      btn.addEventListener('click', function() {
        const target = this.getAttribute('data-tab-target');
        const title = this.getAttribute('data-tab-title');
        activateTab(target, title);
        window.scrollTo({ top: document.querySelector('.hp-sticky-bar').offsetTop - 20, behavior: 'smooth' });
      });
    });

    jumpButtons.forEach(btn => {
      btn.addEventListener('click', function() {
        const target = this.getAttribute('data-jump-target');
        const matchingBtn = document.querySelector(`.hp-nav-item-btn[data-tab-target="${target}"]`);
        const title = matchingBtn ? matchingBtn.getAttribute('data-tab-title') : '';
        activateTab(target, title);
        window.scrollTo({ top: document.querySelector('.hp-sticky-bar').offsetTop - 20, behavior: 'smooth' });
      });
    });

    // Domain Sub-Tabs
    const domainSubtabBtns = document.querySelectorAll('.domain-subtab-btn');
    const domainSubpanels = document.querySelectorAll('.domain-subpanel');
    domainSubtabBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        domainSubtabBtns.forEach(b => b.classList.remove('is-active'));
        domainSubpanels.forEach(p => p.classList.remove('is-active'));
        this.classList.add('is-active');
        const target = document.getElementById(this.getAttribute('data-domain-target'));
        if (target) target.classList.add('is-active');
      });
    });

    // Matrix Sub-Tabs
    const matrixSubtabBtns = document.querySelectorAll('.matrix-subtab-btn');
    const matrixSubpanels = document.querySelectorAll('.matrix-subpanel');
    matrixSubtabBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        matrixSubtabBtns.forEach(b => b.classList.remove('is-active'));
        matrixSubpanels.forEach(p => p.classList.remove('is-active'));
        this.classList.add('is-active');
        const target = document.getElementById(this.getAttribute('data-matrix-target'));
        if (target) target.classList.add('is-active');
      });
    });

    // Deck Sub-Tabs
    const deckSubtabBtns = document.querySelectorAll('.deck-subtab-btn');
    const deckSubpanels = document.querySelectorAll('.deck-subpanel');
    deckSubtabBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        deckSubtabBtns.forEach(b => b.classList.remove('is-active'));
        deckSubpanels.forEach(p => p.classList.remove('is-active'));
        this.classList.add('is-active');
        const target = document.getElementById(this.getAttribute('data-deck-target'));
        if (target) target.classList.add('is-active');
      });
    });

    // Search filter in subnav
    searchInput.addEventListener('input', function() {
      const q = this.value.toLowerCase().trim();
      const groups = document.querySelectorAll('.hp-cluster-group');

      groups.forEach(grp => {
        let visibleInGroup = 0;
        const items = grp.querySelectorAll('.hp-nav-item-btn');
        items.forEach(item => {
          const kw = item.getAttribute('data-keywords') || '';
          if (!q || kw.includes(q)) {
            item.style.display = 'flex';
            visibleInGroup++;
          } else {
            item.style.display = 'none';
          }
        });
        grp.style.display = visibleInGroup > 0 ? 'block' : 'none';
      });
    });

    toggleShowAll.addEventListener('change', function() {
      if (this.checked) {
        editorShell.classList.add('hp-show-all');
        if (activeLabel) activeLabel.innerHTML = '<span>📑</span> Menampilkan Semua Bagian';
        domainSubpanels.forEach(p => p.classList.add('is-active'));
        matrixSubpanels.forEach(p => p.classList.add('is-active'));
        deckSubpanels.forEach(p => p.classList.add('is-active'));
      } else {
        editorShell.classList.remove('hp-show-all');
        const activeBtn = document.querySelector('.hp-nav-item-btn.is-active') || navButtons[0];
        if (activeBtn) {
          activateTab(activeBtn.getAttribute('data-tab-target'), activeBtn.getAttribute('data-tab-title'));
        }
        domainSubpanels.forEach((p, i) => p.classList.toggle('is-active', i === 0));
        matrixSubpanels.forEach((p, i) => p.classList.toggle('is-active', i === 0));
        deckSubpanels.forEach((p, i) => p.classList.toggle('is-active', i === 0));
      }
    });

    // Check URL hash on initial load
    const hash = window.location.hash.replace('#', '');
    if (hash) {
      const targetBtn = document.querySelector(`.hp-nav-item-btn[data-tab-target="panel-${hash}"]`);
      if (targetBtn) {
        activateTab('panel-' + hash, targetBtn.getAttribute('data-tab-title'));
      }
    }
  });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
