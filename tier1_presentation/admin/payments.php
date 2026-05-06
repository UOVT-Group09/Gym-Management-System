<?php
// Connect to Tier 2 Application Logic
require_once '../../tier2_application/get_payments.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - Fitness Hub</title>
    <link rel="stylesheet" href="dashboard_style.css">
    <link rel="stylesheet" href="payments_style.css">
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
                        <input type="date" id="start_date" name="start_date" class="filter-input" value="<?php echo htmlspecialchars($start_date ?? ''); ?>">
                    </div>
                    
                    <div class="date-input-group">
                        <label for="end_date">To:</label>
                        <input type="date" id="end_date" name="end_date" class="filter-input" value="<?php echo htmlspecialchars($end_date ?? ''); ?>">
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
                <?php if (isset($payments_result) && $payments_result->num_rows > 0): ?>
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

</body>
</html>