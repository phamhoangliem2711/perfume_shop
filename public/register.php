<?php
require_once __DIR__ . '/../helpers.php';
$pageTitle = 'Đăng ký - Perfume Shop';
include __DIR__ . '/header.php';

if (is_logged_in()) {
    header('Location: ' . base_url('/index.php'));
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = input('name');
    $email = input('email');
    $password = input('password');
    if (!$name || !$email || !$password) $errors[] = 'Vui lòng nhập tất cả các trường';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';

    if (empty($errors)) {
        $db = db_connect();
        // unique email
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email đã được đăng ký';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (ten, email, mat_khau, ngay_tao) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $hash]);
            header('Location: ' . base_url('/public/login.php'));
            exit;
        }
    }
}
?>
<h3>Đăng ký</h3>
<?php if (!empty($errors)) { echo '<ul style="color:red;">'; foreach($errors as $err) echo "<li>$err</li>"; echo '</ul>'; } ?>
<form method="post" action="" class="row g-3">
    <div class="col-12"><input class="form-control" name="name" placeholder="Họ và tên" required></div>
    <div class="col-md-6"><input class="form-control" name="email" type="email" placeholder="Email" required></div>
    <div class="col-md-6"><input class="form-control" name="password" type="password" placeholder="Mật khẩu" required></div>
    <div class="col-12"><button class="btn btn-primary" type="submit">Tạo tài khoản</button></div>
</form>
<p><a href="login.php">Đã có tài khoản? Đăng nhập</a></p>
<?php include __DIR__ . '/footer.php'; ?>
