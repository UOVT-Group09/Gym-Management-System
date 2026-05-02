<?php
require_once '../../tier2_application/get_members.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gym Management System - View Members</title>

    <!-- Correct CSS Path -->
    <link rel="stylesheet" href="../style.css">
</head>

<body>

<div class="container" style="width: 90%; max-width: 1000px;">
    
    <h1>Registered Members List</h1>

    <table border="1" class="member-table">

        <thead>
            <tr>
                <th>ID</th>
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

            while($row = $result->fetch_assoc()) {
        ?>

            <tr>
                <td><?php echo $row["member_id"]; ?></td>
                <td><?php echo $row["full_name"]; ?></td>
                <td><?php echo $row["email"]; ?></td>
                <td><?php echo $row["phone"]; ?></td>
                <td><?php echo $row["gender"]; ?></td>
                <td><?php echo $row["type_name"]; ?></td>
                <td><?php echo $row["join_date"]; ?></td>
            </tr>

        <?php
            }

        } else {
            echo "<tr><td colspan='7'>No members found</td></tr>";
        }
        ?>

        </tbody>

    </table>

    <div class="home-menu" style="margin-top: 20px;">
        <a href="index.php" class="btn-reset">Back to Dashboard</a>
    </div>

</div>

</body>
</html>