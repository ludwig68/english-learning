<?php
// config/db.php

$charset = 'utf8mb4';

// Nếu tồn tại file db.local.php thì ưu tiên dùng cho môi trường local
$localConfig = __DIR__ . '/db.local.php';
if (file_exists($localConfig)) {
    require $localConfig;
    return; // Dừng lại, không chạy tiếp config dưới
}

/**
 * Mặc định: cấu hình trên host Vietnix
 * Chỉ chạy nếu KHÔNG có db.local.php
 */
$host = 'localhost';
$db   = 'viakingv_englishlearning';
$user = 'viakingv_englishlearning';
$pass = 'viakingv_englishlearning'; // đổi đúng mật khẩu thực tế trên Vietnix

$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Lỗi kết nối CSDL (Vietnix): ' . $e->getMessage());
}
