<?php
require_once 'db_config.php';

$trainer_count = 0;
$r = $conn->query("SELECT COUNT(*) AS total FROM trainers");
if ($r) $trainer_count = $r->fetch_assoc()['total'];

$trainers_result = $conn->query("SELECT trainer_id, name, specialization, phone FROM trainers ORDER BY name ASC");
?>
