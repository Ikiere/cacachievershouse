-- ============================================================
-- CAC ACHIEVERS HOUSE — Database Schema
-- Run this in phpMyAdmin or via: mysql -u root cac < schema.sql
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- ── ADMINS ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admins` (
    `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(120)     NOT NULL,
    `email`      VARCHAR(180)     NOT NULL UNIQUE,
    `password`   VARCHAR(255)     NOT NULL,
    `role`       ENUM('super_admin','editor') NOT NULL DEFAULT 'editor',
    `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin (password: admin123  — CHANGE THIS IMMEDIATELY)
INSERT IGNORE INTO `admins` (`name`, `email`, `password`, `role`) VALUES
('Admin', 'admin@cacachievers.com', '$2y$10$6IvEBVg7TqLYQ3I3VLTHUuGNvQ2pT2kPLnBXlx5aBt5KkGJfGdHVS', 'super_admin');

-- ── EVENTS ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `events` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `title`       VARCHAR(200)     NOT NULL,
    `description` TEXT,
    `start_date`  DATE             NOT NULL,
    `start_time`  TIME,
    `end_date`    DATE,
    `end_time`    TIME,
    `venue_name`  VARCHAR(200),
    `event_type`  VARCHAR(80),
    `status`      ENUM('planning','upcoming','past','cancelled') NOT NULL DEFAULT 'upcoming',
    `image`       VARCHAR(255),
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── GALLERY ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `gallery` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `filename`    VARCHAR(255)     NOT NULL,
    `caption`     VARCHAR(255),
    `category`    VARCHAR(80)      NOT NULL DEFAULT 'General',
    `uploaded_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── CONTACTS ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `contacts` (
    `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(120)     NOT NULL,
    `email`      VARCHAR(180)     NOT NULL,
    `subject`    VARCHAR(200),
    `message`    TEXT             NOT NULL,
    `is_read`    TINYINT(1)       NOT NULL DEFAULT 0,
    `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
