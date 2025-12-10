<?php
require_once __DIR__ . '/../helpers.php';
$db = db_connect();
require_login();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Lấy sản phẩm từ giỏ
$items = [];
$ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
$stmt = $db->query("SELECT v.id AS variant_id, v.gia, v.dung_tich, p.id AS product_id, p.ten FROM variants v JOIN products p ON p.id = v.product_id WHERE v.id IN ($ids)");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total = 0;
foreach ($rows as $r) {
    $qty = $_SESSION['cart'][$r['variant_id']];
    $items[] = ['variant' => $r, 'qty' => $qty, 'subtotal' => $r['gia'] * $qty];
    $total += $r['gia'] * $qty;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user']['id'];

    // Xử lý thanh toán
    try {
        $db->beginTransaction();
        $stmt = $db->prepare("INSERT INTO orders (user_id, tong_tien, trang_thai, ngay_dat) VALUES (?, ?, 'cho_duyet', NOW())");
        $stmt->execute([$user_id, $total]);
        $order_id = $db->lastInsertId();

        $stmt_item = $db->prepare("INSERT INTO order_items (order_id, variant_id, so_luong, don_gia) VALUES (?, ?, ?, ?)");
        foreach ($items as $it) {
            $stmt_item->execute([$order_id, $it['variant']['variant_id'], $it['qty'], $it['variant']['gia']]);
        }
        $db->commit();

        $_SESSION['cart'] = [];
        header('Location: order_complete.php?id=' . $order_id);
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        $errors[] = $e->getMessage();
    }
}

// Bây giờ mới include header để bắt đầu in HTML
$pageTitle = 'Thanh toán';
include __DIR__ . '/header.php';
?>

<h1>Thanh toán</h1>
<?php if (!empty($errors)) { echo '<ul style="color:red;">'; foreach($errors as $err) echo "<li>$err</li>"; echo '</ul>'; } ?>
<form method="post" action="">
    <p>Người đặt: <?= htmlspecialchars($_SESSION['user']['ten']) ?> (<?= htmlspecialchars($_SESSION['user']['email']) ?>)</p>
  <h3>Tóm tắt đơn hàng</h3>
  <ul>
    <?php foreach ($items as $it): ?>
        <li><?= htmlspecialchars($it['variant']['ten']) ?> (<?= $it['variant']['dung_tich'] ?>ml) x <?= $it['qty'] ?> = <?= number_format($it['subtotal'],0,',','.') ?>₫</li>
    <?php endforeach; ?>
  </ul>
  <p>Tổng thanh toán: <?= number_format($total,0,',','.') ?>₫</p>
    <button class="btn btn-success" type="submit">Xác nhận thanh toán</button>
</form>

<?php include __DIR__ . '/footer.php'; ?>
