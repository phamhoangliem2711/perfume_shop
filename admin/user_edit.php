<?php
require_once __DIR__ . '/../helpers.php';
require_login();
if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    echo 'Bạn không có quyền truy cập.';
    exit;
}

$db = db_connect();

$id = intval($_GET['id'] ?? 0);
$ten = '';
$email = '';
$role = 'user';
$errors = [];

if ($id) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) die('Người dùng không tồn tại');
    $ten = $user['ten'];
    $email = $user['email'];
    $role = $user['role'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = input('ten');
    $email = input('email');
    $role = input('role');
    $password = input('password');

    if (!$ten) {
        $errors[] = 'Tên không được để trống.';
    }
    if (!$email) {
        $errors[] = 'Email không được để trống.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ.';
    }
    if (!in_array($role, ['user', 'admin'])) {
        $errors[] = 'Role không hợp lệ.';
    }

    // Nếu thêm mới hoặc đổi mật khẩu
    if (!$id || $password) {
        if (!$password) {
            $errors[] = 'Mật khẩu không được để trống khi tạo mới hoặc đổi mật khẩu.';
        }
        // Mật khẩu có thể hash ở đây (hiện tại chưa có hash)
    }

    if (empty($errors)) {
        if ($id) {
            if ($password) {
                $stmt = $db->prepare("UPDATE users SET ten=?, email=?, role=?, mat_khau=? WHERE id=?");
                $stmt->execute([$ten, $email, $role, $password, $id]);
            } else {
                $stmt = $db->prepare("UPDATE users SET ten=?, email=?, role=? WHERE id=?");
                $stmt->execute([$ten, $email, $role, $id]);
            }
        } else {
            $stmt = $db->prepare("INSERT INTO users (ten, email, role, mat_khau) VALUES (?, ?, ?, ?)");
            $stmt->execute([$ten, $email, $role, $password]);
            $id = $db->lastInsertId();
        }
        header('Location: ' . base_url('/admin/users.php'));
        exit;
    }
}

$pageTitle = ($id ? 'Sửa' : 'Thêm') . ' người dùng - Admin';
include __DIR__ . '/header.php';
?>

<div class="container">
    <h1 class="my-4"><?= $pageTitle ?></h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul><?php foreach ($errors as $err) echo "<li>$err</li>" ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" class="row g-3">
        <div class="col-12">
            <label class="form-label">Tên</label>
            <input type="text" name="ten" class="form-control" value="<?= htmlspecialchars($ten) ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Role</label>
            <select name="role" class="form-select">
                <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Mật khẩu <?= $id ? '(để trống nếu không đổi)' : '' ?></label>
            <input type="password" name="password" class="form-control" <?= $id ? '' : 'required' ?>>
        </div>

        <div class="col-12">
            <button class="btn btn-primary"><?= $id ? 'Cập nhật' : 'Thêm mới' ?></button>
            <a href="users.php" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
