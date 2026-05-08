<?php
require_once '../../tier2_application/admin_auth.php';
require_admin_auth('../login.php');
require_once '../../tier2_application/db_config.php';

$plan_count = 0;
$r = $conn->query("SELECT COUNT(*) AS total FROM membership_types");
if ($r) $plan_count = $r->fetch_assoc()['total'];

$plans_result = $conn->query("SELECT type_id, type_name, amount, duration_months FROM membership_types ORDER BY type_name ASC");

// Load price history grouped by type_id (only if the table exists)
$price_history = [];
$tbl_check = $conn->query("SHOW TABLES LIKE 'membership_type_prices'");
if ($tbl_check && $tbl_check->num_rows > 0) {
    $hr = $conn->query("SELECT type_id, amount, effective_from FROM membership_type_prices ORDER BY type_id, effective_from DESC");
    if ($hr) {
        while ($h = $hr->fetch_assoc()) {
            $price_history[$h['type_id']][] = $h;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Plans - Fitness Hub</title>
    <link rel="stylesheet" href="dashboard_style.css">
    <link rel="stylesheet" href="plans_style.css">
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
                                    <?php if (!empty($price_history[$row['type_id']])): ?>
                                    <button class="btn-history" onclick="openHistoryModal(<?php echo $row['type_id']; ?>)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        History
                                    </button>
                                    <?php endif; ?>
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

    <!-- ── Price History Modals (one per plan) ───────────────────── -->
    <?php if (!empty($price_history)): ?>
        <?php foreach ($price_history as $tid => $rows): ?>
        <div class="modal-overlay" id="historyModal_<?php echo $tid; ?>" onclick="handleOverlay(event,'historyModal_<?php echo $tid; ?>')">
            <div class="modal" style="max-width:520px;">
                <div class="modal-header">
                    <div class="modal-header-left">
                        <div class="modal-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div>
                            <div class="modal-title">Price History</div>
                            <div class="modal-subtitle">All recorded price changes for this plan</div>
                        </div>
                    </div>
                    <button class="modal-close" onclick="closeModal('historyModal_<?php echo $tid; ?>')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
                <div class="modal-body modal-body-table">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Amount (LKR)</th>
                                <th>Effective From</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rows as $i => $h): ?>
                            <tr>
                                <td><?php echo count($rows) - $i; ?></td>
                                <td class="td-amount">
                                    Rs. <?php echo number_format($h['amount'], 2); ?>
                                    <?php if ($i === 0): ?><span class="current-tag">Current</span><?php endif; ?>
                                </td>
                                <td class="td-date"><?php echo date('M j, Y  g:i A', strtotime($h['effective_from'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

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
        function openHistoryModal(id) {
            document.getElementById('historyModal_' + id).classList.add('open');
        }

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
                document.querySelectorAll('.modal-overlay.open').forEach(el => el.classList.remove('open'));
            }
        });
    </script>
    <script src="includes/alerts.js"></script>
    <script>
        if (window.location.search) {
            history.replaceState(null, '', window.location.pathname);
        }
    </script>

</body>
</html>
