<?php
require 'config.php';

$start = $_POST['start_date'].' '.$_POST['start_time'];
$end = ($_POST['end_date'] && $_POST['end_time']) 
    ? $_POST['end_date'].' '.$_POST['end_time'] 
    : null;

$stmt = $pdo->prepare("
INSERT INTO events
(title,event_type,description,start_datetime,end_datetime,
is_recurring,venue_name,street,city,state,zip,is_online,
max_capacity,registration_deadline,organizer_name,contact_email,
contact_phone,ministry,status,notes)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");

$stmt->execute([
  $_POST['title'],
  $_POST['event_type'],
  $_POST['description'],
  $start,
  $end,
  isset($_POST['is_recurring']),
  $_POST['venue_name'],
  $_POST['street'],
  $_POST['city'],
  $_POST['state'],
  $_POST['zip'],
  isset($_POST['is_online']),
  $_POST['max_capacity'] ?: null,
  $_POST['registration_deadline'],
  $_POST['organizer_name'],
  $_POST['contact_email'],
  $_POST['contact_phone'],
  $_POST['ministry'],
  $_POST['status'],
  $_POST['notes']
]);

$event_id = $pdo->lastInsertId();

foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
  if ($tmp) {
    $name = time().$_FILES['images']['name'][$i];
    move_uploaded_file($tmp, "uploads/$name");
    $pdo->prepare("INSERT INTO event_images(event_id,image) VALUES (?,?)")
        ->execute([$event_id,$name]);
  }
}

echo json_encode(['status'=>'success']);
