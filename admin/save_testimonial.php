<?php
// ============================================================
// TESTIMONIAL SAVE HANDLER
// admin/save_testimonial.php
// ============================================================
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../includes/config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// ── DELETE ──────────────────────────────────────────────────
if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if (!$id) { echo json_encode(['success'=>false,'message'=>'Invalid ID']); exit; }

    $stmt = $conn->prepare('DELETE FROM testimonials WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Testimonial deleted.']);
    exit;
}

// ── TOGGLE VISIBILITY ───────────────────────────────────────
if ($action === 'toggle') {
    $id = intval($_POST['id'] ?? 0);
    $is_active = intval($_POST['is_active'] ?? 0);
    if (!$id) { echo json_encode(['success'=>false,'message'=>'Invalid ID']); exit; }

    $stmt = $conn->prepare('UPDATE testimonials SET is_active = ? WHERE id = ?');
    $stmt->bind_param('ii', $is_active, $id);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => $is_active ? 'Testimonial is now visible.' : 'Testimonial hidden.']);
    exit;
}

// ── SAVE (insert or update) ─────────────────────────────────
$id         = intval($_POST['testimonial_id'] ?? 0);
$name       = trim($_POST['name']       ?? '');
$role       = trim($_POST['role']       ?? 'Church Member');
$quote      = trim($_POST['quote']      ?? '');
$photo_url  = trim($_POST['photo_url']  ?? '');
$sort_order = intval($_POST['sort_order'] ?? 0);
$is_active  = isset($_POST['is_active']) ? intval($_POST['is_active']) : 0;

if (!$name || !$quote) {
    echo json_encode(['success'=>false,'message'=>'Name and quote are required.']);
    exit;
}

if ($id > 0) {
    // UPDATE
    $stmt = $conn->prepare('UPDATE testimonials SET name=?, role=?, quote=?, photo_url=?, sort_order=?, is_active=? WHERE id=?');
    $stmt->bind_param('ssssiis', $name, $role, $quote, $photo_url, $sort_order, $is_active, $id);
} else {
    // INSERT
    $stmt = $conn->prepare('INSERT INTO testimonials (name, role, quote, photo_url, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('ssssii', $name, $role, $quote, $photo_url, $sort_order, $is_active);
}

if ($stmt->execute()) {
    echo json_encode(['success'=>true, 'message'=>'Testimonial saved successfully.', 'id'=>$id ?: $conn->insert_id]);
} else {
    echo json_encode(['success'=>false, 'message'=>'Database error: ' . $conn->error]);
}
