<?php
require_once __DIR__ . '/../helpers.php';
require_login();
if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    echo 'Bạn không có quyền truy cập.';
    exit;
}

$db = db_connect();

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Không xóa admin hiện tại hoặc chính bạn (bạn có thể bổ sung logic)
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ' . base_url('/admin/users.php'));
    exit;
}

$users = $db->query("SELECT id, ten, email, role FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Quản lý người dùng - Admin';
include __DIR__ . '/header.php';
?>

<div class="container">
    <h1 class="my-4">Quản lý người dùng</h1>

    <a href="user_edit.php" class="btn btn-success mb-3">Thêm người dùng mới</a>

    <table class="table table-bordered table-hover">
        <thead>
            <tr><th>ID</th><th>Tên</th><th>Email</th><th>Role</th><th>Thao tác</th></tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['ten']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['role']) ?></td>
                <td>
                    <a href="user_edit.php?id=<?= $u['id'] ?>" class="btn btn-primary btn-sm">Sửa</a>
                    <a href="users.php?delete=<?= $u['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa?');" class="btn btn-danger btn-sm">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
            <tr><td colspan="5" class="text-center">Không có người dùng nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/footer.php'; ?>
