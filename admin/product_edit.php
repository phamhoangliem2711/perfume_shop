<?php
require_once __DIR__ . '/../helpers.php';
require_login();
if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    echo 'Bạn không có quyền truy cập.';
    exit;
}

$db = db_connect();

$id = intval($_GET['id'] ?? 0);

$errors = [];
$ten = '';
$mo_ta = '';
$thuong_hieu_id = '';
$danh_muc_id = '';
$trang_thai = 1;

// Lấy dữ liệu brands và categories để select dropdown
$brands = $db->query("SELECT id, ten FROM brands ORDER BY ten")->fetchAll(PDO::FETCH_ASSOC);
$categories = $db->query("SELECT id, ten FROM categories ORDER BY ten")->fetchAll(PDO::FETCH_ASSOC);

if ($id) {
    // Lấy thông tin sản phẩm
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        die('Sản phẩm không tồn tại');
    }
    $ten = $product['ten'];
    $mo_ta = $product['mo_ta'];
    $thuong_hieu_id = $product['thuong_hieu_id'];
    $danh_muc_id = $product['danh_muc_id'];
    $trang_thai = $product['trang_thai'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = input('ten');
    $mo_ta = input('mo_ta');
    $thuong_hieu_id = intval(input('thuong_hieu_id'));
    $danh_muc_id = intval(input('danh_muc_id'));
    $trang_thai = intval(input('trang_thai'));

    if (!$ten) {
        $errors[] = 'Tên sản phẩm không được để trống.';
    }
    if (!$thuong_hieu_id) {
        $errors[] = 'Bạn phải chọn thương hiệu.';
    }
    if (!$danh_muc_id) {
        $errors[] = 'Bạn phải chọn danh mục.';
    }

    if (empty($errors)) {
        if ($id) {
            $stmt = $db->prepare("UPDATE products SET ten=?, mo_ta=?, thuong_hieu_id=?, danh_muc_id=?, trang_thai=? WHERE id=?");
            $stmt->execute([$ten, $mo_ta, $thuong_hieu_id, $danh_muc_id, $trang_thai, $id]);
        } else {
            $stmt = $db->prepare("INSERT INTO products (ten, mo_ta, thuong_hieu_id, danh_muc_id, trang_thai) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$ten, $mo_ta, $thuong_hieu_id, $danh_muc_id, $trang_thai]);
            $id = $db->lastInsertId();
        }
        header('Location: ' . base_url('/admin/products.php'));
        exit;
    }
}

$pageTitle = ($id ? 'Sửa' : 'Thêm') . ' sản phẩm - Admin';
include __DIR__ . '/header.php';
?>

<div class="container">
    <h1 class="my-4"><?= $pageTitle ?></h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul><?php foreach ($errors as $err) echo "<li>$err</li>" ?></ul>
        </div>
    <?php endif; ?>

    <form method="post" class="row g-3">
        <div class="col-12">
            <label class="form-label">Tên sản phẩm</label>
            <input type="text" name="ten" class="form-control" value="<?= htmlspecialchars($ten) ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label">Mô tả</label>
            <textarea name="mo_ta" class="form-control"><?= htmlspecialchars($mo_ta) ?></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Thương hiệu</label>
            <select name="thuong_hieu_id" class="form-select" required>
                <option value="">-- Chọn thương hiệu --</option>
                <?php foreach ($brands as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= $b['id'] == $thuong_hieu_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['ten']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Danh mục</label>
            <select name="danh_muc_id" class="form-select" required>
                <option value="">-- Chọn danh mục --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $c['id'] == $danh_muc_id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['ten']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Trạng thái</label>
            <select name="trang_thai" class="form-select">
                <option value="1" <?= $trang_thai == 1 ? 'selected' : '' ?>>Hiển thị</option>
                <option value="0" <?= $trang_thai == 0 ? 'selected' : '' ?>>Ẩn</option>
            </select>
        </div>

        <div class="col-12">
            <button class="btn btn-primary"><?= $id ? 'Cập nhật' : 'Thêm mới' ?></button>
            <a href="products.php" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
