<?php
require_once '../../tier2_application/admin_auth.php';
require_admin_auth('../login.php');
require_once '../../tier2_application/db_config.php';
$plans = $conn->query("SELECT type_id, type_name, amount FROM membership_types ORDER BY type_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Member - Fitness Hub</title>
    <link rel="stylesheet" href="dashboard_style.css">
    <link rel="stylesheet" href="register_style.css">
</head>
<body>

    <?php $active_page = 'add_member'; include 'includes/sidebar.php'; ?>

    <div class="main-content">

        <header class="topbar">
            <div class="topbar-title">
                <h1>Add New Member</h1>
                <p>Fill in the details to register a new gym member</p>
            </div>
            <div class="topbar-meta">
                <div class="topbar-date"><?php echo date('l, F j, Y'); ?></div>
                <div class="topbar-admin">Welcome, <strong>Admin</strong></div>
            </div>
        </header>

        <div class="form-center">
            <div class="form-card">

                <div class="form-panel-info">

                    <div class="info-brand">
                        <div class="info-brand-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#2c3e50" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 4v16M18 4v16"/><path d="M4 8h4M16 8h4M4 16h4M16 16h4"/><line x1="8" y1="12" x2="16" y2="12"/>
                            </svg>
                        </div>
                        <div class="info-brand-name">Fitness Hub</div>
                    </div>

                    <div>
                        <div class="info-heading">Register a <span>New</span><br>Gym Member</div>
                        </div>

                    <div class="info-features">
                        <div class="info-feature">
                            <div class="info-feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                            Track member profiles &amp; history
                        </div>
                        <div class="info-feature">
                            <div class="info-feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                                </svg>
                            </div>
                            Manage membership plans &amp; payments
                        </div>
                        <div class="info-feature">
                            <div class="info-feature-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
                                </svg>
                            </div>
                            Generate monthly reports
                        </div>
                    </div>

                </div>

                <div class="form-panel-form">

                    <div class="form-panel-title">
                        <h2>Member Registration</h2>
                        <p>Fields marked <span class="required">*</span> are required</p>
                    </div>

                    <form action="../../tier2_application/process_registration.php" method="POST" class="reg-form">

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Full Name <span class="required">*</span></label>
                                <div class="input-wrap">
                                    <span class="input-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                        </svg>
                                    </span>
                                    <input type="text" name="full_name" required placeholder="Enter full name" class="form-input">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email Address <span class="required">*</span></label>
                                <div class="input-wrap">
                                    <span class="input-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                                        </svg>
                                    </span>
                                    <input type="email" name="email" required placeholder="example@mail.com" class="form-input">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <div class="input-wrap">
                                    <span class="input-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.5 2 2 0 0 1 3.6 1.36h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6 6l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7a2 2 0 0 1 1.72 2.02z"/>
                                        </svg>
                                    </span>
                                    <input type="text" name="phone" placeholder="07XXXXXXXX" class="form-input">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Gender <span class="required">*</span></label>
                                <div class="input-wrap">
                                    <span class="input-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="11" r="4"/><path d="M12 15v6"/><path d="M9 18h6"/><path d="M17.5 4.5a4.5 4.5 0 0 1-6.36 6.36"/>
                                        </svg>
                                    </span>
                                    <select name="gender" required class="form-input form-select">
                                        <option value="" disabled selected>Select gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Membership Plan <span class="required">*</span></label>
                                <div class="input-wrap">
                                    <span class="input-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                                        </svg>
                                    </span>
                                    <select name="type_id" id="plan_select" required class="form-input form-select">
                                        <option value="" disabled selected data-amount="">Select a plan</option>
                                        <?php if ($plans && $plans->num_rows > 0): ?>
                                            <?php while ($plan = $plans->fetch_assoc()): ?>
                                                <option value="<?php echo $plan['type_id']; ?>" data-amount="<?php echo $plan['amount']; ?>">
                                                    <?php echo htmlspecialchars($plan['type_name']); ?> &mdash; Rs. <?php echo number_format($plan['amount'], 2); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <option value="" disabled data-amount="">No plans available</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Payment (LKR) <span class="required">*</span></label>
                                <div class="input-wrap">
                                    <span class="input-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                        </svg>
                                    </span>
                                    <input type="number" name="amount" id="payment_amount" step="0.01" required placeholder="0.00" class="form-input" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-submit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                Register Member
                            </button>
                            <button type="reset" class="btn btn-clear">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.5"/>
                                </svg>
                                Clear Form
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>

    </div>

    <script src="includes/alerts.js"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const planSelect = document.getElementById("plan_select");
            const paymentAmount = document.getElementById("payment_amount");

            // Dropdown එකේ අගය වෙනස් වෙද්දී මේක ක්‍රියාත්මක වෙනවා
            planSelect.addEventListener("change", function() {
                // තෝරපු plan එක ගන්නවා
                const selectedOption = planSelect.options[planSelect.selectedIndex];
                
                // ඒ plan එකට අදාළ data-amount (මුදල) ගන්නවා
                const amount = selectedOption.getAttribute("data-amount");
                
                // අරගත්ත මුදල text box එකට දානවා (දශමස්ථාන 2ක් එක්ක)
                if (amount) {
                    paymentAmount.value = parseFloat(amount).toFixed(2);
                } else {
                    paymentAmount.value = "";
                }
            });
        });
    </script>
</body>
</html>
