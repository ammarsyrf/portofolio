<?php
require_once __DIR__ . '/config.php';
header('Content-Type: text/plain; charset=UTF-8');

$base = rtrim(BASE_URL, '/');

echo "User-agent: *\n";
echo "Allow: /\n";
echo "Allow: /links\n";
echo "Allow: /achievements\n";
echo "Allow: /creations\n";
echo "Allow: /guestbook\n";
echo "Disallow: /admin/\n";
echo "Disallow: /auth/\n";
echo "Disallow: /login/\n";
echo "Disallow: /scratch/\n";
echo "\n";
echo "User-agent: Googlebot\n";
echo "Allow: /\n";
echo "\n";
echo "User-agent: Googlebot-Image\n";
echo "Allow: /assets/uploads/\n";
echo "\n";
echo "Sitemap: " . $base . "/sitemap.xml\n";
