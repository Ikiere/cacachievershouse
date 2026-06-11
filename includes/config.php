<?php
// ============================================================
// DATABASE CONNECTION
// ============================================================
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "cac";

$conn = @new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed. Please check your credentials: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

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
