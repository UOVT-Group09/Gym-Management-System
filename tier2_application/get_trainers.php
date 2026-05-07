<?php
require_once 'db_config.php';


$trainer_count = 0;
$r = $conn->query("SELECT COUNT(*) AS total FROM trainers");
if ($r) {
    $trainer_count = $r->fetch_assoc()['total'];
}


$query = "SELECT 
            trainer_id, 
            name, 
            specialization, 
            phone, 
            current_load, 
            (current_load >= 10) AS is_overloaded 
          FROM trainers 
          ORDER BY name ASC";

$trainers_result = $conn->query($query);


 
function fetchAllTrainers($conn) {
    $sql = "SELECT *, (current_load >= 10) AS is_overloaded FROM trainers ORDER BY name ASC";
    return $conn->query($sql);
}
?>