<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Registration - Gym System</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <div class="overlay"></div>
    <div class="container">
        <h2>Gym Member Registration</h2>
        <p>Please fill in the details to register a new member.</p>

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

        <form action="../tier2_application/process_registration.php" method="POST">
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="full_name" required placeholder="Enter full name">
            </div>
            
            <div class="form-group">
                <label>Email Address:</label>
                <input type="email" name="email" required placeholder="example@mail.com">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Phone Number:</label>
                    <input type="text" name="phone" placeholder="07XXXXXXXX">
                </div>

                <div class="form-group">
                    <label>Member Type:</label>
                    <select name="type_id" required>
                        <option value="">Select Member Type</option>
                        <option value="1">Regular</option>
                        <option value="2">Premium</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Gender:</label>
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Initial Payment (LKR):</label>
                    <input type="number" name="amount" step="0.01" required placeholder="5000.00">
                </div>
            </div>
            
            <button type="submit" class="btn-submit">Register Member</button>
            <button type="reset" class="btn-reset">Clear</button>
        </form>
    </div>
    <script src="admin/includes/alerts.js"></script>
</body>
</html>