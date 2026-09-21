<?php
/**
 * Export Visitor Analytics to CSV
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$range = $_GET['range'] ?? '30';
$limit = 1000;

if ($range === '7') {
    $where = "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($range === 'all') {
    $where = "";
    $limit = 5000;
} else {
    $where = "WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
}

$stmt = $pdo->query("
    SELECT id, created_at, ip_address, country, city, device, browser, os, page, referer
    FROM page_views
    {$where}
    ORDER BY id DESC
    LIMIT {$limit}
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filename = 'porto_visitor_analytics_' . date('Y-m-d_His') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// UTF-8 BOM for Excel compatibility
fputs($output, "\xEF\xBB\xBF");

// CSV Header
fputcsv($output, ['ID', 'Waktu (WIB)', 'IP Address', 'Negara', 'Kota', 'Perangkat', 'Browser', 'Sistem Operasi', 'Halaman Dikunjungi', 'Sumber Referer']);

foreach ($rows as $row) {
    fputcsv($output, [
        $row['id'],
        $row['created_at'],
        mask_ip($row['ip_address']),
        $row['country'] ?: 'Unknown',
        $row['city'] ?: 'Unknown',
        $row['device'] ?: 'Desktop',
        $row['browser'] ?: 'Unknown',
        $row['os'] ?: 'Unknown',
        $row['page'] ?: '/',
        $row['referer'] ?: 'Direct / Organic',
    ]);
}

fclose($output);
exit;
