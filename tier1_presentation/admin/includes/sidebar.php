<?php
// $active_page must be set before including this file.
// Values: 'dashboard' | 'add_member' | 'members' | 'payments' | 'trainers' | 'plans' | 'classes'
if (!isset($active_page)) $active_page = '';

function nav_class(string $page, string $active): string {
    return $page === $active ? 'nav-link active' : 'nav-link';
}
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2c3e50" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 4v16M18 4v16"/>
                <path d="M4 8h4M16 8h4M4 16h4M16 16h4"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
        </div>
        <span>Fitness Hub</span>
    </div>

    <nav class="sidebar-nav">

        <a href="admin_dashboard.php" class="<?php echo nav_class('dashboard', $active_page); ?>">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
            </span>
            Dashboard
        </a>

        <a href="register.php" class="<?php echo nav_class('add_member', $active_page); ?>">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <line x1="19" y1="8" x2="19" y2="14"/>
                    <line x1="22" y1="11" x2="16" y2="11"/>
                </svg>
            </span>
            Add Member
        </a>

        <a href="userdata.php" class="<?php echo nav_class('members', $active_page); ?>">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </span>
            View Members
        </a>

        <a href="trainers.php" class="<?php echo nav_class('trainers', $active_page); ?>">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 4v16M18 4v16"/>
                    <path d="M4 8h4M16 8h4M4 16h4M16 16h4"/>
                    <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
            </span>
            Trainers
        </a>

        <a href="classes.php" class="<?php echo nav_class('classes', $active_page); ?>">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </span>
            Classes
        </a>

        <a href="payments.php" class="<?php echo nav_class('payments', $active_page); ?>">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </span>
            Payments
        </a>

        <a href="membership_plans.php" class="<?php echo nav_class('plans', $active_page); ?>">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
            </span>
            Membership Plans
        </a>

        <a href="../logout.php" class="nav-link">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </span>
            Logout
        </a>

    </nav>
    
    <div class="sidebar-footer">
        &copy; <?php echo date('Y'); ?> Fitness Hub
    </div>
</aside>
