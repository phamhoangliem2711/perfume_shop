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
    $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ' . base_url('/admin/categories.php'));
    exit;
}

$categories = $db->query("SELECT * FROM categories ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Quản lý danh mục - Admin';
include __DIR__ . '/header.php';
?>

<div class="container">
    <h1 class="my-4">Quản lý danh mục</h1>

    <a href="category_edit.php" class="btn btn-success mb-3">Thêm danh mục mới</a>

    <table class="table table-bordered table-hover">
        <thead>
            <tr><th>ID</th><th>Tên</th><th>Mô tả</th><th>Thao tác</th></tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['ten']) ?></td>
                <td><?= htmlspecialchars($c['mo_ta']) ?></td>
                <td>
                    <a href="category_edit.php?id=<?= $c['id'] ?>" class="btn btn-primary btn-sm">Sửa</a>
                    <a href="categories.php?delete=<?= $c['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa?');" class="btn btn-danger btn-sm">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?>
            <tr><td colspan="4" class="text-center">Không có danh mục nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/footer.php'; ?>
