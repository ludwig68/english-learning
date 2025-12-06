<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare("UPDATE vocabularies SET deleted_at = NOW() WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: /admin/vocab_list.php');
exit;
