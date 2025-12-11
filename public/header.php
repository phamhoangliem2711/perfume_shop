<?php
require_once __DIR__ . '/../helpers.php';
$db = db_connect();
// fetch categories and brands for navbar
$cats = $db->query("SELECT id, ten FROM categories ORDER BY ten")->fetchAll(PDO::FETCH_ASSOC);
$brands = $db->query("SELECT id, ten FROM brands ORDER BY ten")->fetchAll(PDO::FETCH_ASSOC);

// tính số lượng sản phẩm trong giỏ
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Perfume Shop' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('/public/assets/style.css') ?>" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand" href="<?= base_url('/index.php') ?>">Perfume Shop</a>
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarPerfume"
      aria-controls="navbarPerfume"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarPerfume">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item dropdown">
          <a
            class="nav-link dropdown-toggle"
            href="#"
            id="navCat"
            role="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            >Danh mục</a
          >
          <ul class="dropdown-menu" aria-labelledby="navCat">
            <?php foreach ($cats as $c): ?>
              <li>
                <a class="dropdown-item" href="<?= base_url('/index.php') ?>?category=<?= $c['id'] ?>"
                  ><?= htmlspecialchars($c['ten']) ?></a
                >
              </li>
            <?php endforeach; ?>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a
            class="nav-link dropdown-toggle"
            href="#"
            id="navBrand"
            role="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            >Thương hiệu</a
          >
          <ul class="dropdown-menu" aria-labelledby="navBrand">
            <?php foreach ($brands as $b): ?>
              <li>
                <a class="dropdown-item" href="<?= base_url('/index.php') ?>?brand=<?= $b['id'] ?>"
                  ><?= htmlspecialchars($b['ten']) ?></a
                >
              </li>
            <?php endforeach; ?>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('/public/lab.php') ?>">📚 Lab Thực Hành</a>
        </li>
      </ul>

      <form class="d-flex me-3" method="get" action="<?= base_url('/index.php') ?>">
        <input
          name="q"
          class="form-control me-2"
          type="search"
          placeholder="Tìm sản phẩm..."
          aria-label="Search"
        />
        <button class="btn btn-outline-secondary" type="submit">Tìm</button>
      </form>

      <ul class="navbar-nav mb-2 mb-lg-0 align-items-center">
        <?php if (is_logged_in()): ?>
          <li class="nav-item me-3 position-relative">
            <a class="nav-link" href="<?= base_url('/public/cart.php') ?>">
              🛒 Giỏ hàng
              <?php if ($cart_count > 0): ?>
                <span
                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                  style="font-size: 0.75rem;"
                  >
                  <?= $cart_count ?>
                  <span class="visually-hidden">sản phẩm trong giỏ</span>
                </span>
              <?php endif; ?>
            </a>
          </li>
          <li class="nav-item me-2"><a class="nav-link" href="#">Xin chào, <?= htmlspecialchars($_SESSION['user']['ten']) ?></a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('/public/logout.php') ?>">Đăng xuất</a></li>
        <?php else: ?>
          <li class="nav-item me-2"><a class="nav-link" href="<?= base_url('/public/login.php') ?>">Đăng nhập</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('/public/register.php') ?>">Đăng ký</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">
