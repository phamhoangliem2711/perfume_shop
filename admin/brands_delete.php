<?php
require_once __DIR__ . '/../helpers.php';
require_login();

if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
    echo 'Bạn không có quyền truy cập.';
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: brands.php');
    exit;
}

$db = db_connect();

// Xóa thương hiệu (cần lưu ý nếu có ràng buộc FK trong bảng products)
try {
    $stmt = $db->prepare("DELETE FROM brands WHERE id = ?");
    $stmt->execute([$id]);
} catch (PDOException $e) {
    // Có thể có ràng buộc FK, không xóa được
    echo "Không thể xóa thương hiệu này do ràng buộc dữ liệu.";
    exit;
}

header('Location: brands.php');
exit;
