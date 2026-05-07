<?php
require_once 'db_config.php';

if (!function_exists('column_exists')) {
    function column_exists(mysqli $conn, string $table, string $column): bool {
        $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ss", $table, $column);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row && (int)$row['cnt'] > 0;
    }
}

$trainer_count = 0;
$r = $conn->query("SELECT COUNT(*) AS total FROM trainers");
if ($r) {
    $trainer_count = $r->fetch_assoc()['total'];
}

$has_current_load = column_exists($conn, 'trainers', 'current_load');

if ($has_current_load) {
    $query = "SELECT 
                trainer_id, 
                name, 
                specialization, 
                phone, 
                current_load, 
                (current_load >= 10) AS is_overloaded 
              FROM trainers 
              ORDER BY name ASC";
} else {
    $query = "SELECT 
                trainer_id, 
                name, 
                specialization, 
                phone, 
                0 AS current_load, 
                0 AS is_overloaded 
              FROM trainers 
              ORDER BY name ASC";
}

$trainers_result = $conn->query($query);

function fetchAllTrainers($conn) {
    $has_current_load = column_exists($conn, 'trainers', 'current_load');
    if ($has_current_load) {
        $sql = "SELECT trainer_id, name, specialization, phone, current_load, (current_load >= 10) AS is_overloaded FROM trainers ORDER BY name ASC";
    } else {
        $sql = "SELECT trainer_id, name, specialization, phone, 0 AS current_load, 0 AS is_overloaded FROM trainers ORDER BY name ASC";
    }
    return $conn->query($sql);
}
?>
