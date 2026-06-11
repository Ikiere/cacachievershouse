<?php
require_once "config.php";
$data = json_decode(file_get_contents("php://input"), true);

$password = password_hash("default123", PASSWORD_BCRYPT);

$stmt = $conn->prepare("
    INSERT INTO admins (name,email,phone,joined_date,role,password)
    VALUES (?,?,?,?,?,?)
");
$stmt->bind_param(
    "ssssss",
    $data['name'],
    $data['email'],
    $data['phone'],
    $data['joined'],
    $data['role'],
    $password
);
$stmt->execute();
