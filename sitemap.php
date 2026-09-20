<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/xml; charset=UTF-8');

$base = rtrim(BASE_URL, '/');
$routes = [
    ['path' => '/',             'priority' => '1.0', 'freq' => 'daily'],
    ['path' => '/links',        'priority' => '0.9', 'freq' => 'weekly'],
    ['path' => '/achievements', 'priority' => '0.8', 'freq' => 'monthly'],
    ['path' => '/creations',    'priority' => '0.8', 'freq' => 'weekly'],
    ['path' => '/guestbook',    'priority' => '0.8', 'freq' => 'daily']
];

$today = date('Y-m-d');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($routes as $r): ?>
  <url>
    <loc><?= htmlspecialchars($base . $r['path'], ENT_XML1) ?></loc>
    <lastmod><?= $today ?></lastmod>
    <changefreq><?= $r['freq'] ?></changefreq>
    <priority><?= $r['priority'] ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
