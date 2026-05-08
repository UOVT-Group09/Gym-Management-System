<?php
require_once '../../tier2_application/admin_auth.php';
require_admin_auth('../login.php');
require_once '../../tier2_application/get_classes.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes - Fitness Hub</title>
    <link rel="stylesheet" href="dashboard_style.css">
    <link rel="stylesheet" href="classes_style.css">
</head>
<body>

    <?php $active_page = 'classes'; include 'includes/sidebar.php'; ?>

    <div class="main-content">

        <header class="topbar">
            <div class="topbar-title">
                <h1>Classes</h1>
                <p>Manage class schedules, capacity, and enrollments</p>
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

        <div class="page-header">
            <div>
                <h1>All Classes</h1>
                <p>Set up class schedules and handle waitlists</p>
            </div>
            <div class="header-right">
                <span class="count-badge"><?php echo $class_count; ?> Classes</span>
                <button class="btn-add" onclick="openAddModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Class
                </button>
            </div>
        </div>

        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2c3e50" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Class Schedule
                </div>
                <span class="count-badge"><?php echo $class_count; ?> Records</span>
            </div>

            <table class="classes-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Class</th>
                        <th>Trainer</th>
                        <th>Schedule</th>
                        <th>Capacity</th>
                        <th>Waitlist</th>
                        <th>Enroll</th>
                        <th>Cancel</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($classes_result && $classes_result->num_rows > 0): ?>
                    <?php while ($row = $classes_result->fetch_assoc()): ?>
                        <?php
                            $class_id = (int)$row['class_id'];
                            $capacity = (int)$row['capacity'];
                            $enrolled_count = (int)($row['enrolled_count'] ?? 0);
                            $waitlist_count = (int)($row['waitlist_count'] ?? 0);
                            $scheduled_display = $row['scheduled_at'] ? date('M j, Y g:i A', strtotime($row['scheduled_at'])) : 'N/A';
                            $scheduled_local = $row['scheduled_at'] ? date('Y-m-d\TH:i', strtotime($row['scheduled_at'])) : '';
                            $duration_text = $row['duration_minutes'] ? ((int)$row['duration_minutes'] . ' min') : '';
                            $enrollments = $enrollments_by_class[$class_id] ?? [];
                            $has_enrollments = !empty($enrollments);
                            $has_active_members = $active_members_result && $active_members_result->num_rows > 0;
                        ?>
                        <tr>
                            <td class="cell-id">#<?php echo $class_id; ?></td>
                            <td class="cell-name"><?php echo htmlspecialchars($row['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['trainer_name'] ?? 'Unassigned'); ?></td>
                            <td>
                                <div class="schedule-text"><?php echo $scheduled_display; ?></div>
                                <?php if ($duration_text !== ''): ?>
                                    <div class="schedule-subtext"><?php echo htmlspecialchars($duration_text); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="count-pill enrolled"><?php echo $enrolled_count; ?>/<?php echo $capacity; ?></span>
                            </td>
                            <td>
                                <span class="count-pill waitlist"><?php echo $waitlist_count; ?></span>
                            </td>
                            <td>
                                <form class="inline-form" action="../../tier2_application/process_class_enrollment.php" method="POST">
                                    <input type="hidden" name="action" value="enroll">
                                    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                                    <select name="member_id" class="inline-select" <?php echo $has_active_members ? 'required' : 'disabled'; ?>>
                                        <option value="">Select member</option>
                                        <?php if ($has_active_members): ?>
                                            <?php $active_members_result->data_seek(0); ?>
                                            <?php while ($member = $active_members_result->fetch_assoc()): ?>
                                                <option value="<?php echo $member['member_id']; ?>"><?php echo htmlspecialchars($member['full_name']); ?></option>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <option value="" disabled>No active members</option>
                                        <?php endif; ?>
                                    </select>
                                    <button type="submit" class="btn-inline" <?php echo $has_active_members ? '' : 'disabled'; ?>>Enroll</button>
                                </form>
                            </td>
                            <td>
                                <form class="inline-form" action="../../tier2_application/process_class_enrollment.php" method="POST">
                                    <input type="hidden" name="action" value="cancel">
                                    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                                    <select name="member_id" class="inline-select" <?php echo $has_enrollments ? 'required' : 'disabled'; ?>>
                                        <option value="">Select member</option>
                                        <?php if ($has_enrollments): ?>
                                            <?php foreach ($enrollments as $enrollment): ?>
                                                <option value="<?php echo $enrollment['member_id']; ?>">
                                                    <?php echo htmlspecialchars($enrollment['full_name']); ?> (<?php echo htmlspecialchars($enrollment['status']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>No enrollments</option>
                                        <?php endif; ?>
                                    </select>
                                    <button type="submit" class="btn-inline cancel" <?php echo $has_enrollments ? '' : 'disabled'; ?>>Cancel</button>
                                </form>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-edit"
                                            data-class-id="<?php echo $class_id; ?>"
                                            data-class-name="<?php echo htmlspecialchars($row['class_name'], ENT_QUOTES); ?>"
                                            data-capacity="<?php echo $capacity; ?>"
                                            data-trainer-id="<?php echo htmlspecialchars($row['trainer_id'] ?? '', ENT_QUOTES); ?>"
                                            data-scheduled="<?php echo htmlspecialchars($scheduled_local, ENT_QUOTES); ?>"
                                            data-duration="<?php echo htmlspecialchars($row['duration_minutes'] ?? '', ENT_QUOTES); ?>"
                                            onclick="openEditModal(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                        Edit
                                    </button>
                                    <form action="../../tier2_application/process_class.php" method="POST" onsubmit="return confirm('Delete this class?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                                        <button type="submit" class="btn-delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                <path d="M10 11v6"/><path d="M14 11v6"/>
                                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#2c3e50" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" display="block" margin="0 auto">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                <p>No classes found. Click <strong>Add Class</strong> to get started.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Add Class Modal -->
    <div class="modal-overlay" id="classModal" onclick="handleOverlayClick(event, 'classModal')">
        <div class="modal">

            <div class="modal-header">
                <div class="modal-header-left">
                    <div class="modal-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <div>
                        <div class="modal-title">Add New Class</div>
                        <div class="modal-subtitle">Create a class schedule and capacity</div>
                    </div>
                </div>
                <button class="modal-close" onclick="closeAddModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <form action="../../tier2_application/process_class.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">

                    <div class="form-group">
                        <label class="form-label">Class Name <span class="required">*</span></label>
                        <div class="input-wrap">
                            <input type="text" name="class_name" required placeholder="Enter class name" class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Capacity <span class="required">*</span></label>
                        <div class="input-wrap">
                            <input type="number" name="capacity" min="1" required placeholder="e.g. 20" class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Trainer</label>
                        <div class="input-wrap">
                            <select name="trainer_id" class="form-input">
                                <option value="">Unassigned</option>
                                <?php if ($trainers_result && $trainers_result->num_rows > 0): ?>
                                    <?php while ($trainer = $trainers_result->fetch_assoc()): ?>
                                        <option value="<?php echo $trainer['trainer_id']; ?>"><?php echo htmlspecialchars($trainer['name']); ?></option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <option value="" disabled>No trainers found</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Schedule <span class="required">*</span></label>
                        <div class="input-wrap">
                            <input type="datetime-local" name="scheduled_at" required class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Duration (minutes)</label>
                        <div class="input-wrap">
                            <input type="number" name="duration_minutes" min="1" placeholder="Optional" class="form-input">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="btn-submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Add Class
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Edit Class Modal -->
    <div class="modal-overlay" id="editModal" onclick="handleOverlayClick(event, 'editModal')">
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
                        <div class="modal-title">Edit Class</div>
                        <div class="modal-subtitle">Update class details</div>
                    </div>
                </div>
                <button class="modal-close" onclick="closeEditModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <form action="../../tier2_application/process_class.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="class_id" id="edit_class_id">

                    <div class="form-group">
                        <label class="form-label">Class Name <span class="required">*</span></label>
                        <div class="input-wrap">
                            <input type="text" name="class_name" id="edit_class_name" required class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Capacity <span class="required">*</span></label>
                        <div class="input-wrap">
                            <input type="number" name="capacity" id="edit_capacity" min="1" required class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Trainer</label>
                        <div class="input-wrap">
                            <select name="trainer_id" id="edit_trainer_id" class="form-input">
                                <option value="">Unassigned</option>
                                <?php if ($trainers_result && $trainers_result->num_rows > 0): ?>
                                    <?php $trainers_result->data_seek(0); ?>
                                    <?php while ($trainer = $trainers_result->fetch_assoc()): ?>
                                        <option value="<?php echo $trainer['trainer_id']; ?>"><?php echo htmlspecialchars($trainer['name']); ?></option>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <option value="" disabled>No trainers found</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Schedule <span class="required">*</span></label>
                        <div class="input-wrap">
                            <input type="datetime-local" name="scheduled_at" id="edit_scheduled_at" required class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Duration (minutes)</label>
                        <div class="input-wrap">
                            <input type="number" name="duration_minutes" id="edit_duration" min="1" class="form-input">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
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

    <script>
        function openAddModal() {
            document.getElementById('classModal').classList.add('open');
        }
        function closeAddModal() {
            document.getElementById('classModal').classList.remove('open');
        }
        function openEditModal(button) {
            document.getElementById('edit_class_id').value = button.getAttribute('data-class-id');
            document.getElementById('edit_class_name').value = button.getAttribute('data-class-name');
            document.getElementById('edit_capacity').value = button.getAttribute('data-capacity');
            document.getElementById('edit_trainer_id').value = button.getAttribute('data-trainer-id');
            document.getElementById('edit_scheduled_at').value = button.getAttribute('data-scheduled');
            document.getElementById('edit_duration').value = button.getAttribute('data-duration');
            document.getElementById('editModal').classList.add('open');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('open');
        }
        function handleOverlayClick(e, modalId) {
            if (e.target === document.getElementById(modalId)) {
                document.getElementById(modalId).classList.remove('open');
            }
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
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
