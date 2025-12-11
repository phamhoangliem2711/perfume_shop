<?php
require_once __DIR__ . '/../helpers.php';

// Nếu đã đăng nhập thì chuyển hướng đúng trang theo role
if (is_logged_in()) {
    $user = $_SESSION['user'] ?? null;
    if ($user && ($user['role'] ?? 'user') === 'admin') {
        header('Location: ' . base_url('/admin/index.php'));
    } else {
        header('Location: ' . base_url('/index.php'));
    }
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = input('email');
    $password = input('password');

    if (!$email || !$password) {
        $errors[] = 'Vui lòng nhập email và mật khẩu';
    } else {
        $db = db_connect();
        $stmt = $db->prepare("SELECT id, ten, email, mat_khau, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // So sánh mật khẩu trực tiếp (bạn nên hash mật khẩu thật ra)
        if ($user && $password === $user['mat_khau']) {
            unset($user['mat_khau']);
            auth_user($user);

            // Redirect theo role
            if (($user['role'] ?? 'user') === 'admin') {
                header('Location: ' . base_url('/admin/index.php'));
            } else {
                header('Location: ' . base_url('/index.php'));
            }
            exit;
        } else {
            $errors[] = 'Thông tin đăng nhập không hợp lệ';
        }
    }
}
?>

<?php 
$pageTitle = 'Đăng nhập - Perfume Shop';
include __DIR__ . '/header.php';
?>

<h3>Đăng nhập</h3>

<?php if (!empty($errors)): ?>
<ul style="color:red;">
    <?php foreach ($errors as $err) echo "<li>$err</li>"; ?>
</ul>
<?php endif; ?>

<form method="post" class="row g-3">
    <div class="col-12"><input class="form-control" name="email" placeholder="Email" required></div>
    <div class="col-12"><input class="form-control" type="password" name="password" placeholder="Mật khẩu" required></div>
    <div class="col-12"><button class="btn btn-primary">Đăng nhập</button></div>
</form>

<p><a href="<?= base_url('/public/register.php') ?>">Tạo tài khoản mới</a></p>

<?php include __DIR__ . '/footer.php'; ?>
