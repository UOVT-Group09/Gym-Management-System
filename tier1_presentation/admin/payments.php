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


if ($payments_result) {
    $total_payments = $payments_result->num_rows;
}


$alerts = [];
$alerts_result = @$conn->query("
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
      
        .alerts-panel {
            background: #fff8f0;
            border: 1px solid #f5c97a;
            border-left: 4px solid #e6a800;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }
        .alerts-panel-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        .alerts-panel-header h3 {
            margin: 0;
            font-size: 15px;
            color: #7a5500;
        }
        .alerts-panel .no-alerts {
            color: #999;
            font-size: 13px;
            text-align: center;
            padding: 10px 0;
        }
        .alerts-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .alerts-table thead th {
            background: #ffefc7;
            color: #7a5500;
            padding: 8px 12px;
            text-align: left;
            font-weight: 600;
        }
        .alerts-table tbody tr:hover {
            background: #fff3d6;
        }
        .alerts-table tbody td {
            padding: 7px 12px;
            border-bottom: 1px solid #fde8b0;
            color: #3d3d3d;
        }
        .alert-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
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
                <div class="topbar-admin">Welcome, <strong>Admin</strong></div>
            </div>
        </header>

        <div class="summary-grid">

            <div class="summary-card">
                <div class="summary-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#e6a800" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <div>
                    <div class="summary-value">Rs. <?php echo number_format($total_revenue, 2); ?></div>
                    <div class="summary-label">Total Revenue</div>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#e6a800" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <div>
                    <div class="summary-value">Rs. <?php echo number_format($monthly_revenue, 2); ?></div>
                    <div class="summary-label">This Month</div>
                </div>
            </div>

            <div class="summary-card">
                <div class="summary-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#e6a800" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                </div>
                <div>
                    <div class="summary-value"><?php echo $total_payments; ?></div>
                    <div class="summary-label">Filtered Transactions</div>
                </div>
            </div>

        </div>

      
        <div class="alerts-panel">
            <div class="alerts-panel-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e6a800" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <h3>Payment Anomaly Alerts <span style="font-weight:400; font-size:12px; color:#aaa;">(latest 10)</span></h3>
            </div>

            <?php if (!empty($alerts)): ?>
                <table class="alerts-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Member</th>
                            <th>Alert Type</th>
                            <th>Amount</th>
                            <th>Detected At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alerts as $i => $alert): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><?php echo htmlspecialchars($alert['member_name'] ?? '—'); ?></td>
                                <td>
                                    <span class="alert-badge <?php echo htmlspecialchars($alert['alert_type']); ?>">
                                        <?php echo htmlspecialchars($alert['alert_type']); ?>
                                    </span>
                                </td>
                                <td>Rs. <?php echo number_format($alert['amount'], 2); ?></td>
                                <td><?php echo date('M j, Y  g:i A', strtotime($alert['detected_at'])); ?></td>
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
                <div class="table-card-title">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2c3e50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                        Payment History
                        <span class="count-badge"><?php echo $total_payments; ?> Records</span>
                    </div>
                </div>
                
                <form method="GET" action="payments.php" class="filter-form">
                    <div class="date-input-group">
                        <label for="start_date">From:</label>
                        <input type="date" id="start_date" name="start_date" class="filter-input" value="<?php echo htmlspecialchars($start_date); ?>">
                    </div>
                    
                    <div class="date-input-group">
                        <label for="end_date">To:</label>
                        <input type="date" id="end_date" name="end_date" class="filter-input" value="<?php echo htmlspecialchars($end_date); ?>">
                    </div>

                    <button type="submit" class="filter-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                        </svg>
                        Filter
                    </button>
                    
                    <?php if(!empty($start_date) || !empty($end_date)): ?>
                        <a href="payments.php" class="reset-btn">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <table class="payment-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Member Name</th>
                        <th>Email</th>
                        <th>Membership Plan</th>
                        <th>Amount</th>
                        <th>Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($payments_result && $payments_result->num_rows > 0): ?>
                    <?php while ($row = $payments_result->fetch_assoc()): ?>
                        <tr>
                            <td class="cell-id">#<?php echo htmlspecialchars($row['payment_id']); ?></td>
                            <td class="cell-name"><?php echo htmlspecialchars($row['full_name'] ?? '—'); ?></td>
                            <td class="cell-email"><?php echo htmlspecialchars($row['email'] ?? '—'); ?></td>
                            <td>
                                <span class="plan-badge"><?php echo htmlspecialchars($row['type_name'] ?? '—'); ?></span>
                            </td>
                            <td class="cell-amount">Rs. <?php echo number_format($row['amount'], 2); ?></td>
                            <td class="cell-date"><?php echo date('M j, Y  g:i A', strtotime($row['payment_date'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-data">No payment records found for the selected dates</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="includes/alerts.js"></script>
</body>
</html>