<?php
session_start();
require_once 'config.php';

// Only logged-in admins
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $leader = trim($_POST['leader'] ?? '');
    $tagline = trim($_POST['tagline'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Parse schedules
    $schedule_labels = $_POST['schedule_label'] ?? [];
    $schedule_details = $_POST['schedule_detail'] ?? [];
    $schedule = [];
    
    for ($i = 0; $i < count($schedule_labels); $i++) {
        $l = trim($schedule_labels[$i]);
        $d = trim($schedule_details[$i]);
        if (!empty($l) || !empty($d)) {
            $schedule[] = ['label' => $l, 'detail' => $d];
        }
    }
    
    $schedule_json = json_encode($schedule);

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE ministries SET name=?, leader=?, tagline=?, description=?, schedule=? WHERE id=?");
        $stmt->bind_param("sssssi", $name, $leader, $tagline, $description, $schedule_json, $id);
        
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Database error: " . $conn->error;
        }
    } else {
        echo "Invalid ID.";
    }
}
