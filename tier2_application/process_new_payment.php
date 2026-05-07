<?php
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id'];
    $amount = $_POST['amount'];

    if (!empty($member_id) && !empty($amount)) {
        
        $sql = "INSERT INTO payments (member_id, amount, payment_date) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("id", $member_id, $amount);

        if ($stmt->execute()) {
            header("Location: ../tier1_presentation/admin/payments.php?status=success");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>