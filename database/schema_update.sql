-- ============================================================
-- CAC ACHIEVERS HOUSE — Schema Update: Site Settings
-- Run once via phpMyAdmin or: mysql -u root cac < schema_update.sql
-- ============================================================

SET NAMES utf8mb4;

-- ── SITE SETTINGS ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `site_settings` (
    `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `setting_key`   VARCHAR(100)     NOT NULL UNIQUE,
    `setting_value` TEXT,
    `updated_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default values (INSERT IGNORE = safe to run multiple times)
INSERT IGNORE INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('site_name',        'CAC Achievers House'),
('site_tagline',     'Where Faith Meets Destiny'),
('hero_title',       'Where Faith Meets Destiny'),
('hero_subtitle',    'A vibrant community where lives are restored, purposes are discovered, and believers are empowered to reach their God-given potential.'),
('contact_phone',    '+234 801 234 5678'),
('contact_email',    'info@cacachievers.com'),
('contact_address',  '12 Faith Avenue, Lagos, Nigeria'),
('logo_path',        'assets/logo/cac-logo.png'),
('primary_color',    '#f97316'),
('facebook_url',     '#'),
('youtube_url',      '#'),
('instagram_url',    '#'),
('twitter_url',      '#'),
('whatsapp_number',  ''),
('give_url',         '#');
