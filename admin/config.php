<?php
// ============================================================
// DATABASE CONFIGURATION
// admin/config.php
// Loads credentials from .env.php (never committed to git).
// ============================================================

// --- Load environment credentials -----------------------------------
$envPath = __DIR__ . '/.env.php';

if (!file_exists($envPath)) {
    // Fail gracefully — guide the developer, never expose internals
    http_response_code(503);
    die('Server configuration error. The environment file is missing. '
      . 'Copy .env.example.php to .env.php and set your credentials.');
}

$env = require $envPath;

// --- Validate required keys ----------------------------------------
$requiredKeys = ['db_host', 'db_username', 'db_password', 'db_name'];
foreach ($requiredKeys as $key) {
    if (!array_key_exists($key, $env)) {
        http_response_code(503);
        die("Server configuration error. Missing key: {$key} in .env.php");
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
    // Log the real error server-side; show a safe message to visitors
    error_log('DB connection failed: ' . $conn->connect_error);
    http_response_code(503);
    die('Database connection failed. Please try again later.');
}

// --- Set character encoding ----------------------------------------
$conn->set_charset($env['db_charset'] ?? 'utf8mb4');
