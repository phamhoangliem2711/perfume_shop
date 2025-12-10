<?php
require_once __DIR__ . '/../helpers.php';
require_login();
if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    echo 'Bạn không có quyền truy cập.';
    exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) die('ID đơn hàng không hợp lệ.');

$db = db_connect();

// Lấy đơn hàng
$stmt = $db->prepare("SELECT o.*, u.ten AS user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$order) die('Đơn hàng không tồn tại.');

// Lấy chi tiết đơn hàng (order_items)
$stmt = $db->prepare("SELECT oi.*, p.ten, v.gia FROM order_items oi LEFT JOIN variants v ON oi.variant_id = v.id LEFT JOIN products p ON v.product_id = p.id WHERE oi.order_id = ?");
$stmt->execute([$id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$allowed_status = ['cho_duyet' => 'Chờ duyệt', 'dang_giao' => 'Đang giao', 'hoan_thanh' => 'Hoàn thành', 'huy' => 'Hủy'];

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['trang_thai'] ?? '';
    if (array_key_exists($new_status, $allowed_status)) {
        $stmt = $db->prepare("UPDATE orders SET trang_thai = ? WHERE id = ?");
        $stmt->execute([$new_status, $id]);
        header('Location: ' . base_url('/admin/order_detail.php?id=' . $id));
        exit;
    } else {
        $errors[] = 'Trạng thái không hợp lệ.';
    }
}

$pageTitle = 'Chi tiết đơn hàng #' . $order['id'];
include __DIR__ . '/header.php';
?>

<div class="container">
    <h1 class="my-4"><?= $pageTitle ?></h1>

    <p><strong>Khách hàng:</strong> <?= htmlspecialchars($order['user_name']) ?></p>
    <p><strong>Ngày đặt:</strong> <?= $order['ngay_dat'] ?></p>
    <p><strong>Trạng thái:</strong> <?= htmlspecialchars($allowed_status[$order['trang_thai']] ?? $order['trang_thai']) ?></p>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul><?php foreach ($errors as $err) echo "<li>$err</li>" ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" class="mb-4">
        <label for="trang_thai" class="form-label">Cập nhật trạng thái</label>
        <select name="trang_thai" id="trang_thai" class="form-select mb-2">
            <?php foreach ($allowed_status as $key => $label): ?>
                <option value="<?= $key ?>" <?= ($order['trang_thai'] === $key) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-primary">Cập nhật</button>
        <a href="orders.php" class="btn btn-secondary">Quay lại</a>
    </form>

    <h3>Chi tiết sản phẩm</h3>
    <table class="table table-bordered">
        <thead>
            <tr><th>Tên sản phẩm</th><th>Số lượng</th><th>Giá</th><th>Tổng</th></tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['ten']) ?></td>
                <td><?= $item['so_luong'] ?></td>
                <td><?= number_format($item['gia'], 0, ',', '.') ?>₫</td>
                <td><?= number_format($item['gia'] * $item['so_luong'], 0, ',', '.') ?>₫</td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
            <tr><td colspan="4" class="text-center">Không có sản phẩm nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/footer.php'; ?>
