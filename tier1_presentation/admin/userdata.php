<?php
require_once '../../tier2_application/get_members.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Members - Fitness Hub</title>
    <link rel="stylesheet" href="css/dashboard_style.css">
    <link rel="stylesheet" href="css/userdata_style.css">
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

        <div class="table-card">
            <table class="member-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Membership Plan</th>
                        <th>Join Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $plan = htmlspecialchars($row["type_name"]);
                        $planClass = 'plan-default';
                        if (stripos($plan, 'gold') !== false || stripos($plan, 'premium') !== false) {
                            $planClass = 'plan-gold';
                        } elseif (stripos($plan, 'silver') !== false || stripos($plan, 'standard') !== false) {
                            $planClass = 'plan-silver';
                        } elseif (stripos($plan, 'basic') !== false || stripos($plan, 'bronze') !== false) {
                            $planClass = 'plan-basic';
                        }
                ?>
                    <tr>
                        <td class="cell-id"><?php echo htmlspecialchars($row["member_id"]); ?></td>
                        <td class="cell-name"><?php echo htmlspecialchars($row["full_name"]); ?></td>
                        <td class="cell-email"><?php echo htmlspecialchars($row["email"]); ?></td>
                        <td><?php echo htmlspecialchars($row["phone"]); ?></td>
                        <td>
                            <span class="gender-tag gender-<?php echo strtolower(htmlspecialchars($row['gender'])); ?>">
                                <?php echo htmlspecialchars($row["gender"]); ?>
                            </span>
                        </td>
                        <td><span class="plan-badge <?php echo $planClass; ?>"><?php echo $plan; ?></span></td>
                        <td class="cell-date"><?php echo htmlspecialchars($row["join_date"]); ?></td>
                    </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='7' class='no-data'>No members found</td></tr>";
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
