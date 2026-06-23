<?php
require_once __DIR__ . '/admin/config.php';

echo "=== ALL site_settings ===\n";
$r = $conn->query("SELECT setting_key, setting_value FROM site_settings ORDER BY setting_key");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        echo $row['setting_key'] . ' => ' . $row['setting_value'] . "\n";
    }
} else {
    echo "ERROR: " . $conn->error . "\n";
}

echo "\n=== Test SMTP INSERT ===\n";
$stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES ('smtp_test_key', 'test_value') ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
if ($stmt) {
    $ok = $stmt->execute();
    echo "INSERT ok: " . ($ok ? 'YES' : 'NO') . "\n";
    echo "Affected rows: " . $stmt->affected_rows . "\n";
} else {
    echo "PREPARE ERROR: " . $conn->error . "\n";
}

// Clean up
$conn->query("DELETE FROM site_settings WHERE setting_key = 'smtp_test_key'");
