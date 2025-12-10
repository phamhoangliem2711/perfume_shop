<?php
require_once __DIR__ . '/helpers.php'; // chỉnh lại đường dẫn nếu cần
$db = db_connect();

// Thông tin admin mặc định
$email = 'admin@shop.com';
$name = 'Quản trị viên';
$password = 'admin123'; // Bạn có thể đổi mật khẩu này

// Kiểm tra xem admin đã tồn tại chưa
$stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    echo "Admin với email $email đã tồn tại.\n";
    exit;
}

// Tạo admin mới
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $db->prepare('INSERT INTO users (ten, email, mat_khau, role, ngay_tao) VALUES (?, ?, ?, "admin", NOW())');
$stmt->execute([$name, $email, $hash]);

echo "Admin đã được tạo thành công.\n";
echo "Email: $email\n";
echo "Mật khẩu: $password\n";
