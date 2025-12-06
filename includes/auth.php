<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function current_user_role(): ?string {
    return $_SESSION['user_role'] ?? null;
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: /auth/login.php');
        exit;
    }
}

function require_admin(): void {
    require_login();
    if (current_user_role() !== 'admin') {
        http_response_code(403);
        echo "Access denied.";
        exit;
    }
}
