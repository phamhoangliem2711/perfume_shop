<?php
require_once __DIR__ . '/../helpers.php';
$db = db_connect();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pageTitle = 'Chi tiết sản phẩm';
if (!$id) header('Location: index.php');
$stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$p) header('Location: index.php');

$pageTitle = htmlspecialchars($p['ten']);
include __DIR__ . '/header.php';

// get variants for this product
$stmt = $db->prepare("SELECT * FROM variants WHERE product_id = ? ORDER BY gia ASC");
$stmt->execute([$id]);
$variants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// get images
$stmt = $db->prepare("SELECT url FROM images WHERE product_id = ?");
$stmt->execute([$id]);
$images = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!-- content: product -->
<div class="row">
  <div class="col-md-6">
    <?php if (!empty($p['image'])): ?>
        <img src="<?= base_url('/public/assets/' . $p['image']) ?>" 
             alt="<?= htmlspecialchars($p['ten']) ?>" 
             style="width: 100%; height: auto; border: 1px solid #ddd; padding: 5px; border-radius: 8px; object-fit: contain;">
    <?php else: ?>
        <img src="<?= base_url('/public/assets/uploads/no-image.svg') ?>" 
             style="width: 100%; height: auto;" 
             alt="Chưa có ảnh">
    <?php endif; ?>
  
  </div>
</div>
<h3>Biến thể</h3>
<form method="get" action="cart.php" class="d-flex gap-2 align-items-center">
  <input type="hidden" name="action" value="add">
  <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
  <label>Chọn dung tích:
    <select name="variant_id">
      <?php foreach ($variants as $v): ?>
        <option value="<?= $v['id'] ?>"><?= $v['dung_tich'] ?>ml - <?= number_format($v['gia'],0,',','.') ?>₫</option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Số lượng: <input class="form-control" style="width:80px;" type="number" name="q" value="1" min="1"></label>
  <button class="btn btn-primary" type="submit">Thêm vào giỏ</button>
</form>
<p><a href="<?= base_url('/public/index.php') ?>">Về trang chủ</a></p>
<?php include __DIR__ . '/footer.php'; ?>
