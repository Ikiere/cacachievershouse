<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    $stmt = $conn->prepare("SELECT filename FROM gallery WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $filename = $row['filename'];
        $path = '../assets/gallery/' . $filename;

        // Delete from DB
        $del_stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
        $del_stmt->bind_param("i", $id);
        
        if ($del_stmt->execute()) {
            // Delete physical file
            if (file_exists($path)) {
                unlink($path);
            }
            echo "success";
        } else {
            echo "Database error";
        }
    } else {
        echo "Image not found";
    }
} else {
    echo "Invalid request";
}
