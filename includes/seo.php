<?php
/** Public SEO/GEO metadata: server-rendered for crawlers and AI search. */
function render_seo(array $profile, string $title, string $description, string $path = '/', string $type = 'website'): void
{
    $base = rtrim(BASE_URL, '/');
    $canonical = $base . ($path === '/' ? '/' : '/' . ltrim($path, '/'));
    $name = $profile['full_name'] ?? 'Ammar Syarif';
    $image = !empty($profile['photo']) ? $base . '/assets/uploads/photos/' . rawurlencode(basename($profile['photo'])) : '';
    $person = ['@context'=>'https://schema.org','@graph'=>[
        ['@type'=>'WebSite','@id'=>$base . '/#website','url'=>$base . '/','name'=>$name . ' — Portfolio','inLanguage'=>'id-ID'],
        ['@type'=>'Person','@id'=>$base . '/#person','name'=>$name,'jobTitle'=>$profile['role_title'] ?? 'Full-Stack Web Developer','description'=>$profile['tagline'] ?? $description,'url'=>$base . '/','sameAs'=>array_values(array_filter([$profile['linkedin'] ?? '', $profile['github'] ?? '', $profile['instagram'] ?? '', $profile['tiktok'] ?? '']))],
        ['@type'=>'WebPage','@id'=>$canonical . '#webpage','url'=>$canonical,'name'=>$title,'description'=>$description,'inLanguage'=>'id-ID','isPartOf'=>['@id'=>$base . '/#website'],'about'=>['@id'=>$base . '/#person']]
    ]];
    echo '<link rel="canonical" href="' . e($canonical) . '">' . PHP_EOL;
    echo '<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">' . PHP_EOL;
    echo '<meta property="og:type" content="' . e($type) . '"><meta property="og:locale" content="id_ID">' . PHP_EOL;
    echo '<meta property="og:title" content="' . e($title) . '"><meta property="og:description" content="' . e($description) . '"><meta property="og:url" content="' . e($canonical) . '">' . PHP_EOL;
    if ($image !== '') echo '<meta property="og:image" content="' . e($image) . '"><meta name="twitter:card" content="summary_large_image">' . PHP_EOL;
    echo '<script type="application/ld+json">' . json_encode($person, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . PHP_EOL;
}
