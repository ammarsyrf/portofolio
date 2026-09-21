-- Migration: Tambah Kolom Detail Pengunjung (IP Unik, Negara, Kota, Device, Browser, OS)
-- Database: MySQL / MariaDB

CREATE TABLE IF NOT EXISTS `page_views` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` VARCHAR(128)    NOT NULL,
  `ip_address` VARCHAR(45)     NOT NULL DEFAULT '127.0.0.1',
  `ip_hash`    VARCHAR(64)     NOT NULL,
  `country`    VARCHAR(100)    DEFAULT 'Unknown',
  `city`       VARCHAR(100)    DEFAULT 'Unknown',
  `device`     VARCHAR(50)     DEFAULT 'Desktop',
  `browser`    VARCHAR(50)     DEFAULT 'Unknown',
  `os`         VARCHAR(50)     DEFAULT 'Unknown',
  `page`       VARCHAR(512)    NOT NULL DEFAULT '/',
  `referer`    VARCHAR(512),
  `user_agent` TEXT,
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_session`    (`session_id`),
  INDEX `idx_ip`         (`ip_address`),
  INDEX `idx_country`    (`country`),
  INDEX `idx_page`       (`page`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
