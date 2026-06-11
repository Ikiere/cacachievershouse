<?php
// One-time DB repair + setup script — DELETE THIS FILE AFTER RUNNING
$conn = new mysqli('localhost', 'root', '', 'cac');
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
$conn->set_charset('utf8mb4');

$results = [];

// Suppress errors for cleanup queries
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// First: try to create admins without IF NOT EXISTS so we catch the exact error
// If admins tablespace is orphaned, recreate using a temp approach
$testCreate = $conn->query("CREATE TABLE `admins` (`id` INT PRIMARY KEY) ENGINE=InnoDB");
if (!$testCreate) {
    $err = $conn->error;
    if (strpos($err, 'Tablespace') !== false || strpos($err, 'already exists') !== false) {
        // Use INNODB_FORCE_RECOVERY workaround: create via MyISAM first then alter
        $conn->query("DROP TABLE IF EXISTS admins");
        $results[] = "🔧 Attempted DROP TABLE admins: " . ($conn->error ?: 'OK');
    }
} else {
    $conn->query("DROP TABLE IF EXISTS admins");
}

// Step 2: Create all tables fresh
$tables = [
    "admins" => "CREATE TABLE `admins` (
        `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
        `name`       VARCHAR(120)     NOT NULL,
        `email`      VARCHAR(180)     NOT NULL UNIQUE,
        `password`   VARCHAR(255)     NOT NULL,
        `role`       ENUM('super_admin','editor') NOT NULL DEFAULT 'editor',
        `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "events" => "CREATE TABLE IF NOT EXISTS `events` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "gallery" => "CREATE TABLE IF NOT EXISTS `gallery` (
        `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
        `filename`    VARCHAR(255)     NOT NULL,
        `caption`     VARCHAR(255),
        `category`    VARCHAR(80)      NOT NULL DEFAULT 'General',
        `uploaded_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "contacts" => "CREATE TABLE IF NOT EXISTS `contacts` (
        `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
        `name`       VARCHAR(120)     NOT NULL,
        `email`      VARCHAR(180)     NOT NULL,
        `subject`    VARCHAR(200),
        `message`    TEXT             NOT NULL,
        `is_read`    TINYINT(1)       NOT NULL DEFAULT 0,
        `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];

foreach ($tables as $name => $sql) {
    $r = $conn->query($sql);
    $results[] = $r ? "✅ Table '$name' ready" : "❌ Table '$name' error: " . $conn->error;
}

// Step 3: Insert default admin
$hash = password_hash('admin123', PASSWORD_BCRYPT);
$stmt = $conn->prepare("INSERT IGNORE INTO admins (name, email, password, role) VALUES (?, ?, ?, 'super_admin')");
$aname = 'Admin'; $aemail = 'admin@cacachievers.com';
$stmt->bind_param('sss', $aname, $aemail, $hash);
if ($stmt->execute() && $stmt->affected_rows > 0) {
    $results[] = "✅ Default admin created — email: admin@cacachievers.com | password: admin123";
} else {
    $results[] = "ℹ️ Admin already exists or skipped: " . $stmt->error;
}

$conn->query("SET FOREIGN_KEY_CHECKS = 1");
$conn->close();

header('Content-Type: text/html; charset=utf-8');
echo "<!DOCTYPE html><html><head><title>DB Setup</title></head><body>";
echo "<pre style='font-family:monospace;font-size:14px;padding:2rem;background:#f0f4ff;line-height:1.8;'>";
echo "<strong>CAC Achievers House — Database Setup</strong>\n";
echo str_repeat("─", 50) . "\n\n";
foreach ($results as $r) { echo $r . "\n"; }
echo "\n" . str_repeat("─", 50);
echo "\n<strong style='color:red'>⚠️  DELETE setup.php IMMEDIATELY after verifying above.</strong>";
echo "</pre></body></html>";
