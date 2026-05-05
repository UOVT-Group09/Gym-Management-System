<?php
require_once '../../tier2_application/db_config.php';

$plan_count = 0;
$r = $conn->query("SELECT COUNT(*) AS total FROM membership_types");
if ($r) $plan_count = $r->fetch_assoc()['total'];

$plans_result = $conn->query("SELECT type_id, type_name, amount, duration_months FROM membership_types ORDER BY type_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Plans - Fitness Hub</title>
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        body { background: #f0f2f5; }

        /* ── Page Header ─────────────────────────────── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .page-header h1 { font-size: 1.9rem; font-weight: 800; color: #2c3e50; margin: 0; }
        .page-header p  { font-size: 0.88rem; color: #7f8c8d; margin: 4px 0 0; }

        .header-right { display: flex; align-items: center; gap: 14px; }

        .count-badge {
            background: #ffcc00;
            color: #2c3e50;
            font-weight: 700;
            font-size: 0.82rem;
            padding: 7px 16px;
            border-radius: 999px;
        }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            background: #2c3e50;
            color: #ffcc00;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.2s, transform 0.15s;
        }
        .btn-add:hover { background: #1a252f; transform: translateY(-1px); }

        /* ── Alert ───────────────────────────────────── */
        .alert {
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: #eafaf1; color: #1e8449; border: 1px solid #a9dfbf; }
        .alert-error   { background: #fdedec; color: #c0392b; border: 1px solid #f1948a; }

        /* ── Table Card ──────────────────────────────── */
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

        .plans-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .plans-table thead tr { background: #2c3e50; color: #ffcc00; }
        .plans-table thead th {
            padding: 13px 22px;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .plans-table tbody tr {
            border-bottom: 1px solid #f0f2f5;
            transition: background 0.15s;
        }
        .plans-table tbody tr:hover { background: #fffbea; }

        .plans-table td {
            padding: 15px 22px;
            color: #2c3e50;
            vertical-align: middle;
        }

        .cell-id { color: #95a5a6; font-size: 0.82rem; font-weight: 600; width: 60px; }

        .plan-name {
            font-weight: 700;
            font-size: 0.95rem;
        }

        .plan-icon-wrap {
            width: 38px;
            height: 38px;
            background: #fff8e1;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f39c12;
            flex-shrink: 0;
        }

        .name-cell { display: flex; align-items: center; gap: 12px; }

        .amount-text {
            font-weight: 700;
            color: #1e8449;
            font-size: 0.95rem;
        }

        .duration-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
            background: #eaf4fb;
            color: #2980b9;
            border: 1px solid #aed6f1;
        }

        /* ── Row Actions ─────────────────────────────── */
        .action-btns { display: flex; gap: 8px; }

        .btn-edit, .btn-delete {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 7px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-family: inherit;
            transition: background 0.2s, transform 0.15s;
        }

        .btn-edit   { background: #eaf4fb; color: #2980b9; }
        .btn-edit:hover { background: #d4e9f7; transform: translateY(-1px); }

        .btn-delete { background: #fdedec; color: #c0392b; }
        .btn-delete:hover { background: #fad7d3; transform: translateY(-1px); }

        .empty-state {
            text-align: center;
            padding: 64px 24px;
            color: #95a5a6;
        }
        .empty-state svg { opacity: 0.3; margin-bottom: 16px; }
        .empty-state p   { font-size: 0.95rem; margin: 0; }

        /* ── Modal Overlay ───────────────────────────── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(3px);
        }
        .modal-overlay.open { display: flex; }

        /* ── Modal Box ───────────────────────────────── */
        .modal {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
            animation: slideUp 0.25s ease;
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }

        .modal-header {
            background: #2c3e50;
            padding: 22px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header-left { display: flex; align-items: center; gap: 14px; }

        .modal-icon {
            width: 42px;
            height: 42px;
            background: rgba(255,204,0,0.15);
            border: 1px solid rgba(255,204,0,0.3);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffcc00;
        }

        .modal-icon.danger {
            background: rgba(231,76,60,0.15);
            border-color: rgba(231,76,60,0.3);
            color: #e74c3c;
        }

        .modal-title    { font-size: 1.05rem; font-weight: 700; color: #fff; }
        .modal-subtitle { font-size: 0.75rem; color: #7f8c9a; margin-top: 2px; }

        .modal-close {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.08);
            border: none;
            border-radius: 8px;
            color: #bdc3c7;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s, color 0.2s;
        }
        .modal-close:hover { background: rgba(255,255,255,0.16); color: #fff; }

        /* ── Modal Form ──────────────────────────────── */
        .modal-body { padding: 28px; display: flex; flex-direction: column; gap: 18px; }

        .form-row-modal { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

        .form-group { display: flex; flex-direction: column; gap: 7px; }

        .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #2c3e50;
        }
        .required { color: #e74c3c; }

        .input-wrap { position: relative; }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #bdc3c7;
            display: flex;
            align-items: center;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid #e8ecf0;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #2c3e50;
            background: #fafbfc;
            box-sizing: border-box;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-input::placeholder { color: #c8d0d8; }
        .form-input:focus {
            outline: none;
            border-color: #ffcc00;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(255,204,0,0.15);
        }

        .modal-footer {
            padding: 0 28px 28px;
            display: flex;
            gap: 12px;
        }

        .btn-submit {
            flex: 1;
            padding: 12px;
            background: #2c3e50;
            color: #ffcc00;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: inherit;
            transition: background 0.2s;
        }
        .btn-submit:hover { background: #1a252f; }

        .btn-submit.danger {
            background: #c0392b;
            color: #fff;
        }
        .btn-submit.danger:hover { background: #a93226; }

        .btn-cancel {
            padding: 12px 20px;
            background: #f4f6f8;
            color: #7f8c8d;
            border: 1.5px solid #e0e4e8;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.2s;
        }
        .btn-cancel:hover { background: #eaecef; }

        /* ── Delete confirm text ─────────────────────── */
        .delete-confirm-text {
            padding: 24px 28px;
            color: #7f8c8d;
            font-size: 0.92rem;
            line-height: 1.6;
        }
        .delete-confirm-text strong { color: #2c3e50; }
    </style>
</head>
<body>

    <?php $active_page = 'plans'; include 'includes/sidebar.php'; ?>

    <div class="main-content">

        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-title">
                <h1>Membership Plans</h1>
                <p>Manage available membership plans and pricing</p>
            </div>
            <div class="topbar-meta">
                <div class="topbar-date"><?php echo date('l, F j, Y'); ?></div>
                <div class="topbar-admin">Welcome, <strong>Admin</strong></div>
            </div>
        </header>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>All Plans</h1>
                <p>Create, edit, or remove membership plans</p>
            </div>
            <div class="header-right">
                <span class="count-badge"><?php echo $plan_count; ?> Plans</span>
                <button class="btn-add" onclick="openAddModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Plan
                </button>
            </div>
        </div>

        <!-- Plans Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2c3e50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                    </svg>
                    Plan Directory
                </div>
                <span class="count-badge"><?php echo $plan_count; ?> Records</span>
            </div>

            <table class="plans-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Plan Name</th>
                        <th>Amount (LKR)</th>
                        <th>Duration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($plans_result && $plans_result->num_rows > 0): ?>
                    <?php while ($row = $plans_result->fetch_assoc()): ?>
                        <tr>
                            <td class="cell-id">#<?php echo $row['type_id']; ?></td>
                            <td>
                                <div class="name-cell">
                                    <div class="plan-icon-wrap">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                        </svg>
                                    </div>
                                    <span class="plan-name"><?php echo htmlspecialchars($row['type_name']); ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="amount-text">Rs. <?php echo number_format($row['amount'], 2); ?></span>
                            </td>
                            <td>
                                <?php if ($row['duration_months'] > 0): ?>
                                    <span class="duration-badge"><?php echo $row['duration_months']; ?> Month<?php echo $row['duration_months'] > 1 ? 's' : ''; ?></span>
                                <?php else: ?>
                                    <span style="color:#bdc3c7;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-edit" onclick="openEditModal(<?php echo $row['type_id']; ?>, '<?php echo addslashes(htmlspecialchars($row['type_name'])); ?>', <?php echo $row['amount']; ?>, <?php echo (int)$row['duration_months']; ?>)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                        Edit
                                    </button>
                                    <button class="btn-delete" onclick="openDeleteModal(<?php echo $row['type_id']; ?>, '<?php echo addslashes(htmlspecialchars($row['type_name'])); ?>')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                            <path d="M10 11v6"/><path d="M14 11v6"/>
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#2c3e50" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" display="block" margin="0 auto">
                                    <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                </svg>
                                <p>No plans found. Click <strong>Add Plan</strong> to create one.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- ── Add Plan Modal ─────────────────────────────────────────── -->
    <div class="modal-overlay" id="addModal" onclick="handleOverlay(event, 'addModal')">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-header-left">
                    <div class="modal-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                        </svg>
                    </div>
                    <div>
                        <div class="modal-title">Add New Plan</div>
                        <div class="modal-subtitle">Fill in the membership plan details</div>
                    </div>
                </div>
                <button class="modal-close" onclick="closeModal('addModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form action="../../tier2_application/process_plan.php" method="POST">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Plan Name <span class="required">*</span></label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                </svg>
                            </span>
                            <input type="text" name="type_name" required placeholder="e.g. Gold Monthly" class="form-input">
                        </div>
                    </div>
                    <div class="form-row-modal">
                        <div class="form-group">
                            <label class="form-label">Amount (LKR) <span class="required">*</span></label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                    </svg>
                                </span>
                                <input type="number" name="amount" step="0.01" min="0" required placeholder="5000.00" class="form-input">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Duration (Months)</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                </span>
                                <input type="number" name="duration_months" min="0" placeholder="1" class="form-input">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Cancel</button>
                    <button type="submit" class="btn-submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Add Plan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ── Edit Plan Modal ────────────────────────────────────────── -->
    <div class="modal-overlay" id="editModal" onclick="handleOverlay(event, 'editModal')">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-header-left">
                    <div class="modal-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="modal-title">Edit Plan</div>
                        <div class="modal-subtitle">Update the membership plan details</div>
                    </div>
                </div>
                <button class="modal-close" onclick="closeModal('editModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form action="../../tier2_application/process_plan.php" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="type_id" id="edit_type_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Plan Name <span class="required">*</span></label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                </svg>
                            </span>
                            <input type="text" name="type_name" id="edit_type_name" required placeholder="e.g. Gold Monthly" class="form-input">
                        </div>
                    </div>
                    <div class="form-row-modal">
                        <div class="form-group">
                            <label class="form-label">Amount (LKR) <span class="required">*</span></label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                    </svg>
                                </span>
                                <input type="number" name="amount" id="edit_amount" step="0.01" min="0" required placeholder="5000.00" class="form-input">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Duration (Months)</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                </span>
                                <input type="number" name="duration_months" id="edit_duration" min="0" placeholder="1" class="form-input">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" class="btn-submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ── Delete Confirm Modal ───────────────────────────────────── -->
    <div class="modal-overlay" id="deleteModal" onclick="handleOverlay(event, 'deleteModal')">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-header-left">
                    <div class="modal-icon danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            <path d="M10 11v6"/><path d="M14 11v6"/>
                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                        </svg>
                    </div>
                    <div>
                        <div class="modal-title">Delete Plan</div>
                        <div class="modal-subtitle">This action cannot be undone</div>
                    </div>
                </div>
                <button class="modal-close" onclick="closeModal('deleteModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form action="../../tier2_application/process_plan.php" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="type_id" id="delete_type_id">
                <div class="delete-confirm-text">
                    Are you sure you want to delete the plan <strong id="delete_plan_name"></strong>?
                    Members currently on this plan may be affected.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                    <button type="submit" class="btn-submit danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                        </svg>
                        Delete Plan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.add('open');
        }

        function openEditModal(id, name, amount, duration) {
            document.getElementById('edit_type_id').value   = id;
            document.getElementById('edit_type_name').value = name;
            document.getElementById('edit_amount').value    = amount;
            document.getElementById('edit_duration').value  = duration;
            document.getElementById('editModal').classList.add('open');
        }

        function openDeleteModal(id, name) {
            document.getElementById('delete_type_id').value     = id;
            document.getElementById('delete_plan_name').textContent = name;
            document.getElementById('deleteModal').classList.add('open');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
        }

        function handleOverlay(e, id) {
            if (e.target === document.getElementById(id)) closeModal(id);
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                ['addModal', 'editModal', 'deleteModal'].forEach(id => closeModal(id));
            }
        });
    </script>

</body>
</html>
