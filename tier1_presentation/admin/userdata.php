<?php
require_once '../../tier2_application/get_members.php';
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

    <!-- Main Content -->
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
        <th>ACTION</th>
    </tr>
</thead>
                <tbody>
    <?php 
    $i = 1;
    while($row = mysqli_fetch_assoc($result)): 
    ?>
    <tr>
        <td><?php echo $i++; ?></td>
        <td><?php echo $row['full_name']; ?></td>
        <td><?php echo $row['email']; ?></td>
        <td><?php echo $row['phone']; ?></td>
        <td><?php echo $row['gender']; ?></td>
        <td>
            <span class="badge bg-light text-success border border-success">
                <?php echo $row['type_name']; ?>
            </span>
        </td>
        <td><?php echo $row['join_date']; ?></td>

        <td>
            <span class="badge <?php 
                echo ($row['status'] == 'Active' ? 'bg-success' : 
                     ($row['status'] == 'Frozen' ? 'bg-warning text-dark' : 'bg-danger')); 
            ?>">
                <?php echo $row['status']; ?>
            </span>
        </td>

        <td><?php echo $row['membership_end'] ? $row['membership_end'] : 'N/A'; ?></td>

        <td>
            <?php if($row['status'] == 'Active'): ?>
                <form method="POST" action="../../tier2_application/get_members.php" style="display:inline;">
                    <input type="hidden" name="member_id" value="<?php echo $row['member_id']; ?>">
                    <input type="hidden" name="days" value="30"> 
                    <input type="hidden" name="action" value="freeze">
                    <button type="submit" class="btn btn-outline-info btn-sm">Freeze (30d)</button>
                </form>
            <?php else: ?>
                <button class="btn btn-secondary btn-sm" disabled>No Action</button>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
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
