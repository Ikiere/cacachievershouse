<?php
// ============================================================
// DELETE GALLERY IMAGE — AJAX endpoint
// admin/delete_gallery.php
// ============================================================
ob_start(); // Catch ANY stray output

session_start();
require_once "config.php";

// Clean any output from includes
ob_end_clean();

// Set headers for AJAX response
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Auth check
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

// Method check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed";
    exit;
}

$id = 0;
if (isset($_POST['id'])) {
    $id = (int)$_POST['id'];
} else {
    // Try parsing JSON payload (e.g. from admin/dashboard.php)
    $rawInput = file_get_contents('php://input');
    $jsonData = json_decode($rawInput, true);
    if (isset($jsonData['id'])) {
        $id = (int)$jsonData['id'];
    }
}

if ($id <= 0) {
    http_response_code(400);
    echo "Invalid image ID";
    exit;
}

// Fetch image record
$stmt = $conn->prepare("SELECT filename FROM gallery WHERE id = ?");
if (!$stmt) {
    http_response_code(500);
    echo "Database prepare error";
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    http_response_code(404);
    echo "Image not found";
    $stmt->close();
    exit;
}

$row = $result->fetch_assoc();
$stmt->close();

$filename = $row['filename'];
$path = __DIR__ . '/../assets/gallery/' . $filename;

// Delete from database
$del_stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
if (!$del_stmt) {
    http_response_code(500);
    echo "Database prepare error";
    exit;
}

$del_stmt->bind_param("i", $id);

if (!$del_stmt->execute()) {
    http_response_code(500);
    echo "Database delete failed";
    $del_stmt->close();
    exit;
}

$del_stmt->close();

// Delete physical file (silent fail is OK)
if (file_exists($path)) {
    @unlink($path);
}

echo "success";
exit;
