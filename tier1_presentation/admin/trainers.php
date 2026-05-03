<?php
require_once '../../tier2_application/get_trainers.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainers - Fitness Hub</title>
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

        .trainer-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .trainer-table thead tr { background: #2c3e50; color: #ffcc00; }
        .trainer-table thead th {
            padding: 13px 22px;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .trainer-table tbody tr {
            border-bottom: 1px solid #f0f2f5;
            transition: background 0.15s;
        }
        .trainer-table tbody tr:hover { background: #fffbea; }

        .trainer-table td {
            padding: 15px 22px;
            color: #2c3e50;
            vertical-align: middle;
        }

        .cell-id   { color: #95a5a6; font-size: 0.82rem; font-weight: 600; width: 60px; }
        .cell-name { font-weight: 700; }

        .avatar {
            width: 38px;
            height: 38px;
            background: #eaf4fb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2980b9;
            font-weight: 800;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .name-cell { display: flex; align-items: center; gap: 12px; }

        .spec-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
            background: #eaf4fb;
            color: #2980b9;
            border: 1px solid #aed6f1;
        }

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
    </style>
</head>
<body>

    <?php $active_page = 'trainers'; include 'includes/sidebar.php'; ?>

    <div class="main-content">

        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-title">
                <h1>Trainers</h1>
                <p>Manage gym trainers and their specializations</p>
            </div>
            <div class="topbar-meta">
                <div class="topbar-date"><?php echo date('l, F j, Y'); ?></div>
                <div class="topbar-admin">Welcome, <strong>Admin</strong></div>
            </div>
        </header>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Trainer added successfully!
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
                <h1>All Trainers</h1>
                <p>Active trainers registered in the system</p>
            </div>
            <div class="header-right">
                <span class="count-badge"><?php echo $trainer_count; ?> Trainers</span>
                <button class="btn-add" onclick="openModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Trainer
                </button>
            </div>
        </div>

        <!-- Trainer Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2c3e50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 4v16M18 4v16"/><path d="M4 8h4M16 8h4M4 16h4M16 16h4"/><line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                    Trainer Directory
                </div>
                <span class="count-badge"><?php echo $trainer_count; ?> Records</span>
            </div>

            <table class="trainer-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($trainers_result && $trainers_result->num_rows > 0): ?>
                    <?php while ($row = $trainers_result->fetch_assoc()): ?>
                        <tr>
                            <td class="cell-id">#<?php echo htmlspecialchars($row['trainer_id']); ?></td>
                            <td class="cell-name">
                                <div class="name-cell">
                                    <div class="avatar"><?php echo strtoupper(substr($row['name'], 0, 1)); ?></div>
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($row['specialization'])): ?>
                                    <span class="spec-badge"><?php echo htmlspecialchars($row['specialization']); ?></span>
                                <?php else: ?>
                                    <span style="color:#bdc3c7;">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="color:#7f8c8d;"><?php echo htmlspecialchars($row['phone'] ?: '—'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#2c3e50" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" display="block" margin="0 auto">
                                    <path d="M6 4v16M18 4v16"/><path d="M4 8h4M16 8h4M4 16h4M16 16h4"/><line x1="8" y1="12" x2="16" y2="12"/>
                                </svg>
                                <p>No trainers found. Click <strong>Add Trainer</strong> to get started.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- ── Add Trainer Modal ──────────────────────────────────── -->
    <div class="modal-overlay" id="trainerModal" onclick="handleOverlayClick(event)">
        <div class="modal">

            <div class="modal-header">
                <div class="modal-header-left">
                    <div class="modal-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 4v16M18 4v16"/><path d="M4 8h4M16 8h4M4 16h4M16 16h4"/><line x1="8" y1="12" x2="16" y2="12"/>
                        </svg>
                    </div>
                    <div>
                        <div class="modal-title">Add New Trainer</div>
                        <div class="modal-subtitle">Fill in the trainer details below</div>
                    </div>
                </div>
                <button class="modal-close" onclick="closeModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <form action="../../tier2_application/process_trainer.php" method="POST">
                <div class="modal-body">

                    <div class="form-group">
                        <label class="form-label">Full Name <span class="required">*</span></label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                            </span>
                            <input type="text" name="name" required placeholder="Enter trainer name" class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Specialization</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 4v16M18 4v16"/><path d="M4 8h4M16 8h4M4 16h4M16 16h4"/><line x1="8" y1="12" x2="16" y2="12"/>
                                </svg>
                            </span>
                            <input type="text" name="specialization" placeholder="e.g. Cardio, Weight Training" class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.5 2 2 0 0 1 3.6 1.36h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6 6l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7a2 2 0 0 1 1.72 2.02z"/>
                                </svg>
                            </span>
                            <input type="text" name="phone" placeholder="07XXXXXXXX" class="form-input">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Add Trainer
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        function openModal()  { document.getElementById('trainerModal').classList.add('open'); }
        function closeModal() { document.getElementById('trainerModal').classList.remove('open'); }
        function handleOverlayClick(e) { if (e.target === document.getElementById('trainerModal')) closeModal(); }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
    </script>

</body>
</html>
