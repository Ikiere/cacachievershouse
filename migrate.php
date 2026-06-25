<?php
// ============================================================
// CAC ACHIEVERS HOUSE — SAFE DATABASE MIGRATION SCRIPT
// migrate.php  |  Run once after deployment to cPanel.
// Uses IF NOT EXISTS so it is SAFE to re-run at any time.
// ============================================================
require __DIR__ . '/admin/config.php';

$results = [];
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// ── HELPER: run a query and record result ─────────────────────
function migrate(mysqli $conn, string $label, string $sql, array &$results): void {
    $ok = $conn->query($sql);
    $results[] = $ok
        ? "✅ $label"
        : "❌ $label — " . $conn->error;
}

// ============================================================
// 1. CORE TABLES
// ============================================================

migrate($conn, "Table: admins", "CREATE TABLE IF NOT EXISTS `admins` (
    `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(120)     NOT NULL,
    `email`      VARCHAR(180)     NOT NULL UNIQUE,
    `password`   VARCHAR(255)     NOT NULL,
    `role`       ENUM('super_admin','editor') NOT NULL DEFAULT 'editor',
    `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $results);

migrate($conn, "Table: events", "CREATE TABLE IF NOT EXISTS `events` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $results);

migrate($conn, "Table: gallery", "CREATE TABLE IF NOT EXISTS `gallery` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `filename`    VARCHAR(255)     NOT NULL,
    `caption`     VARCHAR(255),
    `category`    VARCHAR(80)      NOT NULL DEFAULT 'General',
    `uploaded_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $results);

migrate($conn, "Table: contacts", "CREATE TABLE IF NOT EXISTS `contacts` (
    `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(120)     NOT NULL,
    `email`      VARCHAR(180)     NOT NULL,
    `subject`    VARCHAR(200),
    `message`    TEXT             NOT NULL,
    `is_read`    TINYINT(1)       NOT NULL DEFAULT 0,
    `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $results);

migrate($conn, "Table: sermons", "CREATE TABLE IF NOT EXISTS `sermons` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `title`       VARCHAR(255)     NOT NULL,
    `series`      VARCHAR(255),
    `speaker`     VARCHAR(120)     NOT NULL,
    `sermon_date` DATE             NOT NULL,
    `audio_file`  VARCHAR(255),
    `video_url`   VARCHAR(255),
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $results);

migrate($conn, "Table: testimonials", "CREATE TABLE IF NOT EXISTS `testimonials` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(120)     NOT NULL,
    `role`        VARCHAR(120),
    `quote`       TEXT             NOT NULL,
    `photo_url`   VARCHAR(255),
    `is_active`   TINYINT(1)       NOT NULL DEFAULT 1,
    `sort_order`  INT              NOT NULL DEFAULT 0,
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $results);

migrate($conn, "Table: site_settings", "CREATE TABLE IF NOT EXISTS `site_settings` (
    `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `setting_key`   VARCHAR(100)     NOT NULL UNIQUE,
    `setting_value` TEXT,
    `updated_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $results);

// ============================================================
// 2. NEW: MINISTRIES TABLE
// ============================================================
migrate($conn, "Table: ministries", "CREATE TABLE IF NOT EXISTS `ministries` (
    `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `slug`        VARCHAR(80)      NOT NULL UNIQUE,
    `name`        VARCHAR(120)     NOT NULL,
    `icon`        VARCHAR(80)      NOT NULL DEFAULT 'bx bx-heart',
    `color`       VARCHAR(20)      NOT NULL DEFAULT '#f97316',
    `badge_text`  VARCHAR(60)      DEFAULT NULL,
    `tagline`     VARCHAR(255)     DEFAULT NULL,
    `description` TEXT,
    `schedule`    TEXT             COMMENT 'JSON array of schedule items',
    `leader`      VARCHAR(120)     DEFAULT NULL,
    `hero_bg`     VARCHAR(255)     DEFAULT NULL,
    `sort_order`  INT              NOT NULL DEFAULT 0,
    `is_active`   TINYINT(1)       NOT NULL DEFAULT 1,
    `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", $results);

// ============================================================
// 3. COLUMN UPGRADES (safe ALTER TABLE — ignore error if exists)
// ============================================================
$alters = [
    // Ensure gallery.category exists
    "ALTER TABLE `gallery` ADD COLUMN IF NOT EXISTS `category` VARCHAR(80) NOT NULL DEFAULT 'General'",
    // Ensure testimonials.photo_url exists
    "ALTER TABLE `testimonials` ADD COLUMN IF NOT EXISTS `photo_url` VARCHAR(255)",
    // Ensure testimonials.sort_order exists
    "ALTER TABLE `testimonials` ADD COLUMN IF NOT EXISTS `sort_order` INT NOT NULL DEFAULT 0",
];
foreach ($alters as $alter) {
    @$conn->query($alter); // Suppress errors — column may already exist
}
$results[] = "✅ Column upgrades applied (existing columns safely skipped)";

// ============================================================
// 4. DEFAULT SITE SETTINGS
// ============================================================
$conn->query("INSERT IGNORE INTO `site_settings` (`setting_key`, `setting_value`) VALUES
    ('site_name',     'CAC Achievers House'),
    ('site_tagline',  'Where Faith Meets Destiny'),
    ('primary_color', '#f97316')");
$conn->query("INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
    ('contact_address', 'Colville Common Room and Community Space, DE22 3AT')
    ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`)");
$results[] = "✅ Default site settings populated & address casing updated";

// ============================================================
// 5. SEED MINISTRIES (INSERT IGNORE — safe to re-run)
// ============================================================
$ministries = [
    [
        'slug'        => 'youth-ministry',
        'name'        => 'Youth Ministry',
        'icon'        => 'bx bx-meteor',
        'color'       => '#f97316',
        'badge_text'  => 'Ages 13–25',
        'tagline'     => 'Empowering the next generation for God',
        'description' => 'Empowering young people to discover their identity in Christ through dynamic worship, relevant teachings, peer-to-peer accountability, and meaningful community connections. We believe every young person has God-given potential and we exist to help them unlock it.',
        'schedule'    => '[]',
        'leader'      => 'Pastor Taiwo',
        'sort_order'  => 1,
    ],
    [
        'slug'        => 'childrens-church',
        'name'        => 'Children\'s Church',
        'icon'        => 'bx bx-book-heart',
        'color'       => '#3b82f6',
        'badge_text'  => 'Ages 3–12',
        'tagline'     => 'Building faith from the very beginning',
        'description' => 'Building strong foundations of faith in children through engaging Bible stories, age-appropriate worship, interactive activities, and a safe, loving environment. We partner with parents to raise children who know, love and serve God from an early age.',
        'schedule'    => '[]',
        'leader'      => 'Deaconess Funmi',
        'sort_order'  => 2,
    ],
    [
        'slug'        => 'womens-fellowship',
        'name'        => 'Women\'s Fellowship',
        'icon'        => 'bx bx-crown',
        'color'       => '#ec4899',
        'badge_text'  => 'All Women Welcome',
        'tagline'     => 'Raising queens who impact their world',
        'description' => 'A nurturing community where women grow spiritually, build lasting friendships, mentor one another, and are equipped to impact their homes, workplaces, and nation. Every woman is valued, celebrated, and empowered to fulfil her God-given destiny.',
        'schedule'    => '[]',
        'leader'      => 'Pastor Mrs. Adeyemi',
        'sort_order'  => 3,
    ],
    [
        'slug'        => 'evangelism-committee',
        'name'        => 'Evangelism Committee',
        'icon'        => 'bx bx-world',
        'color'       => '#22c55e',
        'badge_text'  => 'Open to All',
        'tagline'     => 'Reaching the unreached for Christ',
        'description' => 'Reaching the unreached in our communities through street evangelism, hospital visitation, prison ministry, and community development initiatives that demonstrate God\'s love in action. We carry the Great Commission into every corner of society.',
        'schedule'    => '[]',
        'leader'      => 'Evang. Grace',
        'sort_order'  => 4,
    ],
];

$stmt = $conn->prepare(
    "INSERT IGNORE INTO `ministries`
        (slug, name, icon, color, badge_text, tagline, description, schedule, leader, sort_order)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

foreach ($ministries as $m) {
    $stmt->bind_param(
        'sssssssssi',
        $m['slug'], $m['name'], $m['icon'], $m['color'],
        $m['badge_text'], $m['tagline'], $m['description'],
        $m['schedule'], $m['leader'], $m['sort_order']
    );
    $ok = $stmt->execute();
    $results[] = $ok
        ? "✅ Ministry seeded: {$m['name']}"
        : "⚠️  Ministry '{$m['name']}' skipped (already exists)";
}

// ============================================================
// 6. DEFAULT ADMIN (if missing)
// ============================================================
$hash  = password_hash('admin123', PASSWORD_BCRYPT);
$aname = 'Admin';
$aemail = 'admin@cacachievers.com';
$astmt = $conn->prepare("INSERT IGNORE INTO admins (name, email, password, role) VALUES (?, ?, ?, 'super_admin')");
$astmt->bind_param('sss', $aname, $aemail, $hash);
$astmt->execute();
$results[] = $astmt->affected_rows > 0
    ? "✅ Default admin created (admin@cacachievers.com / admin123)"
    : "ℹ️  Default admin already exists";

$conn->query("SET FOREIGN_KEY_CHECKS = 1");
$conn->close();

// ── OUTPUT ────────────────────────────────────────────────────
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Database Migration — CAC Achievers House</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; background: #f0f4ff; padding: 2rem; }
    .container { max-width: 700px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    h1 { font-size: 1.5rem; color: #0f172a; margin-bottom: 0.5rem; }
    .subtitle { color: #64748b; font-size: 0.9rem; margin-bottom: 2rem; }
    .results { list-style: none; line-height: 2; font-size: 0.9rem; }
    hr { border: none; border-top: 1px solid #e2e8f0; margin: 1.5rem 0; }
    .warning { background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 1rem; font-size: 0.9rem; color: #92400e; margin-top: 1.5rem; }
    .warning strong { display: block; margin-bottom: 0.3rem; }
</style>
</head>
<body>
<div class="container">
    <h1>🏛 CAC Achievers House — Database Migration</h1>
    <p class="subtitle">Ran on <?= date('Y-m-d H:i:s') ?></p>
    <hr>
    <ul class="results">
        <?php foreach ($results as $r): ?>
        <li><?= htmlspecialchars($r) ?></li>
        <?php endforeach; ?>
    </ul>
    <div class="warning">
        <strong>⚠️ Security Notice</strong>
        This script has completed successfully. For security, <strong>delete or rename migrate.php</strong> before your site goes live. You can also protect it via <code>.htaccess</code> or add IP restriction to the cPanel file manager.
    </div>
</div>
</body>
</html>
