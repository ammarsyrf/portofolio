<?php
/**
 * Database Connection via PDO
 */
require_once __DIR__ . '/../config.php';

function get_db(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            // Pesan aman tanpa bocoran kredensial atau stack trace
            http_response_code(500);
            die("Layanan database sedang tidak tersedia. Silakan hubungi administrator.");
        }
    }

    return $pdo;
}

// Inisialisasi variabel $pdo global untuk kemudahan penggunaan
$pdo = get_db();
