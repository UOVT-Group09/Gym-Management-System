<?php
require_once 'db_config.php';

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
function normalize_date($value) {
    if (empty($value)) {
        return '';
    }

    $dt = DateTime::createFromFormat('Y-m-d', $value);
    if ($dt && $dt->format('Y-m-d') === $value) {
        return $value;
    }

    return '';
}

$start_date = normalize_date($start_date);
$end_date = normalize_date($end_date);

$total_revenue = 0;
$r1 = $conn->query("SELECT COALESCE(SUM(amount), 0) AS total FROM payments");
if ($r1) $total_revenue = $r1->fetch_assoc()['total'];

$monthly_revenue = 0;
$r2 = $conn->query("SELECT COALESCE(SUM(amount), 0) AS total FROM payments
                   WHERE MONTH(payment_date) = MONTH(CURRENT_DATE())
                   AND YEAR(payment_date) = YEAR(CURRENT_DATE())");
if ($r2) $monthly_revenue = $r2->fetch_assoc()['total'];

$base_query = "
    SELECT
        p.payment_id,
        m.full_name,
        m.email,
        mt.type_name,
        p.amount,
        p.payment_date
    FROM payments p
    LEFT JOIN members m ON p.member_id = m.member_id
    LEFT JOIN membership_types mt ON m.type_id = mt.type_id
";

if (!empty($start_date) && !empty($end_date)) {
    $stmt = $conn->prepare($base_query . " WHERE DATE(p.payment_date) BETWEEN ? AND ? ORDER BY p.payment_date DESC");
    if ($stmt) {
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        $payments_result = $stmt->get_result();
        $stmt->close();
    } else {
        $payments_result = false;
    }
} elseif (!empty($start_date)) {
    $stmt = $conn->prepare($base_query . " WHERE DATE(p.payment_date) >= ? ORDER BY p.payment_date DESC");
    if ($stmt) {
        $stmt->bind_param("s", $start_date);
        $stmt->execute();
        $payments_result = $stmt->get_result();
        $stmt->close();
    } else {
        $payments_result = false;
    }
} elseif (!empty($end_date)) {
    $stmt = $conn->prepare($base_query . " WHERE DATE(p.payment_date) <= ? ORDER BY p.payment_date DESC");
    if ($stmt) {
        $stmt->bind_param("s", $end_date);
        $stmt->execute();
        $payments_result = $stmt->get_result();
        $stmt->close();
    } else {
        $payments_result = false;
    }
} else {
    $payments_result = $conn->query($base_query . " ORDER BY p.payment_date DESC");
}

$total_payments = 0;
if ($payments_result) {
    $total_payments = $payments_result->num_rows;
} else {
    $r3 = $conn->query("SELECT COUNT(*) AS total FROM payments");
    if ($r3) $total_payments = $r3->fetch_assoc()['total'];
}

$alerts = [];
$alerts_result = $conn->query("
    SELECT
        pa.alert_id,
        pa.alert_type,
        pa.amount,
        pa.detected_at,
        m.full_name AS member_name
    FROM payment_alerts pa
    LEFT JOIN members m ON pa.member_id = m.member_id
    ORDER BY pa.detected_at DESC
    LIMIT 10
");
if ($alerts_result) {
    while ($row = $alerts_result->fetch_assoc()) {
        $alerts[] = $row;
    }
}

$members_list = $conn->query("SELECT m.member_id, m.full_name, mt.amount, mt.type_name FROM members m JOIN membership_types mt ON m.type_id = mt.type_id ORDER BY m.full_name ASC");
?>
