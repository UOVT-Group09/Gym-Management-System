<?php
require_once 'db_config.php';

// Summary stats
$total_revenue = 0;
$r = $conn->query("SELECT COALESCE(SUM(amount), 0) AS total FROM payments");
if ($r) $total_revenue = $r->fetch_assoc()['total'];

// අලුත් View එක භාවිතයෙන් මේ මාසයේ ආදායම ලබා ගැනීම (Monthly Revenue Snapshot View)
$monthly_revenue = 0;
$r = $conn->query("SELECT total_revenue FROM vw_monthly_revenue 
                   WHERE rev_year = YEAR(CURRENT_DATE()) 
                   AND rev_month = MONTH(CURRENT_DATE())");
if ($r && $row = $r->fetch_assoc()) {
    $monthly_revenue = $row['total_revenue'];
}

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