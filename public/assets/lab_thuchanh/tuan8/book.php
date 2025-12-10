<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Quản lý Sách</title>
    <style>
        #container {
            width: 750px;
            margin: 0 auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }

        table,
        th,
        td {
            border: 1px solid black;
            padding: 6px;
        }

        .pagination {
            text-align: center;
            margin-top: 10px;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 5px 10px;
            margin: 0 2px;
            border: 1px solid #ccc;
            text-decoration: none;
        }

        .pagination .active {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }

        .pagination .disabled {
            color: #aaa;
            border-color: #eee;
        }
    </style>
</head>

<body>
<div id="container">

    <?php
    // KẾT NỐI CSDL
    try {
        $pdh = new PDO("mysql:host=localhost; dbname=bookstore", "root", "");
        $pdh->query("set names 'utf8'");
    } catch (Exception $e) {
        echo $e->getMessage();
        exit;
    }

    // ==================== INSERT SÁCH ====================
    if (isset($_POST["sm_insert"])) {
        $sql = "INSERT INTO book(book_id, book_name, description, price, pub_id, cat_id) 
                VALUES(:id, :name, :des, :price, :pub, :cat)";
        $arr = array(
            ":id" => $_POST["book_id"],
            ":name" => $_POST["book_name"],
            ":des" => $_POST["description"],
            ":price" => $_POST["price"],
            ":pub" => $_POST["pub_id"],
            ":cat" => $_POST["cat_id"]
        );
        $stm = $pdh->prepare($sql);
        $stm->execute($arr);
        echo $stm->rowCount() > 0 ? "Đã thêm 1 sách." : "Lỗi thêm sách.";
    }

    // ==================== DELETE SÁCH ====================
    if (isset($_GET["del"])) {
        $id = $_GET["del"];
        $stm = $pdh->prepare("DELETE FROM book WHERE book_id = :id");
        $stm->execute([":id" => $id]);
        echo $stm->rowCount() > 0 ? "Đã xóa 1 sách." : "Lỗi xóa sách.";
        echo "<script>window.location='lab8_book_full.php';</script>";
        exit;
    }

    // ==================== UPDATE SÁCH ====================
    if (isset($_POST["sm_update"])) {
        $sql = "UPDATE book SET book_name=:name, description=:des, price=:price, pub_id=:pub, cat_id=:cat
                WHERE book_id=:id";
        $arr = array(
            ":id" => $_POST["book_id"],
            ":name" => $_POST["book_name"],
            ":des" => $_POST["description"],
            ":price" => $_POST["price"],
            ":pub" => $_POST["pub_id"],
            ":cat" => $_POST["cat_id"]
        );
        $stm = $pdh->prepare($sql);
        $stm->execute($arr);
        echo $stm->rowCount() > 0 ? "Đã cập nhật sách." : "Lỗi cập nhật sách.";
    }

    // ==================== LẤY THÔNG TIN SỬA ====================
    $editBook = null;
    if (isset($_GET["edit"])) {
        $id = $_GET["edit"];
        $stm = $pdh->prepare("SELECT * FROM book WHERE book_id=:id");
        $stm->execute([":id" => $id]);
        $editBook = $stm->fetch(PDO::FETCH_ASSOC);
    }

    // ==================== PHÂN TRANG ====================
    $limit = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    $stm = $pdh->prepare("SELECT COUNT(*) FROM book");
    $stm->execute();
    $totalRows = $stm->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    // Lấy dữ liệu trang hiện tại
    $stm = $pdh->prepare("SELECT * FROM book LIMIT :offset, :limit");
    $stm->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stm->bindValue(":limit", $limit, PDO::PARAM_INT);
    $stm->execute();
    $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <!-- FORM THÊM / SỬA SÁCH -->
    <form action="lab8_book_full.php" method="post">
        <table>
            <tr>
                <td>Mã sách:</td>
                <td>
                    <input type="text" name="book_id" value="<?php echo $editBook ? $editBook['book_id'] : ''; ?>" <?php echo $editBook ? 'readonly' : ''; ?> />
                </td>
            </tr>
            <tr>
                <td>Tên sách:</td>
                <td><input type="text" name="book_name" value="<?php echo $editBook ? $editBook['book_name'] : ''; ?>" /></td>
            </tr>
            <tr>
                <td>Mô tả:</td>
                <td><input type="text" name="description" value="<?php echo $editBook ? $editBook['description'] : ''; ?>" /></td>
            </tr>
            <tr>
                <td>Giá:</td>
                <td><input type="text" name="price" value="<?php echo $editBook ? $editBook['price'] : ''; ?>" /></td>
            </tr>
            <tr>
                <td>Pub ID:</td>
                <td><input type="text" name="pub_id" value="<?php echo $editBook ? $editBook['pub_id'] : ''; ?>" /></td>
            </tr>
            <tr>
                <td>Cat ID:</td>
                <td><input type="text" name="cat_id" value="<?php echo $editBook ? $editBook['cat_id'] : ''; ?>" /></td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php if ($editBook) { ?>
                        <input type="submit" name="sm_update" value="Update" />
                        <a href="lab8_book_full.php">Hủy</a>
                    <?php } else { ?>
                        <input type="submit" name="sm_insert" value="Insert" />
                    <?php } ?>
                </td>
            </tr>
        </table>
    </form>

    <!-- HIỂN THỊ DANH SÁCH SÁCH -->
    <table>
        <tr>
            <th>ID</th>
            <th>Tên sách</th>
            <th>Mô tả</th>
            <th>Giá</th>
            <th>Pub</th>
            <th>Cat</th>
            <th>Hành động</th>
        </tr>
        <?php foreach ($rows as $r) { ?>
            <tr>
                <td><?php echo $r["book_id"]; ?></td>
                <td><?php echo $r["book_name"]; ?></td>
                <td><?php echo $r["description"]; ?></td>
                <td><?php echo $r["price"]; ?></td>
                <td><?php echo $r["pub_id"]; ?></td>
                <td><?php echo $r["cat_id"]; ?></td>
                <td>
                    <a href="lab8_book_full.php?edit=<?php echo $r['book_id']; ?>">Sua</a> |
                    <a href="lab8_book_full.php?del=<?php echo $r['book_id']; ?>" onclick="return confirm('Xóa sách này?');">Xoa</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <!-- PHÂN TRANG -->
    <div class="pagination">
        <?php
        // Prev
        if ($page > 1) {
            $prev = $page - 1;
            echo "<a href='?page=$prev'>Prev</a>";
        } else {
            echo "<span class='disabled'>Prev</span>";
        }

        // Các số trang
        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i == $page)
                echo "<span class='active'>$i</span>";
            else
                echo "<a href='?page=$i'>$i</a>";
        }

        // Next
        if ($page < $totalPages) {
            $next = $page + 1;
            echo "<a href='?page=$next'>Next</a>";
        } else {
            echo "<span class='disabled'>Next</span>";
        }
        ?>
    </div>

</div>
</body>
</html>
