<?php
// ------------------- KẾT NỐI DATABASE -------------------
try {
    $pdh = new PDO("mysql:host=localhost;dbname=bookstore", "root", "");
    $pdh->query("SET NAMES 'utf8'");
} catch (Exception $e) {
    exit("Lỗi kết nối: " . $e->getMessage());
}

// ------------------- LẤY DANH SÁCH PUBLISHER VÀ CATEGORY -------------------
$pubs = $pdh->query("SELECT * FROM publisher")->fetchAll(PDO::FETCH_ASSOC);
$cats = $pdh->query("SELECT * FROM category")->fetchAll(PDO::FETCH_ASSOC);

// ------------------- THÊM SÁCH -------------------
if (isset($_POST['add'])) {
    $stm = $pdh->prepare("INSERT INTO book(book_name, description, price, pub_id, cat_id)
                          VALUES(:name, :des, :price, :pub, :cat)");
    $stm->execute([
        ":name" => $_POST['book_name'],
        ":des" => $_POST['description'],
        ":price" => $_POST['price'],
        ":pub" => $_POST['pub_id'],
        ":cat" => $_POST['cat_id']
    ]);
}

// ------------------- SỬA SÁCH -------------------
if (isset($_POST['edit'])) {
    $stm = $pdh->prepare("UPDATE book SET book_name=:name, description=:des, price=:price, pub_id=:pub, cat_id=:cat
                          WHERE book_id=:id");
    $stm->execute([
        ":id" => $_POST['book_id'],
        ":name" => $_POST['book_name'],
        ":des" => $_POST['description'],
        ":price" => $_POST['price'],
        ":pub" => $_POST['pub_id'],
        ":cat" => $_POST['cat_id']
    ]);
}

// ------------------- XÓA SÁCH -------------------
if (isset($_POST['delete'])) {
    $stm = $pdh->prepare("DELETE FROM book WHERE book_id=:id");
    $stm->execute([":id" => $_POST['delete']]);
}

// ------------------- TÌM KIẾM + PHÂN TRANG -------------------
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$search = isset($_POST['search']) ? $_POST['search'] : (isset($_GET['search']) ? $_GET['search'] : "");

$countStm = $pdh->prepare("SELECT COUNT(*) FROM book WHERE book_name LIKE :search");
$countStm->bindValue(":search", "%$search%");
$countStm->execute();
$total = $countStm->fetchColumn();
$total_pages = ceil($total / $limit);

$stm = $pdh->prepare("SELECT * FROM book WHERE book_name LIKE :search LIMIT :start, :limit");
$stm->bindValue(":search", "%$search%");
$stm->bindValue(":start", $start, PDO::PARAM_INT);
$stm->bindValue(":limit", $limit, PDO::PARAM_INT);
$stm->execute();
$rows = $stm->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sách</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">

<div class="container">
    <h2 class="text-center mb-4">📚 Quản lý sách</h2>

    <!-- FORM TÌM KIẾM -->
    <form method="POST" class="card p-3 shadow-sm mb-4">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Nhập tên sách..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addModal">+ Thêm sách</button>
            </div>
        </div>
    </form>

    <!-- BẢNG DANH SÁCH -->
    <table class="table table-bordered table-hover bg-white shadow-sm">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Tên sách</th>
            <th>Giá</th>
            <th>Nhà xuất bản</th>
            <th>Thể loại</th>
            <th>Hành động</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= $r['book_id'] ?></td>
                <td><?= htmlspecialchars($r['book_name']) ?></td>
                <td><?= $r['price'] ?></td>
                <td>
                    <?php
                    foreach($pubs as $p) if($p['pub_id']==$r['pub_id']) echo htmlspecialchars($p['pub_name']);
                    ?>
                </td>
                <td>
                    <?php
                    foreach($cats as $c) if($c['cat_id']==$r['cat_id']) echo htmlspecialchars($c['cat_name']);
                    ?>
                </td>
                <td>
                    <button class="btn btn-warning btn-sm edit-btn"
                            data-id="<?= $r['book_id'] ?>"
                            data-name="<?= htmlspecialchars($r['book_name']) ?>"
                            data-price="<?= $r['price'] ?>"
                            data-pub="<?= $r['pub_id'] ?>"
                            data-cat="<?= $r['cat_id'] ?>"
                            data-des="<?= htmlspecialchars($r['description']) ?>"
                            data-bs-toggle="modal" data-bs-target="#editModal">Sửa</button>

                    <button class="btn btn-danger btn-sm delete-btn"
                            data-id="<?= $r['book_id'] ?>"
                            data-name="<?= htmlspecialchars($r['book_name']) ?>"
                            data-bs-toggle="modal" data-bs-target="#deleteModal">Xóa</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- PHÂN TRANG -->
    <nav>
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">« Trước</a>
            </li>
            <?php for ($i=1;$i<=$total_pages;$i++): ?>
                <li class="page-item <?= ($i==$page)?'active':'' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page >= $total_pages)?'disabled':'' ?>">
                <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">Sau »</a>
            </li>
        </ul>
    </nav>
</div>

<!-- MODAL THÊM -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Thêm sách mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Tên sách</label>
                        <input type="text" name="book_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Giá</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Nhà xuất bản</label>
                        <select name="pub_id" class="form-control" required>
                            <?php foreach($pubs as $p): ?>
                                <option value="<?= $p['pub_id'] ?>"><?= htmlspecialchars($p['pub_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Thể loại</label>
                        <select name="cat_id" class="form-control" required>
                            <?php foreach($cats as $c): ?>
                                <option value="<?= $c['cat_id'] ?>"><?= htmlspecialchars($c['cat_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label>Mô tả</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-success" name="add">Thêm</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL SỬA -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Sửa sách</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="book_id" id="editBookId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Tên sách</label>
                        <input type="text" name="book_name" id="editBookName" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Giá</label>
                        <input type="number" name="price" id="editPrice" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Nhà xuất bản</label>
                        <select name="pub_id" id="editPub" class="form-control" required>
                            <?php foreach($pubs as $p): ?>
                                <option value="<?= $p['pub_id'] ?>"><?= htmlspecialchars($p['pub_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Thể loại</label>
                        <select name="cat_id" id="editCat" class="form-control" required>
                            <?php foreach($cats as $c): ?>
                                <option value="<?= $c['cat_id'] ?>"><?= htmlspecialchars($c['cat_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label>Mô tả</label>
                        <textarea name="description" id="editDes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-warning" name="edit">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL XÓA -->
<div class="modal fade" id="deleteModal">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xóa sách</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc muốn xóa sách <b id="deleteBookName"></b>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-danger" name="delete" id="deleteBookId" value="">Xóa ngay</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Điền dữ liệu vào modal Sửa
document.querySelectorAll('.edit-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
        document.getElementById('editBookId').value = btn.dataset.id;
        document.getElementById('editBookName').value = btn.dataset.name;
        document.getElementById('editPrice').value = btn.dataset.price;
        document.getElementById('editPub').value = btn.dataset.pub;
        document.getElementById('editCat').value = btn.dataset.cat;
        document.getElementById('editDes').value = btn.dataset.des;
    });
});

// Điền dữ liệu vào modal Xóa
document.querySelectorAll('.delete-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
        document.getElementById('deleteBookId').value = btn.dataset.id;
        document.getElementById('deleteBookName').innerText = btn.dataset.name;
    });
});
</script>
</body>
</html>
