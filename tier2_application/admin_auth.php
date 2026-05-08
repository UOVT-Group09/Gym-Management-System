<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function is_admin_authenticated(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_admin_auth(string $login_redirect): void {
    if (is_admin_authenticated()) {
        return;
    }

    $separator = str_contains($login_redirect, '?') ? '&' : '?';
    header('Location: ' . $login_redirect . $separator . 'error=admin_only');
    exit;
}
