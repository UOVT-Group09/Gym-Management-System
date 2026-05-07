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
    $m_id = $_POST['member_id'];
    $days = $_POST['days'];
    

    $freeze_query = "CALL ApplyMemberFreeze($m_id, $days)";
    mysqli_query($conn, $freeze_query);
    
    header("Location: ../tier1_presentation/admin/userdata.php?status=frozen");
    exit();
}
?>
