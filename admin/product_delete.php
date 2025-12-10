<?php
require_once __DIR__ . '/../helpers.php';
require_login();
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') { echo 'Bạn không có quyền truy cập.'; exit; }
$db = db_connect();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id) {
    $stmt = $db->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
}
header('Location: products.php');
exit;
