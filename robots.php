<?php
require_once __DIR__ . '/config.php';
header('Content-Type: text/plain; charset=UTF-8');
echo "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /auth/\nDisallow: /login/\n\nSitemap: " . rtrim(BASE_URL, '/') . "/sitemap.xml\n";
