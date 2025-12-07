<?php
// config/db.local.php
$host    = '127.0.0.1';
$db      = 'english_learning';  // tên DB local
$user    = 'root';              // thường là root
$pass    = '';                  // XAMPP/WAMP thường để rỗng
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Lỗi kết nối CSDL (LOCAL): ' . $e->getMessage());
}
?>