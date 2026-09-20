<?php
/**
 * Salinan statis halaman utama. Disimpan sebagai satu dokumen JSON di
 * site_settings agar dapat dikelola dari admin tanpa mengubah source code.
 */
function homepage_content_defaults(): array
{
    return [
        'hero_education_label' => 'Pendidikan',
        'hero_degree' => 'S1',
        'hero_field' => 'Sistem Informasi',
        'hero_specialty_label' => 'Spesialisasi',
        'hero_specialty_value' => 'Web & Analitik Data',
        'hero_availability_label' => 'Ketersediaan',
        'hero_availability_value' => '● Siap Rekrutmen',
        'hero_readiness' => '88',
        'hero_line_one' => "let's",
        'hero_line_two' => 'code +',
        'hero_line_three' => 'systems',
        'hero_badge' => 'ammar syarif',
        'hero_vertical_badge' => '01. My Profile ♡',
        'brand_title' => 'Our Brand',
        'brand_badge' => 'Zenerie',
        'brand_description' => 'Zenerie adalah brand kreatif yang kami rintis untuk menghadirkan identitas dan pengalaman digital yang berkesan.',
        'brand_url' => 'https://www.zenerie.my.id',
        'brand_button' => 'Kunjungi Zenerie',
        'identity_button' => 'View Profile',
        'media_title' => 'Media',
        'media_button' => 'See All ➔',
        'tech_widget_title' => 'Tech Stack',
        'tech_widget_status' => '● Active',
        'tech_widget_footer' => '✦ Teruji di Produksi & Riset',
        'tech_widget_button' => 'Semua Skill ➔',
        'projects_caption' => 'Koleksi Proyek',
        'projects_title' => 'Hasil Karya & Sistem Bisnis',
        'about_widget_title' => 'About Me',
        'about_widget_status' => 'S1 Sistem Informasi',
        'about_widget_text_one' => 'Lulusan S1 Sistem Informasi dengan passion mendalam pada arsitektur web modern, rekayasa fullstack yang tangguh, serta pemodelan analitik data cerdas.',
        'about_widget_text_two' => 'Berpengalaman merancang dan membangun sistem bisnis end-to-end dari nol: mulai dari perancangan database relasional, efisiensi server, hingga antarmuka siap pakai di tingkat produksi.',
        'about_metric_one_value' => '7+',
        'about_metric_one_label' => 'Sistem Nyata',
        'about_metric_two_value' => '100%',
        'about_metric_two_label' => 'Bebas SQLi',
        'about_caption' => 'Tentang Saya',
        'about_title' => 'Membangun Sistem yang Andal dari Hulu ke Hilir',
        'about_kicker' => 'Full-Stack Web Developer',
        'about_heading' => 'Dari rancangan sistem hingga aplikasi siap digunakan.',
        'about_paragraph_one' => 'Saya adalah seorang Full-Stack Web Developer yang berfokus pada pengembangan aplikasi web menggunakan PHP, Laravel, MySQL, React, dan Next.js. Saya terbiasa mengembangkan website dan sistem berbasis web mulai dari perancangan database, pembuatan REST API, implementasi fitur backend dan frontend, hingga proses deployment ke server.',
        'about_paragraph_two' => 'Dalam pengembangan aplikasi, saya tidak hanya berfokus pada tampilan dan fungsi, tetapi juga memperhatikan struktur sistem, performa, keamanan, skalabilitas, dan kemudahan maintenance. Saya juga memiliki pengalaman dalam penggunaan Git, Linux, VPS, Nginx, Docker, Redis, queue, serta integrasi berbagai layanan dan API.',
        'about_paragraph_three' => 'Saya memiliki ketertarikan besar pada pengembangan sistem yang efisien, otomatis, dan dapat menyelesaikan kebutuhan nyata pengguna. Saat ini saya terus memperdalam kemampuan di bidang full-stack development, system architecture, dan server infrastructure dengan tujuan berkembang sebagai Web Developer profesional dan dapat berkontribusi pada berbagai project secara remote maupun kolaboratif.',
        'skills_caption' => 'Kompetensi & Toolkit',
        'skills_title' => 'Keahlian Teknis & Domain Kerja',
        'skills_lead' => 'Kombinasi rekayasa backend modular, performa antarmuka bersih tanpa bloatware, serta pemodelan analitik data berbasis riset sistem informasi yang telah teruji pada berbagai aplikasi produksi nyata.',
        'contact_caption' => 'Jalur Komunikasi',
        'contact_title' => 'Mari Membangun Solusi Bersama',
        'contact_email_title' => 'Mulai percakapan profesional',
        'contact_email_description' => 'Untuk kolaborasi, rekrutmen, dan diskusi proyek',
        'contact_linkedin_title' => 'Terhubung di LinkedIn',
        'contact_linkedin_description' => 'Lihat profil dan pengalaman',
        'contact_github_title' => 'Jelajahi GitHub',
        'contact_github_description' => 'Repository dan eksperimen',
        'contact_form_title' => 'Ceritakan kebutuhan proyekmu',
        'contact_form_button' => 'Kirim melalui Email',
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
    $content['hero_readiness'] = (string) max(0, min(100, (int) $content['hero_readiness']));
    return $content;
}

function save_homepage_content(PDO $pdo, array $input): void
{
    $content = homepage_content_defaults();
    foreach ($content as $key => $default) {
        $value = trim((string) ($input[$key] ?? $default));
        $content[$key] = $value === '' ? $default : $value;
    }
    $content['hero_readiness'] = (string) max(0, min(100, (int) $content['hero_readiness']));
    set_setting($pdo, 'homepage_content', json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
}
