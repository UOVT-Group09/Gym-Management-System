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


if (isset($_POST['action'])) {
    $m_id = (int)$_POST['member_id'];

    if ($_POST['action'] === 'freeze') {
        $days = (int)$_POST['days'];
        $stmt = $conn->prepare("CALL ApplyMemberFreeze(?, ?)");
        $stmt->bind_param("ii", $m_id, $days);
        $stmt->execute();
        $stmt->close();
        header("Location: ../tier1_presentation/admin/userdata.php?success=Member+frozen+for+30+days.");
        exit();
    }

    if ($_POST['action'] === 'unfreeze') {
        $stmt = $conn->prepare("UPDATE members SET status = 'Active' WHERE member_id = ?");
        $stmt->bind_param("i", $m_id);
        $stmt->execute();
        $stmt->close();
        header("Location: ../tier1_presentation/admin/userdata.php?success=Member+successfully+unfrozen.");
        exit();
    }
}
