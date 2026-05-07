<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../tier1_presentation/admin/classes.php');
    exit;
}

$action = $_POST['action'] ?? '';
$class_id = filter_var($_POST['class_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$member_id = filter_var($_POST['member_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($class_id === false || $member_id === false) {
    header('Location: ../tier1_presentation/admin/classes.php?error=Invalid+class+or+member');
    exit;
}

$has_status = false;
$col_check = $conn->query("SELECT COUNT(*) AS cnt FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'members' AND column_name = 'status'");
if ($col_check) {
    $row = $col_check->fetch_assoc();
    $has_status = $row && (int)$row['cnt'] > 0;
}

if (!$has_status) {
    header('Location: ../tier1_presentation/admin/classes.php?error=Database+missing+members.status.+Apply+schema+updates+first');
    exit;
}

if ($action === 'enroll') {
    $stmt = $conn->prepare("CALL EnrollMemberInClass(?, ?)");
    $stmt->bind_param("ii", $member_id, $class_id);
    $success_message = 'Enrollment+processed+successfully';
} elseif ($action === 'cancel') {
    $stmt = $conn->prepare("CALL CancelEnrollment(?, ?)");
    $stmt->bind_param("ii", $member_id, $class_id);
    $success_message = 'Enrollment+cancelled+successfully';
} else {
    header('Location: ../tier1_presentation/admin/classes.php?error=Unknown+action');
    exit;
}

if ($stmt->execute()) {
    header('Location: ../tier1_presentation/admin/classes.php?success=' . $success_message);
} else {
    header('Location: ../tier1_presentation/admin/classes.php?error=' . urlencode($stmt->error));
}

$stmt->close();
$conn->close();
exit;
?>