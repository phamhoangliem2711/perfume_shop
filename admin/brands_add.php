<?php
require_once __DIR__ . '/../helpers.php';
require_login();

// Chỉ cho admin truy cập
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
    echo 'Bạn không có quyền truy cập.';
    exit;
}

$errors = [];
$ten = '';
$mo_ta = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim(input('ten'));
    $mo_ta = trim(input('mo_ta'));

    if ($ten === '') {
        $errors[] = 'Tên thương hiệu không được để trống.';
    }

    if (!$errors) {
        $db = db_connect();
        $stmt = $db->prepare("INSERT INTO brands (ten, mo_ta) VALUES (?, ?)");
        $stmt->execute([$ten, $mo_ta]);

        header('Location: brands.php');
        exit;
    }
}

$pageTitle = 'Thêm thương hiệu mới - Admin Perfume Shop';
include __DIR__ . '/header.php';
?>

<div class="container mt-4">
    <h1>Thêm thương hiệu mới</h1>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" class="mb-3">
        <div class="mb-3">
            <label for="ten" class="form-label">Tên thương hiệu</label>
            <input type="text" id="ten" name="ten" class="form-control" value="<?= htmlspecialchars($ten) ?>" required>
        </div>

        <div class="mb-3">
            <label for="mo_ta" class="form-label">Mô tả</label>
            <textarea id="mo_ta" name="mo_ta" class="form-control" rows="4"><?= htmlspecialchars($mo_ta) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="brands.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
