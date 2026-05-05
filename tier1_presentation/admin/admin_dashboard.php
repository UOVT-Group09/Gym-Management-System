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
    <style>
        body { background: #f0f2f5; }

        /* ── Page Header ─────────────────────────────── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .page-header h1 { font-size: 1.9rem; font-weight: 800; color: #2c3e50; margin: 0; }
        .page-header p  { font-size: 0.88rem; color: #7f8c8d; margin: 4px 0 0; }

        /* ── Summary Cards ───────────────────────────── */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 28px;
        }

        .summary-card {
            background: #fff;
            border-radius: 12px;
            padding: 22px 24px;
            display: flex;
            align-items: center;
            gap: 18px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            border-bottom: 4px solid #ffcc00;
            transition: transform 0.2s;
        }
        .summary-card:hover { transform: translateY(-3px); }

        .summary-icon {
            width: 50px;
            height: 50px;
            background: #fff8e1;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .summary-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: #2c3e50;
        }
        .summary-label {
            font-size: 0.72rem;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-top: 3px;
        }

        /* ── Table Card & Filter ──────────────────────────────── */
        .table-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            overflow: hidden;
        }

        .table-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid #f0f2f5;
        }

        .table-card-title {
            font-size: 1rem;
            font-weight: 700;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .count-badge {
            background: #ffcc00;
            color: #2c3e50;
            font-weight: 700;
            font-size: 0.78rem;
            padding: 4px 12px;
            border-radius: 999px;
            margin-left: 15px;
        }

        /* Professional Date Range Filter Styling */
        .filter-form {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .date-input-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .date-input-group label {
            font-size: 0.8rem;
            color: #7f8c8d;
            font-weight: 600;
        }

        .filter-input {
            padding: 7px 12px;
            border: 1px solid #dcdde1;
            border-radius: 6px;
            font-size: 0.85rem;
            color: #2c3e50;
            outline: none;
            cursor: pointer;
            background-color: #fff;
            font-family: inherit;
        }
        
        .filter-input:focus {
            border-color: #ffcc00;
            box-shadow: 0 0 0 2px rgba(255, 204, 0, 0.2);
        }

        .filter-btn {
            background-color: #2c3e50;
            color: #ffcc00;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-btn:hover {
            background-color: #1a252f;
        }

        .reset-btn {
            background-color: #f1f2f6;
            color: #7f8c8d;
            border: 1px solid #dcdde1;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .reset-btn:hover {
            background-color: #e0e4e8;
            color: #2c3e50;
        }

        /* ── Table ───────────────────────────────────── */
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .payment-table thead tr {
            background: #2c3e50;
            color: #ffcc00;
        }

        .payment-table thead th {
            padding: 13px 20px;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .payment-table tbody tr {
            border-bottom: 1px solid #f0f2f5;
            transition: background 0.15s;
        }
        .payment-table tbody tr:hover { background: #fffbea; }

        .payment-table td {
            padding: 14px 20px;
            color: #2c3e50;
            vertical-align: middle;
        }

        .cell-id    { color: #95a5a6; font-size: 0.82rem; font-weight: 600; }
        .cell-name  { font-weight: 600; }
        .cell-email { color: #7f8c8d; font-size: 0.85rem; }
        .cell-amount {
            font-weight: 800;
            color: #27ae60;
            font-size: 0.95rem;
        }
        .cell-date  { color: #95a5a6; font-size: 0.84rem; }

        /* Plan badge */
        .plan-badge {
            display: inline-block;
            padding: 3px 11px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            background: #fff8e1;
            color: #e6a800;
            border: 1px solid #ffe082;
        }

        .no-data {
            text-align: center;
            padding: 56px !important;
            color: #95a5a6;
            font-style: italic;
        }
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