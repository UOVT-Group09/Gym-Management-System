<?php
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = filter_var($_POST['member_id'] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    $amount = filter_var($_POST['amount'] ?? null, FILTER_VALIDATE_FLOAT);

    if ($member_id !== false && $amount !== false && $amount > 0) {
        
        $sql = "INSERT INTO payments (member_id, amount, payment_date) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("id", $member_id, $amount);

        if ($stmt->execute()) {
            header("Location: ../tier1_presentation/admin/payments.php?status=success");
            exit();
        } else {
            echo "Error: Unable to process payment.";
        }
    }
}
?>
