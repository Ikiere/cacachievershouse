<?php
// ============================================================
// SAVE SITE SETTINGS
// admin/save_settings.php
// ============================================================
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$allowed_keys = [
    'site_name', 'site_tagline', 'hero_title', 'hero_subtitle',
    'contact_phone', 'contact_email', 'contact_address',
    'primary_color', 'facebook_url', 'youtube_url',
    'instagram_url', 'twitter_url', 'whatsapp_number', 'give_url'
];

$errors = [];

// ── Handle logo upload ────────────────────────────────────────
if (!empty($_FILES['logo']['tmp_name'])) {
    $file    = $_FILES['logo'];
    $allowed = ['image/png', 'image/jpeg', 'image/webp', 'image/gif', 'image/svg+xml'];
    $maxSize = 2 * 1024 * 1024; // 2 MB

    if (!in_array($file['type'], $allowed)) {
        $errors[] = 'Logo must be PNG, JPG, WebP, GIF, or SVG.';
    } elseif ($file['size'] > $maxSize) {
        $errors[] = 'Logo must be under 2 MB.';
    } else {
        $ext        = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename   = 'cac-logo.' . strtolower($ext);
        $uploadPath = dirname(__DIR__) . '/assets/logo/' . $filename;

        if (!is_dir(dirname($uploadPath))) {
            mkdir(dirname($uploadPath), 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $stmt = $conn->prepare(
                "INSERT INTO site_settings (setting_key, setting_value)
                 VALUES ('logo_path', ?)
                 ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
            );
            $logoVal = 'assets/logo/' . $filename;
            $stmt->bind_param('s', $logoVal);
            $stmt->execute();
        } else {
            $errors[] = 'Failed to save logo file.';
        }
    }
}

// ── Handle text settings ──────────────────────────────────────
foreach ($_POST as $key => $value) {
    if (!in_array($key, $allowed_keys)) continue;

    $value = trim(strip_tags($value));

    $stmt = $conn->prepare(
        "INSERT INTO site_settings (setting_key, setting_value)
         VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
    );
    $stmt->bind_param('ss', $key, $value);
    $stmt->execute();
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
} else {
    echo json_encode(['success' => true, 'message' => 'Settings saved successfully.']);
}
exit;
