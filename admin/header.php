<?php
require_once __DIR__ . '/../helpers.php';
require_login();

// Kiểm tra role admin
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
    echo '<div class="alert alert-danger m-4">Bạn không có quyền truy cập.</div>';
    exit;
}

// Bạn có thể đặt $pageTitle trước khi include header để thay đổi tiêu đề trang
if (!isset($pageTitle)) {
    $pageTitle = 'Admin - Perfume Shop';
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="<?= base_url('/admin/index.php') ?>">Admin Dashboard</a>
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
