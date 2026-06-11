<?php
require 'config.php';

$events = $pdo->query("
    SELECT e.*,
    (SELECT COUNT(*) FROM event_images i WHERE i.event_id = e.id) AS images
    FROM events e
    ORDER BY start_datetime ASC
")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($events);
