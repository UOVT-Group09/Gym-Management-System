<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Vertex Fitness Hub</title>
    <link rel="stylesheet" href="dashstyle.css">
</head>

<body>


<div class="sidebar">
    <h2>🏋️ Vertex Fitness</h2>

    <a href="#">Dashboard</a>
    <a href="#">Members</a>
    <a href="#">Trainers</a>
    <a href="#">Payments</a>
    <a href="#">Goals</a>
    <a href="logout.php" class="logout">Logout</a>
</div>


<div class="main">

   
    <div class="topbar">
        <h1>Admin Dashboard</h1>
        <div class="user">👤 <?php echo $_SESSION['user']; ?></div>
    </div>

    
    <div class="cards">

        <div class="card">
            <h3>👥 Members</h3>
            <p>120</p>
        </div>

        <div class="card">
            <h3>🏋️ Trainers</h3>
            <p>10</p>
        </div>

        <div class="card">
            <h3>💳 Payments</h3>
            <p>Rs. 50,000</p>
        </div>

        <div class="card">
            <h3>🎯 Active Goals</h3>
            <p>35</p>
        </div>

    </div>

</div>

</body>
</html>
