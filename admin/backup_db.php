<?php
/**
 * 1-Click Database Backup (.sql) Generator
 * Dumps all tables and rows cleanly for disaster recovery / offline backup.
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Disable time limit for large backups
@set_time_limit(300);

$filename = 'db_portfolio_ammar_' . date('Y-m-d_His') . '.sql';

header('Content-Type: application/sql; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

fputs($output, "-- ========================================================\n");
fputs($output, "-- Portfolio Ammar Syarif — MySQL Database Backup\n");
fputs($output, "-- Date / Time : " . date('Y-m-d H:i:s') . " WIB\n");
fputs($output, "-- Host        : " . DB_HOST . ":" . DB_PORT . "\n");
fputs($output, "-- Database    : " . DB_NAME . "\n");
fputs($output, "-- ========================================================\n\n");
fputs($output, "SET FOREIGN_KEY_CHECKS=0;\n");
fputs($output, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
fputs($output, "SET NAMES utf8mb4;\n\n");

try {
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        fputs($output, "-- --------------------------------------------------------\n");
        fputs($output, "-- Table structure for table `{$table}`\n");
        fputs($output, "-- --------------------------------------------------------\n");
        fputs($output, "DROP TABLE IF EXISTS `{$table}`;\n");

        $createTable = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
        fputs($output, $createTable['Create Table'] . ";\n\n");

        fputs($output, "-- Dumping data for table `{$table}`\n");
        $stmt = $pdo->query("SELECT * FROM `{$table}`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            $columns = array_keys($rows[0]);
            $colList = '`' . implode('`, `', $columns) . '`';

            $insertChunks = array_chunk($rows, 100);
            foreach ($insertChunks as $chunk) {
                $valRows = [];
                foreach ($chunk as $row) {
                    $vals = [];
                    foreach ($row as $val) {
                        if ($val === null) {
                            $vals[] = 'NULL';
                        } else {
                            $vals[] = $pdo->quote($val);
                        }
                    }
                    $valRows[] = '(' . implode(', ', $vals) . ')';
                }
                fputs($output, "INSERT INTO `{$table}` ({$colList}) VALUES\n" . implode(",\n", $valRows) . ";\n");
            }
        }
        fputs($output, "\n");
    }

    fputs($output, "SET FOREIGN_KEY_CHECKS=1;\n");
    fputs($output, "-- Backup completed successfully.\n");

} catch (\Throwable $e) {
    fputs($output, "\n-- ERROR DURING BACKUP: " . $e->getMessage() . "\n");
}

fclose($output);
exit;
