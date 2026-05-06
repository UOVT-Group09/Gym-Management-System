<?php
$conn = new mysqli("localhost", "root", "root", "gym_management");

if (isset($_POST['enroll'])) {

    $m_id = $_POST['member_id'];
    $c_id = $_POST['class_id'];

    $stmt = $conn->prepare("CALL EnrollMemberInClass(?, ?)");
    $stmt->bind_param("ii", $m_id, $c_id);
    $stmt->execute();

    header("Location: ../tier1_presentation/admin/classes.php");
}
?>