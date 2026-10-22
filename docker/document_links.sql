-- e-Saraban: generic document relationships for future modules.
-- Safe to run on an existing database; does not modify or delete existing records.
CREATE TABLE IF NOT EXISTS `document_links` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `source_type` VARCHAR(50) NOT NULL,
  `source_id` BIGINT UNSIGNED NOT NULL,
  `target_type` VARCHAR(50) NOT NULL,
  `target_id` BIGINT UNSIGNED NOT NULL,
  `relation_type` VARCHAR(50) NOT NULL DEFAULT 'related',
  `created_by` INT UNSIGNED NULL,
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_document_link` (`source_type`, `source_id`, `target_type`, `target_id`, `relation_type`),
  KEY `idx_document_link_source` (`source_type`, `source_id`),
  KEY `idx_document_link_target` (`target_type`, `target_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
