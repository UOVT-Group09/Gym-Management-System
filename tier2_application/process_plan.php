<?php
require_once 'admin_auth.php';
require_admin_auth('../tier1_presentation/login.php');
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../tier1_presentation/admin/membership_plans.php');
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $type_name       = trim($_POST['type_name'] ?? '');
    $amount          = trim($_POST['amount'] ?? '');
    $duration_months = (int)($_POST['duration_months'] ?? 0);

    if ($type_name === '' || $amount === '') {
        header('Location: ../tier1_presentation/admin/membership_plans.php?error=Plan+name+and+amount+are+required');
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO membership_types (type_name, amount, duration_months) VALUES (?, ?, ?)");
    $stmt->bind_param("sdi", $type_name, $amount, $duration_months);

    if ($stmt->execute()) {
        header('Location: ../tier1_presentation/admin/membership_plans.php?success=Plan+added+successfully');
    } else {
        header('Location: ../tier1_presentation/admin/membership_plans.php?error=' . urlencode($stmt->error));
    }
    $stmt->close();

} elseif ($action === 'edit') {
    $type_id         = (int)($_POST['type_id'] ?? 0);
    $type_name       = trim($_POST['type_name'] ?? '');
    $amount          = trim($_POST['amount'] ?? '');
    $duration_months = (int)($_POST['duration_months'] ?? 0);

    if ($type_id === 0 || $type_name === '' || $amount === '') {
        header('Location: ../tier1_presentation/admin/membership_plans.php?error=Invalid+data+submitted');
        exit;
    }

    $stmt = $conn->prepare("UPDATE membership_types SET type_name=?, amount=?, duration_months=? WHERE type_id=?");
    $stmt->bind_param("sdii", $type_name, $amount, $duration_months, $type_id);

    if ($stmt->execute()) {
        header('Location: ../tier1_presentation/admin/membership_plans.php?success=Plan+updated+successfully');
    } else {
        header('Location: ../tier1_presentation/admin/membership_plans.php?error=' . urlencode($stmt->error));
    }
    $stmt->close();

} elseif ($action === 'delete') {
    $type_id = (int)($_POST['type_id'] ?? 0);

    if ($type_id === 0) {
        header('Location: ../tier1_presentation/admin/membership_plans.php?error=Invalid+plan+ID');
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM membership_types WHERE type_id=?");
    $stmt->bind_param("i", $type_id);

    if ($stmt->execute()) {
        header('Location: ../tier1_presentation/admin/membership_plans.php?success=Plan+deleted+successfully');
    } else {
        header('Location: ../tier1_presentation/admin/membership_plans.php?error=' . urlencode($stmt->error));
    }
    $stmt->close();

} else {
    header('Location: ../tier1_presentation/admin/membership_plans.php?error=Unknown+action');
}

$conn->close();
exit;
?>
