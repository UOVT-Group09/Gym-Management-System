<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../tier1_presentation/login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';


if ($username === "admin" && $password === "admin123") {
    session_regenerate_id(true);
    $_SESSION['user'] = $username;
    $_SESSION['role'] = 'admin';

    header('Location: ../tier1_presentation/admin/admin_dashboard.php');
    exit;
} else {
    unset($_SESSION['user'], $_SESSION['role']);
    header('Location: ../tier1_presentation/login.php?error=invalid_credentials');
    exit;
}

?>


