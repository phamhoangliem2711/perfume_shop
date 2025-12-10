<?php
require_once __DIR__ . '/../helpers.php';
require_login();
if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    echo 'Bạn không có quyền truy cập.';
    exit;
}

$db = db_connect();

// Xử lý cập nhật trạng thái đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['trang_thai'])) {
    $order_id = intval($_POST['order_id']);
    $trang_thai = $_POST['trang_thai'];
    $allowed_status = ['cho_duyet', 'dang_giao', 'hoan_thanh', 'huy'];

    if (in_array($trang_thai, $allowed_status)) {
        $stmt = $db->prepare("UPDATE orders SET trang_thai = ? WHERE id = ?");
        $stmt->execute([$trang_thai, $order_id]);
        header('Location: ' . base_url('/admin/orders.php'));
        exit;
    }
}

// Lấy danh sách đơn hàng
$sql = "SELECT o.*, u.ten AS user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.id DESC";
$orders = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Quản lý đơn hàng - Admin';
include __DIR__ . '/header.php';
?>

<div class="container">
    <h1 class="my-4">Quản lý đơn hàng</h1>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th><th>Khách hàng</th><th>Tổng tiền</th><th>Trạng thái</th><th>Ngày đặt</th><th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= $o['id'] ?></td>
                <td><?= htmlspecialchars($o['user_name'] ?? '') ?></td>
                <td><?= number_format($o['tong_tien'], 0, ',', '.') ?>₫</td>
                <td><?= htmlspecialchars($o['trang_thai']) ?></td>
                <td><?= $o['ngay_dat'] ?></td>
                <td>
                    <a href="order_detail.php?id=<?= $o['id'] ?>" class="btn btn-primary btn-sm">Xem chi tiết</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?>
            <tr><td colspan="6" class="text-center">Không có đơn hàng nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/footer.php'; ?>
