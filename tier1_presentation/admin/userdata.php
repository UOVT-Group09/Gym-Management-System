<?php
require_once '../../tier2_application/get_members.php';
require_once '../../tier2_application/get_trainers.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Members - Fitness Hub</title>
    <link rel="stylesheet" href="dashboard_style.css">
    <link rel="stylesheet" href="userdata_style.css">
</head>
<body>

    <?php $active_page = 'members'; include 'includes/sidebar.php'; ?>

    <div class="main-content">

        <div class="page-header">
            <div>
                <h1>Registered Members</h1>
                <p>All active gym members at a glance</p>
            </div>
            <?php $total = $result->num_rows; ?>
            <div class="members-count-badge"><?php echo $total; ?> Members</div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <div class="table-card">
            <table class="member-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>FULL NAME</th>
                        <th>EMAIL</th>
                        <th>PHONE</th>
                        <th>GENDER</th>
                        <th>MEMBERSHIP PLAN</th>
                        <th>JOIN DATE</th>
                        <th>STATUS</th>
                        <th>EXPIRY DATE</th>
                        <th>ASSIGN TRAINER</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $member_id = (int)$row['member_id'];
                ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td>
                            <span class="badge bg-light text-success border border-success">
                                <?php echo htmlspecialchars($row['type_name']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($row['join_date']); ?></td>
                        <td>
                            <?php
                                $statusClass = match($row['status']) {
                                    'Active'  => 'status-active',
                                    'Frozen'  => 'status-frozen',
                                    default   => 'status-expired',
                                };
                            ?>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $row['membership_end'] ? htmlspecialchars($row['membership_end']) : 'N/A'; ?></td>
                        <td>
                            <form action="../../tier2_application/process_assignment.php" method="POST" class="assign-form">
                                <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
                                <select name="trainer_id" class="trainer-select" required>
                                    <option value="">Select Trainer</option>
                                    <?php
                                    $trainers_result->data_seek(0);
                                    while ($t = $trainers_result->fetch_assoc()):
                                    ?>
                                        <option value="<?php echo $t['trainer_id']; ?>" <?php echo ($t['is_overloaded'] ? 'disabled' : ''); ?>>
                                            <?php echo htmlspecialchars($t['name']); ?> (<?php echo $t['current_load']; ?>/10)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <button type="submit" class="btn-assign">Assign</button>
                            </form>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'Active'): ?>
                                <form method="POST" action="../../tier2_application/get_members.php" style="display:inline;">
                                    <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
                                    <input type="hidden" name="days" value="30">
                                    <input type="hidden" name="action" value="freeze">
                                    <button type="submit" class="btn-freeze">Freeze (30d)</button>
                                </form>
                            <?php elseif ($row['status'] == 'Frozen'): ?>
                                <form method="POST" action="../../tier2_application/get_members.php" style="display:inline;">
                                    <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
                                    <input type="hidden" name="action" value="unfreeze">
                                    <button type="submit" class="btn-unfreeze">Unfreeze</button>
                                </form>
                            <?php else: ?>
                                <button class="btn-no-action" disabled>No Action</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='11' class='no-data'>No members found</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <a href="admin_dashboard.php" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Dashboard
        </a>

    </div>

    <script src="includes/alerts.js"></script>
</body>
</html>
