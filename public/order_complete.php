<?php
require_once __DIR__ . '/../helpers.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pageTitle = 'Hoàn tất';
include __DIR__ . '/header.php';
?>
<h1>Đặt hàng thành công</h1>
<p>Cảm ơn bạn đã mua hàng! Mã đơn hàng: <?= $id ?></p>
<p><a href="<?= base_url('/index.php') ?>">Quay về trang chủ</a></p>
<?php include __DIR__ . '/footer.php'; ?>
