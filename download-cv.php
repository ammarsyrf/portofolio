<?php
/**
 * CV / Resume Secure Downloader & Telemetry Tracker
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$profile = get_profile($pdo);

if (empty($profile['cv_file'])) {
    http_response_code(404);
    die("Berkas CV belum diunggah oleh administrator.");
}

$filename = basename($profile['cv_file']);
$filepath = UPLOAD_DIR_CV . '/' . $filename;

if (!file_exists($filepath)) {
    http_response_code(404);
    die("Berkas CV fisik tidak ditemukan di server.");
}

// Track download event
try {
    track_cv_download($pdo);
} catch (\Throwable $e) {
    // Ignore tracker error so download is not disrupted
}

// Serve file download
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $filepath) ?: 'application/pdf';
finfo_close($finfo);

header('Content-Type: ' . $mimeType);
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

readfile($filepath);
exit;
