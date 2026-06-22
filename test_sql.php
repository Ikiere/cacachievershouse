<?php
require 'includes/config.php';
$title = 'T';
$description = 'D';
$start_date = '2025-01-01';
$start_time = '10:00:00';
$end_date = 'NULL';
$end_time = 'NULL';
$venue_name = 'V';
$event_type = 'Service';
$status = 'upcoming';

$sql = "INSERT INTO events (title, description, start_date, start_time, end_date, end_time, venue_name, event_type, status ) 
        VALUES ('$title', '$description', '$start_date', '$start_time', $end_date, $end_time, '$venue_name', '$event_type', '$status' )";

if (!$conn->query($sql)) {
    echo "ERROR: " . $conn->error . "\n";
} else {
    echo "SUCCESS\n";
}
