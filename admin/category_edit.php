<?php
require_once __DIR__ . '/../helpers.php';
require_login();
if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    echo 'Bạn không có quyền truy cập.';
    exit;
}

$db = db_connect();

$id = intval($_GET['id'] ?? 0);
$ten = '';
$mo_ta = '';
$errors = [];

if ($id) {
    $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $cat = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cat) die('Danh mục không tồn tại');
    $ten = $cat['ten'];
    $mo_ta = $cat['mo_ta'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = input('ten');
    $mo_ta = input('mo_ta');

    if (!$ten) {
        $errors[] = 'Tên danh mục không được để trống.';
    }

    if (empty($errors)) {
        if ($id) {
            $stmt = $db->prepare("UPDATE categories SET ten=?, mo_ta=? WHERE id=?");
            $stmt->execute([$ten, $mo_ta, $id]);
        } else {
            $stmt = $db->prepare("INSERT INTO categories (ten, mo_ta) VALUES (?, ?)");
            $stmt->execute([$ten, $mo_ta]);
            $id = $db->lastInsertId();
        }
        header('Location: ' . base_url('/admin/categories.php'));
        exit;
    }
}

$pageTitle = ($id ? 'Sửa' : 'Thêm') . ' danh mục - Admin';
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
            <label class="form-label">Tên danh mục</label>
            <input type="text" name="ten" class="form-control" value="<?= htmlspecialchars($ten) ?>" required>
        </div>
        <div class="col-12">
            <label class="form-label">Mô tả</label>
            <textarea name="mo_ta" class="form-control"><?= htmlspecialchars($mo_ta) ?></textarea>
        </div>

        <div class="col-12">
            <button class="btn btn-primary"><?= $id ? 'Cập nhật' : 'Thêm mới' ?></button>
            <a href="categories.php" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
