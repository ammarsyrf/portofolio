<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/xml; charset=UTF-8');
$base = rtrim(BASE_URL, '/');
$urls = ['/', '/achievements', '/creations', '/links'];
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
foreach ($urls as $path) echo '<url><loc>' . htmlspecialchars($base . $path, ENT_XML1) . '</loc><changefreq>weekly</changefreq><priority>' . ($path === '/' ? '1.0' : '0.7') . '</priority></url>';
echo '</urlset>';
