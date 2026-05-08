<?php
require_once 'admin_auth.php';
require_admin_auth('../tier1_presentation/login.php');
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../tier1_presentation/admin/classes.php');
    exit;
}

function normalize_datetime($value) {
    $value = trim($value ?? '');
    if ($value === '') {
        return null;
    }
    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return null;
    }
    return date('Y-m-d H:i:s', $timestamp);
}

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $class_name = trim($_POST['class_name'] ?? '');
    $capacity = filter_var($_POST['capacity'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $trainer_id = filter_var($_POST['trainer_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $scheduled_at = normalize_datetime($_POST['scheduled_at'] ?? '');
    $duration_minutes = trim($_POST['duration_minutes'] ?? '');
    $duration_minutes = $duration_minutes !== '' ? filter_var($duration_minutes, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : null;

    if ($class_name === '' || $capacity === false || $scheduled_at === null) {
        header('Location: ../tier1_presentation/admin/classes.php?error=Class+name,+capacity,+and+schedule+are+required');
        exit;
    }

    if ($duration_minutes === false) {
        header('Location: ../tier1_presentation/admin/classes.php?error=Invalid+duration');
        exit;
    }

    if ($trainer_id === false) {
        $trainer_id = null;
    }

    $stmt = $conn->prepare("INSERT INTO classes (class_name, capacity, trainer_id, scheduled_at, duration_minutes)
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("siisi", $class_name, $capacity, $trainer_id, $scheduled_at, $duration_minutes);

    if ($stmt->execute()) {
        header('Location: ../tier1_presentation/admin/classes.php?success=Class+added+successfully');
    } else {
        header('Location: ../tier1_presentation/admin/classes.php?error=' . urlencode($stmt->error));
    }
    $stmt->close();

} elseif ($action === 'edit') {
    $class_id = filter_var($_POST['class_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $class_name = trim($_POST['class_name'] ?? '');
    $capacity = filter_var($_POST['capacity'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $trainer_id = filter_var($_POST['trainer_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $scheduled_at = normalize_datetime($_POST['scheduled_at'] ?? '');
    $duration_minutes = trim($_POST['duration_minutes'] ?? '');
    $duration_minutes = $duration_minutes !== '' ? filter_var($duration_minutes, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : null;

    if ($class_id === false || $class_name === '' || $capacity === false || $scheduled_at === null) {
        header('Location: ../tier1_presentation/admin/classes.php?error=Invalid+class+data+submitted');
        exit;
    }

    if ($duration_minutes === false) {
        header('Location: ../tier1_presentation/admin/classes.php?error=Invalid+duration');
        exit;
    }

    if ($trainer_id === false) {
        $trainer_id = null;
    }

    $stmt = $conn->prepare("UPDATE classes
                            SET class_name = ?, capacity = ?, trainer_id = ?, scheduled_at = ?, duration_minutes = ?
                            WHERE class_id = ?");
    $stmt->bind_param("siisii", $class_name, $capacity, $trainer_id, $scheduled_at, $duration_minutes, $class_id);

    if ($stmt->execute()) {
        header('Location: ../tier1_presentation/admin/classes.php?success=Class+updated+successfully');
    } else {
        header('Location: ../tier1_presentation/admin/classes.php?error=' . urlencode($stmt->error));
    }
    $stmt->close();

} elseif ($action === 'delete') {
    $class_id = filter_var($_POST['class_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($class_id === false) {
        header('Location: ../tier1_presentation/admin/classes.php?error=Invalid+class+ID');
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM classes WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);

    if ($stmt->execute()) {
        header('Location: ../tier1_presentation/admin/classes.php?success=Class+deleted+successfully');
    } else {
        header('Location: ../tier1_presentation/admin/classes.php?error=' . urlencode($stmt->error));
    }
    $stmt->close();

} else {
    header('Location: ../tier1_presentation/admin/classes.php?error=Unknown+action');
}

$conn->close();
exit;
?>