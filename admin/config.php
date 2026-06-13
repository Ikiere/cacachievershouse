<?php
// ============================================================
// DATABASE CONFIGURATION
// admin/config.php
//
// Auto-detects environment:
//   LOCAL  → uses XAMPP defaults (no .env.php needed)
//   PROD   → loads credentials from .env.php on the server
// ============================================================

// --- Detect environment --------------------------------------------
$isLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1'])
        || in_array($_SERVER['HTTP_HOST']   ?? '', ['localhost', '127.0.0.1'])
        || (PHP_OS_FAMILY === 'Windows');

// --- Load credentials ----------------------------------------------
if ($isLocal) {
    // XAMPP defaults — works out of the box locally
    $env = [
        'db_host'     => 'localhost',
        'db_username' => 'root',
        'db_password' => '',
        'db_name'     => 'cac',
        'db_charset'  => 'utf8mb4',
    ];
} else {
    // Production — load from .env.php (server-only, never in git)
    $envPath = __DIR__ . '/.env.php';

    if (!file_exists($envPath)) {
        http_response_code(503);
        error_log('FATAL: .env.php missing in ' . __DIR__);
        die('Server configuration error. Please contact the administrator.');
    }

    $env = require $envPath;

    // Validate required keys
    $requiredKeys = ['db_host', 'db_username', 'db_password', 'db_name'];
    foreach ($requiredKeys as $key) {
        if (!array_key_exists($key, $env)) {
            http_response_code(503);
            error_log("FATAL: Missing key '{$key}' in .env.php");
            die('Server configuration error. Please contact the administrator.');
        }
    }
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

// --- Set character encoding ----------------------------------------
$conn->set_charset($env['db_charset'] ?? 'utf8mb4');

