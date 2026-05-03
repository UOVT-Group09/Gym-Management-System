<?php
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $gender    = trim($_POST['gender'] ?? '');
    $type_id   = trim($_POST['type_id'] ?? '');

    $allowed_genders = ['male', 'female', 'other'];

    if (empty($full_name) || empty($email) || empty($gender) || empty($type_id)) {
        die("Error: Please fill all required fields.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Error: Invalid email format.");
    }

    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        die("Error: Phone number must be 10 digits.");
    }

    if (!in_array(strtolower(trim($gender)), $allowed_genders, true)) {
        die("Error: Invalid gender value.");
    }

    $validated_type_id = filter_var($type_id, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    if ($validated_type_id === false) {
        die("Error: Invalid membership type.");
    }

    $type_id = (int)$validated_type_id;

    try {
        
        $sql = "CALL RegisterNewMember(?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $full_name, $email, $phone, $gender, $type_id);

        if ($stmt->execute()) {
            
            header("Location: ../tier1_presentation/index.php?status=success");
            exit();
        } else {
            error_log("Registration failed: " . $stmt->error);
            echo "Error: Unable to process registration at this time.";
        }
        $stmt->close();
    } catch (Exception $e) {
        error_log("Registration exception: " . $e->getMessage());
        echo "Error: Unable to process registration at this time.";
    }

    $conn->close();
}
?>