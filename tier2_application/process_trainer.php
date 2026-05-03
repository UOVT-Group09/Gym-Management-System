<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name           = trim($_POST['name'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $phone          = trim($_POST['phone'] ?? '');

    if ($name === '') {
        header('Location: ../tier1_presentation/admin/trainers.php?error=Name+is+required');
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO trainers (name, specialization, phone) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $specialization, $phone);

    if ($stmt->execute()) {
        header('Location: ../tier1_presentation/admin/trainers.php?success=1');
    } else {
        header('Location: ../tier1_presentation/admin/trainers.php?error=' . urlencode($stmt->error));
    }

    $stmt->close();
    $conn->close();
    exit;
}

header('Location: ../tier1_presentation/admin/trainers.php');
exit;
?>
