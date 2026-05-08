<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gym Login</title>
    <link rel="stylesheet" href="loginstyle.css">
</head>

<body>

<div class="brand-title">Fitness Hub</div>

<div class="login-container">

    <h2>Gym Management Login</h2>

    <?php if (isset($_GET['error'])): ?>
        <p style="color:#c0392b; margin-bottom: 12px; text-align: center; font-weight: 600;">
            <?php echo $_GET['error'] === 'admin_only' ? 'Please log in as admin to continue.' : 'Invalid username or password.'; ?>
        </p>
    <?php endif; ?>

    <form action="../tier2_application/login_process.php" method="POST">

        <div class="input-box">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="input-box">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit">Login</button>

    </form>

</div>

</body>
</html>
