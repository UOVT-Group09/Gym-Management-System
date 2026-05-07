<?php
require_once '../../tier2_application/db_config.php';

$total_revenue = 0;
$monthly_revenue = 0;
$total_payments = 0;
$date_condition = "";

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

if (!empty($start_date) && !empty($end_date)) {
    $date_condition = "WHERE DATE(p.payment_date) BETWEEN '$start_date' AND '$end_date'";
} elseif (!empty($start_date)) {
    $date_condition = "WHERE DATE(p.payment_date) >= '$start_date'";
} elseif (!empty($end_date)) {
    $date_condition = "WHERE DATE(p.payment_date) <= '$end_date'";
}


$rev_query = "SELECT SUM(amount) as total FROM payments";
$rev_result = $conn->query($rev_query);
if ($rev_result && $row = $rev_result->fetch_assoc()) {
    $total_revenue = $row['total'] ? $row['total'] : 0;
}

$month_query = "SELECT SUM(amount) as total FROM payments WHERE MONTH(payment_date) = MONTH(CURRENT_DATE()) AND YEAR(payment_date) = YEAR(CURRENT_DATE())";
$month_result = $conn->query($month_query);
if ($month_result && $row = $month_result->fetch_assoc()) {
    $monthly_revenue = $row['total'] ? $row['total'] : 0;
}

$payments_query = "
    SELECT p.payment_id, p.amount, p.payment_date, m.full_name, m.email, mt.type_name 
    FROM payments p
    LEFT JOIN members m ON p.member_id = m.member_id
    LEFT JOIN membership_types mt ON m.type_id = mt.type_id
    $date_condition
    ORDER BY p.payment_date DESC
";
$payments_result = $conn->query($payments_query);
if ($payments_result) { $total_payments = $payments_result->num_rows; }


$alerts = [];
$alerts_result = @$conn->query("
    SELECT pa.alert_id, pa.alert_type, pa.amount, pa.detected_at, m.full_name AS member_name 
    FROM payment_alerts pa 
    LEFT JOIN members m ON pa.member_id = m.member_id 
    ORDER BY pa.detected_at DESC LIMIT 10");
if ($alerts_result) { while ($row = $alerts_result->fetch_assoc()) { $alerts[] = $row; } }


$members_list = $conn->query("SELECT m.member_id, m.full_name, mt.amount, mt.type_name FROM members m JOIN membership_types mt ON m.type_id = mt.type_id ORDER BY m.full_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Fitness Hub</title>
    <link rel="stylesheet" href="dashboard_style.css">
    <link rel="stylesheet" href="payments_style.css">
    <style>
        .add-payment-card { background: #fff; border-radius: 10px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e0e0e0; }
        .form-row { display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-end; }
        .form-group { flex: 1; min-width: 200px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; background: #fdfdfd; }
        .btn-submit { background: #27ae60; color: white; border: none; padding: 11px 25px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.3s; }
        .btn-submit:hover { background: #219150; }
        
        .alerts-panel { background: #fff8f0; border: 1px solid #f5c97a; border-left: 4px solid #e6a800; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; }
        .alert-badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .alert-badge.NEGATIVE  { background: #fde8e8; color: #c0392b; }
        .alert-badge.HIGH_VALUE { background: #fdf3e8; color: #d35400; }
        .alert-badge.DUPLICATE  { background: #eaf0fb; color: #2980b9; }
    </style>
</head>
<body>
    <?php $active_page = 'payments'; include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <header class="topbar">
            <div class="topbar-title">
                <h1>Payments</h1>
                <p>Track all member payments and revenue</p>
            </div>
            <div class="topbar-meta">
                <div class="topbar-date"><?php echo date('l, F j, Y'); ?></div>
            </div>
        </header>

        <div class="add-payment-card">
            <h3 style="margin-top:0; margin-bottom:15px; color:#2c3e50;">➕ Add New Payment</h3>
            <form action="../../tier2_application/process_new_payment.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Select Member</label>
                        <select name="member_id" id="member_select" class="form-control" required onchange="updatePaymentDetails()">
                            <option value="">-- Choose Member --</option>
                            <?php while($m = $members_list->fetch_assoc()): ?>
                                <option value="<?php echo $m['member_id']; ?>" 
                                        data-price="<?php echo $m['amount']; ?>" 
                                        data-plan="<?php echo htmlspecialchars($m['type_name']); ?>">
                                    <?php echo $m['member_id'] . " - " . htmlspecialchars($m['full_name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Membership Plan</label>
                        <input type="text" id="display_plan" class="form-control" readonly placeholder="Auto-filled">
                    </div>
                    <div class="form-group">
                        <label>Amount (Rs.)</label>
                        <input type="number" step="0.01" name="amount" id="display_amount" class="form-control" required>
                    </div>
                    <div class="form-group" style="flex:0;">
                        <button type="submit" class="btn-submit">Record Payment</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-value">Rs. <?php echo number_format($total_revenue, 2); ?></div>
                <div class="summary-label">Total Revenue</div>
            </div>
            <div class="summary-card">
                <div class="summary-value">Rs. <?php echo number_format($monthly_revenue, 2); ?></div>
                <div class="summary-label">This Month</div>
            </div>
            <div class="summary-card">
                <div class="summary-value"><?php echo $total_payments; ?></div>
                <div class="summary-label">Filtered Transactions</div>
            </div>
        </div>

        <div class="alerts-panel">
            <div class="alerts-panel-header">
                <h3>⚠️ Payment Anomaly Alerts</h3>
            </div>
            <?php if (!empty($alerts)): ?>
                <table class="alerts-table">
                    <thead>
                        <tr><th>Member</th><th>Alert Type</th><th>Amount</th><th>Time</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alerts as $alert): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($alert['member_name'] ?? '—'); ?></td>
                                <td><span class="alert-badge <?php echo htmlspecialchars($alert['alert_type']); ?>"><?php echo htmlspecialchars($alert['alert_type']); ?></span></td>
                                <td style="color:#c0392b; font-weight:bold;">Rs. <?php echo number_format($alert['amount'], 2); ?></td>
                                <td><?php echo date('M j, g:i A', strtotime($alert['detected_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-alerts">✅ No anomalies detected.</p>
            <?php endif; ?>
        </div>

        <div class="table-card">
            <div class="table-card-header">
                <h3>Payment History</h3>
                <form method="GET" class="filter-form">
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>">
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>">
                    <button type="submit" class="filter-btn">Filter</button>
                </form>
            </div>
            <table class="payment-table">
                <thead>
                    <tr><th>#</th><th>Member Name</th><th>Plan</th><th>Amount</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php if ($payments_result->num_rows > 0): ?>
                        <?php while ($row = $payments_result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['payment_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><span class="plan-badge"><?php echo htmlspecialchars($row['type_name']); ?></span></td>
                                <td>Rs. <?php echo number_format($row['amount'], 2); ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($row['payment_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function updatePaymentDetails() {
        var select = document.getElementById('member_select');
        var selectedOption = select.options[select.selectedIndex];
        document.getElementById('display_amount').value = selectedOption.getAttribute('data-price') || "";
        document.getElementById('display_plan').value = selectedOption.getAttribute('data-plan') || "";
    }
    </script>
</body>
</html>
