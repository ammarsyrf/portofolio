<?php
/**
 * AJAX API: Save Admin Scratchpad Notes
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$content = $_POST['notes'] ?? '';

try {
    save_admin_notes($pdo, $content);
    echo json_encode(['status' => 'success', 'message' => 'Catatan tersimpan']);
} catch (\Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit;
