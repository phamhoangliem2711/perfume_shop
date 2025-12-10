<?php
require_once __DIR__ . '/../helpers.php';  // file helper chứa hàm db_connect(), base_url(), ...
$db = db_connect();
$pageTitle = 'Trang chủ - Perfume Shop';
include __DIR__ . '/header.php';

/* =============================
   XỬ LÝ LỌC CATEGORY + BRAND
================================ */
$where = [];
$params = [];

// lọc theo danh mục
if (!empty($_GET['category'])) {
    $where[] = "p.danh_muc_id = ?";
    $params[] = intval($_GET['category']);
}

// lọc theo thương hiệu
if (!empty($_GET['brand'])) {
    $where[] = "p.thuong_hieu_id = ?";
    $params[] = intval($_GET['brand']);
}

// ghép điều kiện WHERE
$sqlWhere = "";
if ($where) {
    $sqlWhere = "WHERE " . implode(" AND ", $where);
}

/* =============================
   LẤY DANH SÁCH SẢN PHẨM
================================ */
$sql = "
    SELECT p.id, p.ten, p.mo_ta, 
           MIN(v.gia) AS gia,
           (SELECT url FROM images WHERE product_id = p.id LIMIT 1) AS image
    FROM products p
    LEFT JOIN variants v ON v.product_id = p.id
    $sqlWhere
    GROUP BY p.id
    ORDER BY p.id DESC
";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =============================
   LẤY DANH MỤC + THƯƠNG HIỆU
================================ */
$cats = $db->query("SELECT id, ten FROM categories ORDER BY ten")->fetchAll(PDO::FETCH_ASSOC);
$brands = $db->query("SELECT id, ten FROM brands ORDER BY ten")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
  body { background: #f8f9fa; }
  .card-img-top { object-fit: cover; height: 200px; }
  .navbar-brand { font-weight: 700; }
  .card-title { font-size: 1.1rem; }
  .container h1 { font-size: 1.6rem; }
</style>

<div class="row">

  <!-- ============================= -->
  <!--        DANH SÁCH SẢN PHẨM     -->
  <!-- ============================= -->
  <div class="col-md-9">
    <h1 class="mb-4">Sản phẩm nổi bật</h1>

    <?php if (empty($products)): ?>
      <div class="alert alert-warning">Không có sản phẩm nào trong bộ lọc.</div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-3 g-3">
      <?php foreach ($products as $p): ?>
        <div class="col">
          <div class="card h-100 d-flex flex-column">
           <?php if (!empty($p['image'])): ?>
              <img src="<?= base_url('/public/assets/' . $p['image']) ?>" 
                   class="card-img-top" 
                   alt="<?= htmlspecialchars($p['ten']) ?>"
                   onerror="this.style.display='none'"> 
            <?php endif; ?>

            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($p['ten']) ?></h5>
              <p class="card-text mb-2">
                Giá từ:
                <strong><?= number_format($p['gia'] ?? 0, 0, ',', '.') ?>₫</strong>
              </p>

              <form method="get" action="cart.php" class="mt-auto d-flex align-items-center gap-2">
                <input type="hidden" name="action" value="add" />
                <input type="hidden" name="product_id" value="<?= $p['id'] ?>" />

                <label>
                  SL:
                  <input type="number" name="q" value="1" min="1"
                    style="width: 60px" class="form-control form-control-sm ms-1" />
                </label>

                <button type="submit" class="btn btn-primary btn-sm">Thêm vào giỏ</button>
              </form>

              <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-link btn-sm mt-2">
                Xem chi tiết
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  </div>

  <!-- ============================= -->
  <!--             SIDEBAR           -->
  <!-- ============================= -->
  <div class="col-md-3">

    <div class="card mb-3">
      <div class="card-body">
        <h5 class="card-title">Bộ lọc</h5>
        <p>Giúp bạn tìm sản phẩm nhanh hơn</p>
      </div>
    </div>

    <!-- DANH MỤC -->
    <div class="card mb-3">
      <div class="card-body">
        <h6>Danh mục</h6>
        <ul class="list-unstyled">
          <?php foreach ($cats as $c): ?>
            <li>
              <a href="<?= base_url('/public/index.php') ?>?category=<?= $c['id'] ?>"
                class="<?= (($_GET['category'] ?? 0) == $c['id']) ? 'fw-bold' : '' ?>">
                <?= htmlspecialchars($c['ten']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <!-- THƯƠNG HIỆU -->
    <div class="card mb-3">
      <div class="card-body">
        <h6>Thương hiệu</h6>
        <ul class="list-unstyled">
          <?php foreach ($brands as $b): ?>
            <li>
              <a href="<?= base_url('/public/index.php') ?>?brand=<?= $b['id'] ?>"
                class="<?= (($_GET['brand'] ?? 0) == $b['id']) ? 'fw-bold' : '' ?>">
                <?= htmlspecialchars($b['ten']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <a href="<?= base_url('/public/cart.php') ?>" class="btn btn-sm btn-primary">
          Xem giỏ hàng
        </a>
      </div>
    </div>

  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
