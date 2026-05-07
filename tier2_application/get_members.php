<?php
require_once 'db_config.php';

$query = "SELECT m.member_id, m.full_name, m.email, m.phone, m.gender, m.join_date, 
                 mt.type_name, m.status, m.membership_end 
          FROM members m 
          LEFT JOIN membership_types mt ON m.type_id = mt.type_id";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}


if(isset($_POST['action']) && $_POST['action'] == 'freeze') {
    $m_id = filter_var($_POST['member_id'] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    $days = filter_var($_POST['days'] ?? null, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);

    if ($m_id !== false && $days !== false) {
        $freeze_stmt = $conn->prepare("CALL ApplyMemberFreeze(?, ?)");
        if ($freeze_stmt) {
            $freeze_stmt->bind_param("ii", $m_id, $days);
            $freeze_stmt->execute();
            $freeze_stmt->close();
        }
    }
    
    header("Location: ../tier1_presentation/admin/userdata.php?status=frozen");
    exit();
}
?>
