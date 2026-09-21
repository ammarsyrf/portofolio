-- Migration: Tambah tabel page_views untuk statistik pengunjung
-- Jalankan sekali di database

CREATE TABLE IF NOT EXISTS `page_views` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` VARCHAR(128)    NOT NULL,
  `ip_hash`    VARCHAR(64)     NOT NULL,
  `user_agent` TEXT,
  `page`       VARCHAR(512)    NOT NULL DEFAULT '/',
  `referer`    VARCHAR(512),
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_session`    (`session_id`),
  INDEX `idx_page`       (`page`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
