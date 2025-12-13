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

    // Nếu có PDO, kiểm tra trạng thái tài khoản (bị khóa / đã xóa)
    global $pdo;
    if (isset($pdo) && $pdo instanceof PDO) {
        $stmt = $pdo->prepare("SELECT status, deleted_at FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !empty($user['deleted_at']) || $user['status'] === 'blocked') {
            session_unset();
            session_destroy();
            header('Location: /auth/login.php?error=blocked');
            exit;
        }
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
