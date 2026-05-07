<?php
require_once 'db_config.php';

$class_count = 0;
$r = $conn->query("SELECT COUNT(*) AS total FROM classes");
if ($r) {
    $class_count = (int)$r->fetch_assoc()['total'];
}

$classes_query = "SELECT c.class_id, c.class_name, c.capacity, c.scheduled_at, c.duration_minutes,
                        t.trainer_id, t.name AS trainer_name,
                        SUM(CASE WHEN ce.status = 'Enrolled' THEN 1 ELSE 0 END) AS enrolled_count,
                        SUM(CASE WHEN ce.status = 'Waitlisted' THEN 1 ELSE 0 END) AS waitlist_count
                 FROM classes c
                 LEFT JOIN trainers t ON c.trainer_id = t.trainer_id
                 LEFT JOIN class_enrollments ce ON ce.class_id = c.class_id
                 GROUP BY c.class_id, c.class_name, c.capacity, c.scheduled_at, c.duration_minutes, t.trainer_id, t.name
                 ORDER BY c.scheduled_at ASC";

$classes_result = $conn->query($classes_query);

$trainers_result = $conn->query("SELECT trainer_id, name FROM trainers ORDER BY name ASC");

$has_status = false;
$col_check = $conn->query("SELECT COUNT(*) AS cnt FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'members' AND column_name = 'status'");
if ($col_check) {
    $row = $col_check->fetch_assoc();
    $has_status = $row && (int)$row['cnt'] > 0;
}

if ($has_status) {
    $active_members_result = $conn->query("SELECT member_id, full_name FROM members WHERE status = 'Active' ORDER BY full_name ASC");
} else {
    $active_members_result = $conn->query("SELECT member_id, full_name FROM members ORDER BY full_name ASC");
}

$enrollments_by_class = [];
$enrollments_query = "SELECT ce.class_id, ce.member_id, ce.status, ce.waitlisted_at, ce.enrollment_id, m.full_name
                      FROM class_enrollments ce
                      INNER JOIN members m ON ce.member_id = m.member_id
                      WHERE ce.status IN ('Enrolled', 'Waitlisted')
                      ORDER BY ce.class_id, FIELD(ce.status, 'Enrolled', 'Waitlisted'), ce.waitlisted_at, ce.enrollment_id";
$enrollments_result = $conn->query($enrollments_query);
if ($enrollments_result) {
    while ($row = $enrollments_result->fetch_assoc()) {
        $class_id = (int)$row['class_id'];
        $enrollments_by_class[$class_id][] = $row;
    }
}
?>