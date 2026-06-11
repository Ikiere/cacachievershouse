<?php
// ============================================================
// ADMIN AUTH HANDLER
// admin/auth.php
// ============================================================
session_start();
require_once 'config.php';

if (!isset($_POST['email'], $_POST['password'])) {
    $_SESSION['error'] = 'Invalid request.';
    header('Location: login.php');
    exit;
}

$email    = trim($_POST['email']);
$password = $_POST['password'];

$stmt = $conn->prepare('SELECT id, name, email, role, password FROM admins WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    if (password_verify($password, $admin['password'])) {
        // Regenerate session ID to prevent fixation
        session_regenerate_id(true);

        $_SESSION['admin_id']    = $admin['id'];
        $_SESSION['admin_name']  = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_role']  = $admin['role'];

        header('Location: dashboard.php');
        exit;
    }
}

$_SESSION['error'] = 'Invalid email or password. Please try again.';
header('Location: login.php');
exit;
