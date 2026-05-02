<?php
require_once 'db_config.php';

$sql = "SELECT m.member_id, m.full_name, m.email, m.phone, 
               m.gender, mt.type_name, m.join_date 
        FROM members m
        JOIN membership_types mt ON m.type_id = mt.type_id
        ORDER BY m.member_id DESC";

$result = $conn->query($sql);
?>