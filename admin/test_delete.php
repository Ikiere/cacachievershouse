<?php
// Quick CLI test for gallery deletion
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/cac/admin/test.php';

require_once __DIR__ . '/config.php';

echo "DB connected: OK\n";

// Check gallery table
$res = $conn->query("SELECT id, filename FROM gallery ORDER BY id LIMIT 5");
if (!$res) {
    echo "QUERY ERROR: " . $conn->error . "\n";
    exit(1);
}

echo "Gallery rows: " . $res->num_rows . "\n";
if ($res->num_rows === 0) {
    echo "No images in gallery. Nothing to test deletion on.\n";
    exit(0);
}

while ($r = $res->fetch_assoc()) {
    $path = __DIR__ . '/../assets/gallery/' . $r['filename'];
    $exists = file_exists($path) ? 'EXISTS' : 'MISSING';
    echo "  ID={$r['id']}  file={$r['filename']}  physical={$exists}\n";
}

// Test deletion of a non-existent ID to see what the response would be
echo "\nTesting delete with id=99999 (non-existent):\n";
$stmt = $conn->prepare("SELECT filename FROM gallery WHERE id = ?");
$test_id = 99999;
$stmt->bind_param("i", $test_id);
$stmt->execute();
$result = $stmt->get_result();
echo "  num_rows: " . $result->num_rows . "\n";
echo "  Expected response: 'Image not found'\n";

echo "\nDone.\n";
