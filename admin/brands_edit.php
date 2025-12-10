<?php
require_once __DIR__ . '/../helpers.php';
require_login();

if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
    echo 'Bạn không có quyền truy cập.';
    exit;
}

$db = db_connect();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: brands.php');
    exit;
}

// Lấy dữ liệu thương hiệu hiện tại
$stmt = $db->prepare("SELECT * FROM brands WHERE id = ?");
$stmt->execute([$id]);
$brand = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$brand) {
    header('Location: brands.php');
    exit;
}

// Lấy giá trị an toàn với default ''
$ten = $brand['ten'] ?? '';
$mo_ta = $brand['mo_ta'] ?? '';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim(input('ten'));
    $mo_ta = trim(input('mo_ta'));

    if ($ten === '') {
        $errors[] = 'Tên thương hiệu không được để trống.';
    }

    if (!$errors) {
        $stmt = $db->prepare("UPDATE brands SET ten = ?, mo_ta = ? WHERE id = ?");
        $stmt->execute([$ten, $mo_ta, $id]);

        header('Location: brands.php');
        exit;
    }
}

$pageTitle = 'Sửa thương hiệu - Admin Perfume Shop';
include __DIR__ . '/header.php';
?>

<div class="container mt-4">
    <h1>Sửa thương hiệu</h1>

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
            <input
                type="text"
                id="ten"
                name="ten"
                class="form-control"
                value="<?= htmlspecialchars($ten) ?>"
                required
            >
        </div>

        <div class="mb-3">
            <label for="mo_ta" class="form-label">Mô tả</label>
            <textarea
                id="mo_ta"
                name="mo_ta"
                class="form-control"
                rows="4"
            ><?= htmlspecialchars($mo_ta) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        <a href="brands.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
