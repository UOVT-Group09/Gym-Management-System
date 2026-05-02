<?php
require_once '../../tier2_application/get_members.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gym Management System - View Members</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="admin_style.css">
</head>

<body class="members-page">

<div class="members-wrapper">

    <div class="members-header">
        <div class="members-title-block">
            <span class="members-icon">&#127939;</span>
            <div>
                <h1 class="members-title">Registered Members</h1>
                <p class="members-subtitle">All active gym members at a glance</p>
            </div>
        </div>
        <?php
            $total = isset($result->num_rows) ? $result->num_rows : 0;
        ?>
        <div class="members-count-badge"><?php echo $total; ?> Members</div>
    </div>

    <div class="members-table-card">
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

    <div class="members-footer">
        <a href="index.php" class="btn-back">&#8592; Back to Dashboard</a>
    </div>

</div>

</body>
</html>
