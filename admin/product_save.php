<?php
require_once __DIR__ . '/../helpers.php';
require_login();
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
    echo 'Bạn không có quyền truy cập.';
    exit;
}

$db = db_connect();
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$ten = input('ten');
$mo_ta = input('mo_ta', '');
$thuong_hieu_id = input('thuong_hieu_id', null);
$danh_muc_id = input('danh_muc_id', null);
$dung_tich = intval(input('dung_tich', 0));
$gia = floatval(input('gia', 0));
$image_url = input('image', '');

// Validate required fields (bạn có thể mở rộng thêm)
if (!$ten || !$thuong_hieu_id || !$danh_muc_id) {
    echo "Vui lòng nhập đầy đủ thông tin sản phẩm.";
    exit;
}

// Lưu sản phẩm (insert hoặc update)
if ($id) {
    $stmt = $db->prepare('UPDATE products SET ten = ?, mo_ta = ?, thuong_hieu_id = ?, danh_muc_id = ? WHERE id = ?');
    $stmt->execute([$ten, $mo_ta, $thuong_hieu_id, $danh_muc_id, $id]);
} else {
    $stmt = $db->prepare('INSERT INTO products (ten, mo_ta, thuong_hieu_id, danh_muc_id) VALUES (?, ?, ?, ?)');
    $stmt->execute([$ten, $mo_ta, $thuong_hieu_id, $danh_muc_id]);
    $id = $db->lastInsertId();
}

// Xử lý upload ảnh (nếu có)
if (!empty($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
    $uploadDir = __DIR__ . '/../public/assets/uploads';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);
    $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowedExt)) {
        echo "Định dạng ảnh không hợp lệ.";
        exit;
    }
    $filename = uniqid('img_', true) . '.' . $ext;
    if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . '/' . $filename)) {
        $image_url = base_url('/public/assets/uploads/' . $filename);
    } else {
        echo "Lỗi khi upload ảnh.";
        exit;
    }
}

// Nếu có URL ảnh (upload hoặc nhập), insert vào bảng images nếu chưa có
if ($image_url) {
    // Kiểm tra ảnh đã tồn tại chưa (để tránh insert trùng)
    $stmt = $db->prepare('SELECT id FROM images WHERE product_id = ? AND url = ?');
    $stmt->execute([$id, $image_url]);
    if (!$stmt->fetch()) {
        $stmt = $db->prepare('INSERT INTO images (product_id, url) VALUES (?, ?)');
        $stmt->execute([$id, $image_url]);
    }
}

// Thêm biến thể mới (nếu nhập đủ dữ liệu)
if ($dung_tich > 0 && $gia > 0) {
    $stmt = $db->prepare('INSERT INTO variants (product_id, dung_tich, gia) VALUES (?, ?, ?)');
    $stmt->execute([$id, $dung_tich, $gia]);
}

header('Location: products.php');
exit;
