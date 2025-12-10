<?php
require_once __DIR__ . '/../helpers.php';
require_login();
if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    echo 'Bạn không có quyền truy cập.';
    exit;
}

$db = db_connect();

// Xử lý xóa sản phẩm
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ' . base_url('/admin/products.php'));
    exit;
}

// Lấy danh sách sản phẩm với brand + category
$sql = "SELECT p.*, b.ten AS brand_name, c.ten AS category_name 
        FROM products p
        LEFT JOIN brands b ON p.thuong_hieu_id = b.id
        LEFT JOIN categories c ON p.danh_muc_id = c.id
        ORDER BY p.id DESC";

$products = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Quản lý sản phẩm - Admin';
include __DIR__ . '/header.php';
?>

<div class="container">
    <h1 class="my-4">Quản lý sản phẩm</h1>

    <a href="product_edit.php" class="btn btn-success mb-3">Thêm sản phẩm mới</a>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th><th>Tên</th><th>Thương hiệu</th><th>Danh mục</th><th>Trạng thái</th><th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['ten']) ?></td>
                <td><?= htmlspecialchars($p['brand_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['category_name'] ?? '') ?></td>
                <td><?= $p['trang_thai'] == 1 ? 'Hiển thị' : 'Ẩn' ?></td>
                <td>
                    <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">Sửa</a>
                    <a href="products.php?delete=<?= $p['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa?');" class="btn btn-danger btn-sm">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
            <tr><td colspan="6" class="text-center">Không có sản phẩm nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/footer.php'; ?>
