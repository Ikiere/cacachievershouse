<?php
// ============================================================
// DATABASE CONNECTION
// includes/config.php
//
// Auto-detects environment:
//   LOCAL  → uses XAMPP defaults
//   PROD   → loads credentials from admin/.env.php
// ============================================================

// --- Detect environment --------------------------------------------
$isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1'])
        || in_array($_SERVER['HTTP_HOST']   ?? '', ['localhost', '127.0.0.1'])
        || (PHP_OS_FAMILY === 'Windows');

// --- Load credentials ----------------------------------------------
if ($isLocal) {
    $env = [
        'db_host'     => 'localhost',
        'db_username' => 'root',
        'db_password' => '',
        'db_name'     => 'cac',
        'db_charset'  => 'utf8mb4',
    ];
} else {
    $envPath = __DIR__ . '/../admin/.env.php';

    if (!file_exists($envPath)) {
        http_response_code(503);
        die('Server configuration error. The environment file is missing.');
    }

    $env = require $envPath;
}

// --- Connect to MySQL ----------------------------------------------
$conn = @new mysqli(
    $env['db_host'],
    $env['db_username'],
    $env['db_password'],
    $env['db_name']
);

if ($conn->connect_error) {
    error_log('DB connection failed: ' . $conn->connect_error);
    http_response_code(503);
    die('Database connection failed. Please try again later.');
}

$conn->set_charset($env['db_charset'] ?? 'utf8mb4');

// ============================================================
// BASE URL — auto-detect subdirectory vs domain root
// Localhost: /cac/   |   Live server: /
// ============================================================
if (!defined('BASE_URL')) {
    $script_dir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    // If we're inside /includes/, go up one level
    if (basename($script_dir) === 'includes' || basename($script_dir) === 'admin') {
        $script_dir = rtrim(dirname($script_dir), '/\\');
    }
    define('BASE_URL', $script_dir . '/');
}

// Global shorthand — available to every page after require_once config.php
$base = defined('BASE_URL') ? BASE_URL : '/';

// ============================================================
// SITE SETTINGS HELPER
// Reads from the site_settings table with an in-memory cache
// so multiple get_setting() calls within one request are free.
// Usage: get_setting('site_name', 'Default Value')
// ============================================================
if (!isset($_settings_cache)) {
    $_settings_cache = null;
}

if (!function_exists('get_setting')) {
    function get_setting(string $key, string $default = ''): string {
        global $conn, $_settings_cache;

        // Load all settings once per request
        if ($_settings_cache === null) {
            $_settings_cache = [];
            try {
                $res = $conn->query("SELECT setting_key, setting_value FROM site_settings");
                if ($res) {
                    while ($row = $res->fetch_assoc()) {
                        $_settings_cache[$row['setting_key']] = $row['setting_value'];
                    }
                }
            } catch (\Exception $e) {
                // Table may not exist yet (fresh install) — use defaults
                $_settings_cache = [];
            }
        }

        return $_settings_cache[$key] ?? $default;
    }
}
?>
