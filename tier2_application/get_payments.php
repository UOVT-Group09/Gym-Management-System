<?php
require_once 'db_config.php';

// Summary stats
$total_revenue = 0;
$r = $conn->query("SELECT COALESCE(SUM(amount), 0) AS total FROM payments");
if ($r) $total_revenue = $r->fetch_assoc()['total'];

$monthly_revenue = 0;
$r = $conn->query("SELECT COALESCE(SUM(amount), 0) AS total FROM payments
                   WHERE MONTH(payment_date) = MONTH(CURRENT_DATE())
                   AND YEAR(payment_date) = YEAR(CURRENT_DATE())");
if ($r) $monthly_revenue = $r->fetch_assoc()['total'];

$total_payments = 0;
$r = $conn->query("SELECT COUNT(*) AS total FROM payments");
if ($r) $total_payments = $r->fetch_assoc()['total'];

// All payments with member name and plan
$payments_result = $conn->query("
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
    ORDER BY p.payment_date DESC
");
?>
