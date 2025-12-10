<?php
require_once __DIR__ . '/../helpers.php';
require_login();

// Kiểm tra user có role là admin không
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
    echo '<div class="alert alert-danger m-4">Bạn không có quyền truy cập.</div>';
    exit;
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard - Perfume Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="#">Admin Dashboard</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('/public/logout.php') ?>">Đăng xuất</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
  <h1 class="mb-4">Admin Dashboard</h1>

  <ul class="list-group" style="max-width: 400px;">
    <li class="list-group-item">
      <a href="products.php" class="text-decoration-none">Quản lý sản phẩm</a>
    </li>
    <li class="list-group-item">
      <a href="orders.php" class="text-decoration-none">Quản lý đơn hàng</a>
    </li>
    <li class="list-group-item">
      <a href="users.php" class="text-decoration-none">Quản lý người dùng</a>
    </li>
    <li class="list-group-item">
      <a href="categories.php" class="text-decoration-none">Quản lý danh mục</a>
    </li>
    <li class="list-group-item">
      <a href="brands.php" class="text-decoration-none">Quản lý thương hiệu</a>
    </li>
  </ul>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
