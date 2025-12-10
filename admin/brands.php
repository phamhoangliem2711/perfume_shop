<?php
require_once __DIR__ . '/../helpers.php';
require_login();
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
    echo 'Bạn không có quyền truy cập.';
    exit;
}

$db = db_connect();
$brands = $db->query("SELECT id, ten, mo_ta FROM brands ORDER BY ten")->fetchAll(PDO::FETCH_ASSOC);
include __DIR__ . '/header.php';
?>

<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <title>Quản lý thương hiệu - Admin Perfume Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
  <h1>Quản lý thương hiệu</h1>
  

  <a href="brands_add.php" class="btn btn-primary mb-3">Thêm thương hiệu mới</a>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Tên</th>
        <th>Mô tả</th>
        <th>Thao tác</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($brands as $b): ?>
      <tr>
        <td><?= $b['id'] ?></td>
        <td><?= htmlspecialchars($b['ten']) ?></td>
        <td><?= htmlspecialchars($b['mo_ta'] ?? '') ?></td> <!-- fix lỗi -->
        <td>
          <a href="brands_edit.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
          <a href="brands_delete.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-danger"
             onclick="return confirm('Bạn có chắc muốn xóa thương hiệu này?');">Xóa</a>
        </td>
      </tr>
      <?php endforeach; ?>
      
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
