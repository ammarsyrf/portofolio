<?php
/**
 * AJAX API: Toggle Hire / Work Availability Status
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$status = $_POST['status'] ?? '';
if (!in_array($status, ['available', 'busy'], true)) {
    // If empty or toggle requested, switch current status
    $curr = get_hire_status($pdo);
    $status = ($curr['status'] === 'available') ? 'busy' : 'available';
}

try {
    set_hire_status($pdo, $status);
    $hire = get_hire_status($pdo);
    echo json_encode([
        'status'      => 'success',
        'hire_status' => $hire['status'],
        'label'       => $hire['label'],
        'short'       => $hire['short'],
        'color'       => $hire['color'],
        'badge'       => $hire['badge'],
    ]);
} catch (\Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit;
