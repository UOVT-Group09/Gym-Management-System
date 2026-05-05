<?php
require_once '../../tier2_application/get_trainers.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainers - Fitness Hub</title>
    <link rel="stylesheet" href="css/dashboard_style.css">
    <link rel="stylesheet" href="css/trainers_style.css">
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
                        <th>Actions</th>
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
                            <td>
                                <div class="action-btns">
                                    <button class="btn-edit" onclick="openEditModal(<?php echo $row['trainer_id']; ?>, '<?php echo addslashes(htmlspecialchars($row['name'])); ?>', '<?php echo addslashes(htmlspecialchars($row['specialization'])); ?>', '<?php echo addslashes(htmlspecialchars($row['phone'])); ?>')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                        Edit
                                    </button>
                                    <button class="btn-delete" onclick="openDeleteModal(<?php echo $row['trainer_id']; ?>, '<?php echo addslashes(htmlspecialchars($row['name'])); ?>')">
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
    <div class="modal-overlay" id="trainerModal" onclick="handleOverlay(event,'trainerModal')">
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
                <button class="modal-close" onclick="closeModal('trainerModal')">
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
                    <button type="button" class="btn-cancel" onclick="closeModal('trainerModal')">Cancel</button>
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

    <!-- ── Edit Trainer Modal ────────────────────────────────────── -->
    <div class="modal-overlay" id="editModal" onclick="handleOverlay(event,'editModal')">
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
                        <div class="modal-title">Edit Trainer</div>
                        <div class="modal-subtitle">Update trainer details below</div>
                    </div>
                </div>
                <button class="modal-close" onclick="closeModal('editModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form action="../../tier2_application/process_trainer.php" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="trainer_id" id="edit_trainer_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Full Name <span class="required">*</span></label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                            </span>
                            <input type="text" name="name" id="edit_name" required placeholder="Enter trainer name" class="form-input">
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
                            <input type="text" name="specialization" id="edit_specialization" placeholder="e.g. Cardio, Weight Training" class="form-input">
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
                            <input type="text" name="phone" id="edit_phone" placeholder="07XXXXXXXX" class="form-input">
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
    <div class="modal-overlay" id="deleteModal" onclick="handleOverlay(event,'deleteModal')">
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
                        <div class="modal-title">Delete Trainer</div>
                        <div class="modal-subtitle">This action cannot be undone</div>
                    </div>
                </div>
                <button class="modal-close" onclick="closeModal('deleteModal')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form action="../../tier2_application/process_trainer.php" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="trainer_id" id="delete_trainer_id">
                <div class="delete-confirm-text">
                    Are you sure you want to delete trainer <strong id="delete_trainer_name"></strong>? This will permanently remove them from the system.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                    <button type="submit" class="btn-submit danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                        </svg>
                        Delete Trainer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal()  { document.getElementById('trainerModal').classList.add('open'); }

        function openEditModal(id, name, spec, phone) {
            document.getElementById('edit_trainer_id').value    = id;
            document.getElementById('edit_name').value          = name;
            document.getElementById('edit_specialization').value = spec;
            document.getElementById('edit_phone').value         = phone;
            document.getElementById('editModal').classList.add('open');
        }

        function openDeleteModal(id, name) {
            document.getElementById('delete_trainer_id').value       = id;
            document.getElementById('delete_trainer_name').textContent = name;
            document.getElementById('deleteModal').classList.add('open');
        }

        function closeModal(id) { document.getElementById(id).classList.remove('open'); }
        function handleOverlay(e, id) { if (e.target === document.getElementById(id)) closeModal(id); }
        function handleOverlayClick(e) { if (e.target === document.getElementById('trainerModal')) closeModal('trainerModal'); }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') ['trainerModal','editModal','deleteModal'].forEach(id => closeModal(id));
        });
    </script>
    <script src="includes/alerts.js"></script>

</body>
</html>
