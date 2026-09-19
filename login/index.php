<?php
/**
 * Login Directory Entry Point
 * Mengalihkan akses dari /login/ langsung ke Portal Administrator (/admin/login.php)
 */
require_once __DIR__ . '/../config.php';
header('Location: ' . BASE_URL . '/admin/login.php');
exit;
