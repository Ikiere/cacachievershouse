<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$action = $_POST['action'] ?? 'save';

if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid event ID.']);
        exit;
    }

    // Fetch existing image to delete it from disk
    $res = $conn->query("SELECT image FROM events WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        if ($row['image'] && file_exists(__DIR__ . '/../' . $row['image'])) {
            unlink(__DIR__ . '/../' . $row['image']);
        }
    }

    if ($conn->query("DELETE FROM events WHERE id = $id")) {
        echo json_encode(['success' => true, 'message' => 'Event deleted.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
    exit;
}

// ── Save / Update ───────────────────────────────────────────────
$id = intval($_POST['id'] ?? 0);
$title = $conn->real_escape_string(trim($_POST['title'] ?? ''));
$description = $conn->real_escape_string(trim($_POST['description'] ?? ''));
$start_date = $conn->real_escape_string(trim($_POST['start_date'] ?? ''));
$start_time = $conn->real_escape_string(trim($_POST['start_time'] ?? ''));
$end_date = !empty($_POST['end_date']) ? "'" . $conn->real_escape_string(trim($_POST['end_date'])) . "'" : 'NULL';
$end_time = !empty($_POST['end_time']) ? "'" . $conn->real_escape_string(trim($_POST['end_time'])) . "'" : 'NULL';
$venue_name = $conn->real_escape_string(trim($_POST['venue_name'] ?? ''));
$event_type = $conn->real_escape_string(trim($_POST['event_type'] ?? 'Service'));
$status = $conn->real_escape_string(trim($_POST['status'] ?? 'upcoming'));

if (!$title || !$start_date) {
    echo json_encode(['success' => false, 'message' => 'Title and Start Date are required.']);
    exit;
}
if (!$start_time) $start_time = '00:00:00';

$image_path_sql = "";

// Image Upload Logic
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/../assets/images/events/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $file_tmp = $_FILES['image']['tmp_name'];
    $file_name = $_FILES['image']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($file_ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid image format. Use JPG, PNG, or WEBP.']);
        exit;
    }

    $new_file_name = 'event_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
    $target_file = $upload_dir . $new_file_name;

    if (move_uploaded_file($file_tmp, $target_file)) {
        // Delete old image if updating
        if ($id > 0) {
            $oldRes = $conn->query("SELECT image FROM events WHERE id = $id");
            if ($old = $oldRes->fetch_assoc()) {
                if ($old['image'] && file_exists(__DIR__ . '/../' . $old['image'])) {
                    unlink(__DIR__ . '/../' . $old['image']);
                }
            }
        }
        $rel_path = 'assets/images/events/' . $new_file_name;
        $image_path_sql = ", image = '$rel_path'";
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
        exit;
    }
}

if ($id > 0) {
    // UPDATE
    $sql = "UPDATE events SET 
            title = '$title',
            description = '$description',
            start_date = '$start_date',
            start_time = '$start_time',
            end_date = $end_date,
            end_time = $end_time,
            venue_name = '$venue_name',
            event_type = '$event_type',
            status = '$status'
            $image_path_sql
            WHERE id = $id";
} else {
    // INSERT
    // For insert, we need the image logic integrated differently if it was uploaded
    $img_col = ""; $img_val = "";
    if ($image_path_sql !== "") {
        $img_col = ", image";
        $img_val = ", '$rel_path'";
    }

    $sql = "INSERT INTO events (title, description, start_date, start_time, end_date, end_time, venue_name, event_type, status $img_col) 
            VALUES ('$title', '$description', '$start_date', '$start_time', $end_date, $end_time, '$venue_name', '$event_type', '$status' $img_val)";
}

if ($conn->query($sql)) {
    echo json_encode(['success' => true, 'message' => 'Event saved successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}
