-- Migration 004: Tables for Career Telemetry, Security Logs, and Admin Scratchpad
-- Database: MySQL / MariaDB

CREATE TABLE IF NOT EXISTS `cv_downloads` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address` VARCHAR(45) NOT NULL,
  `country` VARCHAR(100) DEFAULT 'Unknown',
  `city` VARCHAR(100) DEFAULT 'Unknown',
  `user_agent` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `login_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `status` ENUM('SUCCESS', 'FAILED') NOT NULL DEFAULT 'FAILED',
  `ip_address` VARCHAR(45) NOT NULL,
  `country` VARCHAR(100) DEFAULT 'Unknown',
  `city` VARCHAR(100) DEFAULT 'Unknown',
  `user_agent` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admin_notes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `note_content` MEDIUMTEXT DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
