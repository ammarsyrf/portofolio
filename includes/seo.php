<?php
/**
 * Public SEO & GEO Metadata Generator
 * Dioptimalkan untuk Google Search, Google Knowledge Graph, AI Crawlers, dan GEO Tagging
 * Target Kata Kunci: "Ammar Syarif", "zentokun90", "zenerie"
 */

function render_seo(
    array $profile,
    string $title,
    string $description,
    string $path = '/',
    string $type = 'website',
    array $breadcrumbs = []
): void {
    $base = rtrim(BASE_URL, '/');
    $canonical = $base . ($path === '/' ? '/' : '/' . ltrim($path, '/'));
    $name = !empty($profile['full_name']) ? $profile['full_name'] : 'Ammar Syarif';
    $role = !empty($profile['role_title']) ? $profile['role_title'] : 'Web Developer & Data Analyst';
    $tagline = !empty($profile['tagline']) ? $profile['tagline'] : 'Membangun sistem yang rapi dan mengubah data jadi keputusan.';
    $fullDesc = !empty($description) ? $description : ($name . ' — ' . $role . '. ' . $tagline);

    // URL Foto/Avatar untuk Open Graph & Twitter Card
    $image = '';
    if (!empty($profile['photo'])) {
        $photoPath = UPLOAD_DIR_PHOTOS . '/' . basename($profile['photo']);
        if (file_exists($photoPath)) {
            $image = upload_url('photos', $profile['photo']);
        }
    }
    if (empty($image)) {
        // Fallback placeholder / icon jika belum upload foto
        $image = $base . '/assets/img/og-preview.png';
    }

    // Daftar kanal sosial resmi untuk Google Entity Knowledge Graph (sameAs)
    $sameAs = array_values(array_filter([
        $profile['github'] ?? 'https://github.com/ammarsyrf',
        $profile['linkedin'] ?? 'https://linkedin.com/in/zentokun90',
        $profile['instagram'] ?? 'https://instagram.com/zentokun90',
        $profile['tiktok'] ?? 'https://tiktok.com/@zentokun90',
        'https://zen.zenerie.my.id/',
        'https://github.com/ammarsyrf',
        'https://tiktok.com/@zentokun90'
    ]));
    $sameAs = array_values(array_unique($sameAs));

    // Keyword list berbobot tinggi untuk Ammar Syarif, ZenS, ammarsyrf, dan zenerie
    $keywords = [
        'Ammar Syarif',
        'ZenS',
        'ammarsyrf',
        'zentokun90',
        'zenerie',
        'Zenerie',
        'Zeneriem',
        'zen.zenerie.my.id',
        'Portofolio Ammar Syarif',
        'Ammar Syarif Web Developer',
        'Ammar Syarif Data Analyst',
        'Full-Stack Developer Indonesia',
        'Software Engineer Indonesia',
        'S1 Sistem Informasi',
        'Laravel Developer',
        'Flutter Developer',
        'PHP Native MySQL'
    ];

    // Schema.org Graph (Semantic Knowledge Graph untuk Google)
    $schemaGraph = [
        '@context' => 'https://schema.org',
        '@graph' => [
            // Entitas Person (Ammar Syarif / ZenS / ammarsyrf)
            [
                '@type' => 'Person',
                '@id' => $base . '/#person',
                'name' => $name,
                'alternateName' => ['ZenS', 'ammarsyrf', 'zentokun90', 'Zenerie', 'Ammar', 'Zen', 'zeneriem'],
                'jobTitle' => $role,
                'description' => $tagline,
                'url' => $base . '/',
                'image' => $image,
                'sameAs' => $sameAs,
                'nationality' => [
                    '@type' => 'Country',
                    'name' => 'Indonesia'
                ],
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressCountry' => 'ID',
                    'addressLocality' => 'Indonesia'
                ],
                'alumniOf' => [
                    '@type' => 'EducationalOrganization',
                    'name' => 'S1 Sistem Informasi'
                ],
                'knowsAbout' => [
                    'Web Development',
                    'Data Analysis',
                    'Full-Stack Engineering',
                    'PHP',
                    'Laravel',
                    'MySQL',
                    'Flutter',
                    'Algorithm C4.5',
                    'Zenerie'
                ]
            ],
            // Entitas Brand / Studio Organisasi (Zenerie)
            [
                '@type' => 'Organization',
                '@id' => $base . '/#organization',
                'name' => 'Zenerie',
                'alternateName' => ['Zenerie Studio', 'Zeneriem', 'zen.zenerie.my.id'],
                'url' => $base . '/',
                'logo' => $image,
                'founder' => [
                    '@id' => $base . '/#person'
                ]
            ],
            // Entitas WebSite
            [
                '@type' => 'WebSite',
                '@id' => $base . '/#website',
                'url' => $base . '/',
                'name' => $name . ' (zentokun90) — Zenerie Portfolio',
                'alternateName' => ['Zenerie', 'zentokun90 Portfolio', 'Portofolio Ammar Syarif'],
                'description' => $tagline,
                'inLanguage' => ['id-ID', 'en-US'],
                'publisher' => [
                    '@id' => $base . '/#person'
                ]
            ],
            // Entitas WebPage Spesifik
            [
                '@type' => ($path === '/links' ? 'ProfilePage' : 'WebPage'),
                '@id' => $canonical . '#webpage',
                'url' => $canonical,
                'name' => $title,
                'description' => $fullDesc,
                'inLanguage' => 'id-ID',
                'isPartOf' => [
                    '@id' => $base . '/#website'
                ],
                'about' => [
                    '@id' => $base . '/#person'
                ]
            ]
        ]
    ];

    // Tambahkan BreadcrumbList jika halaman bukan root
    if (!empty($breadcrumbs)) {
        $itemList = [];
        $pos = 1;
        $itemList[] = [
            '@type' => 'ListItem',
            'position' => $pos++,
            'name' => 'Beranda',
            'item' => $base . '/'
        ];
        foreach ($breadcrumbs as $bName => $bPath) {
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $pos++,
                'name' => $bName,
                'item' => $base . ($bPath === '/' ? '/' : '/' . ltrim($bPath, '/'))
            ];
        }
        $schemaGraph['@graph'][] = [
            '@type' => 'BreadcrumbList',
            '@id' => $canonical . '#breadcrumb',
            'itemListElement' => $itemList
        ];
    }
    ?>
  <!-- ==================== Favicons & App Icons ==================== -->
  <link rel="icon" type="image/svg+xml" href="<?= asset('img/favicon.svg') ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= asset('img/favicon-32x32.png') ?>">
  <link rel="icon" type="image/png" sizes="64x64" href="<?= asset('img/favicon.png') ?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= asset('img/apple-touch-icon.png') ?>">

  <!-- ==================== SEO & Canonical ==================== -->
  <link rel="canonical" href="<?= e($canonical) ?>">
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
  <meta name="keywords" content="<?= e(implode(', ', $keywords)) ?>">
  <meta name="author" content="<?= e($name) ?> (ZenS / @ammarsyrf)">
  <meta name="publisher" content="Zenerie">

  <!-- ==================== GEO & Local Search ==================== -->
  <meta name="geo.region" content="ID">
  <meta name="geo.placename" content="Indonesia">
  <meta name="geo.position" content="-6.2088;106.8456">
  <meta name="ICBM" content="-6.2088, 106.8456">

  <!-- ==================== Open Graph (Facebook / WhatsApp / LinkedIn) ==================== -->
  <meta property="og:site_name" content="Zenerie — <?= e($name) ?> (@ammarsyrf)">
  <meta property="og:type" content="<?= e($type) ?>">
  <meta property="og:locale" content="id_ID">
  <meta property="og:title" content="<?= e($title) ?>">
  <meta property="og:description" content="<?= e($fullDesc) ?>">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <?php if (!empty($image)): ?>
  <meta property="og:image" content="<?= e($image) ?>">
  <meta property="og:image:alt" content="Portofolio <?= e($name) ?> (ZenS / ammarsyrf) - Zenerie">
  <?php endif; ?>

  <!-- ==================== Twitter Cards ==================== -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:site" content="@ammarsyrf">
  <meta name="twitter:creator" content="@ammarsyrf">
  <meta name="twitter:title" content="<?= e($title) ?>">
  <meta name="twitter:description" content="<?= e($fullDesc) ?>">
  <?php if (!empty($image)): ?>
  <meta name="twitter:image" content="<?= e($image) ?>">
  <meta name="twitter:image:alt" content="Portofolio <?= e($name) ?> (ZenS / ammarsyrf) - Zenerie">
  <?php endif; ?>

  <!-- ==================== Schema.org Knowledge Graph (JSON-LD) ==================== -->
  <script type="application/ld+json">
  <?= json_encode($schemaGraph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
  </script>
<?php
}
