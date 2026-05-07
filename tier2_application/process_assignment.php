<?php

require_once 'db_config.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $m_id = isset($_POST['member_id']) ? intval($_POST['member_id']) : 0;
    $t_id = isset($_POST['trainer_id']) ? intval($_POST['trainer_id']) : 0;

    if ($m_id > 0 && $t_id > 0) {
        
        $sql = "INSERT INTO member_trainer_assignments (member_id, trainer_id) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE trainer_id = VALUES(trainer_id)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $m_id, $t_id);
        
        if ($stmt->execute()) {
            
            header("Location: ../tier1_presentation/admin/userdata.php?status=success");
            exit();
        } else {
            
            echo "Error: Could not process assignment. " . $conn->error;
        }
        
        $stmt->close();
    } else {
        echo "Invalid Member or Trainer ID.";
    }
} else {
    
    header("Location: ../tier1_presentation/admin/userdata.php");
    exit();
}

$conn->close();
?>