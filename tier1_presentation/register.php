<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Registration - Gym System</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <div class="container">
        <h2>Gym Member Registration</h2>
        <p>Please fill in the details to register a new member.</p>
        
        
<form action="../tier2_application/process_registration.php" method="POST">
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" name="full_name" required placeholder="Enter full name">
            </div>
            
            <div class="form-group">
                <label>Email Address:</label>
                <input type="email" name="email" required placeholder="example@mail.com">
            </div>
            
            <div class="form-group">
                <label>Phone Number:</label>
                <input type="text" name="phone" placeholder="07XXXXXXXX">
            </div>

            <div class="form-group">
                <label>Initial Payment (LKR):</label>
                <input type="number" name="amount" step="0.01" required placeholder="5000.00">
            </div>
            
            <button type="submit" class="btn-submit">Register Member</button>
            <button type="reset" class="btn-reset">Clear</button>
        </form>
    </div>
</body>
</html>