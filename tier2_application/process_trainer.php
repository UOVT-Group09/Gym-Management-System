<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../tier1_presentation/admin/trainers.php');
    exit;
}

$action = $_POST['action'] ?? 'add';

if ($action === 'add') {
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
        header('Location: ../tier1_presentation/admin/trainers.php?success=Trainer+added+successfully');
    } else {
        header('Location: ../tier1_presentation/admin/trainers.php?error=' . urlencode($stmt->error));
    }
    $stmt->close();

} elseif ($action === 'edit') {
    $trainer_id     = (int)($_POST['trainer_id'] ?? 0);
    $name           = trim($_POST['name'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $phone          = trim($_POST['phone'] ?? '');

    if ($trainer_id === 0 || $name === '') {
        header('Location: ../tier1_presentation/admin/trainers.php?error=Invalid+data+submitted');
        exit;
    }

    $stmt = $conn->prepare("UPDATE trainers SET name=?, specialization=?, phone=? WHERE trainer_id=?");
    $stmt->bind_param("sssi", $name, $specialization, $phone, $trainer_id);

    if ($stmt->execute()) {
        header('Location: ../tier1_presentation/admin/trainers.php?success=Trainer+updated+successfully');
    } else {
        header('Location: ../tier1_presentation/admin/trainers.php?error=' . urlencode($stmt->error));
    }
    $stmt->close();

} elseif ($action === 'delete') {
    $trainer_id = (int)($_POST['trainer_id'] ?? 0);

    if ($trainer_id === 0) {
        header('Location: ../tier1_presentation/admin/trainers.php?error=Invalid+trainer+ID');
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM trainers WHERE trainer_id=?");
    $stmt->bind_param("i", $trainer_id);

    if ($stmt->execute()) {
        header('Location: ../tier1_presentation/admin/trainers.php?success=Trainer+deleted+successfully');
    } else {
        header('Location: ../tier1_presentation/admin/trainers.php?error=' . urlencode($stmt->error));
    }
    $stmt->close();

} else {
    header('Location: ../tier1_presentation/admin/trainers.php?error=Unknown+action');
}

$conn->close();
exit;
?>
