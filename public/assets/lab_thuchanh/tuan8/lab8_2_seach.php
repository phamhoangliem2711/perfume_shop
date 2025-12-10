<!-- <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Lab8_2 - PDO - MySQL - select - insert - parameter</title>
   
</head>

<body>
    <?php
    // ------------------- KẾT NỐI CSDL -------------------
    try {
        // Tạo đối tượng PDO kết nối đến database 'bookstore' với user 'root'
        $pdh = new PDO("mysql:host=localhost; dbname=bookstore", "root", "");
        // Thiết lập bộ mã UTF-8 để hiển thị tiếng Việt đúng
        $pdh->query("set names 'utf8'");
    } catch (Exception $e) {
        // Nếu kết nối thất bại thì báo lỗi và dừng chương trình
        echo $e->getMessage();
        exit;
    }
    if(isset($_POST['search'])){    
        $sql = "SELECT * FROM book where book_name like :search";
        $search = $_POST['search'];
        $stm = $pdh->prepare($sql);
        $stm->bindValue(":search", "%$search%");
        $stm->execute();
        $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
    }


    ?>
    <form action="" method="POST" id="bookForm">
        <table>
            <tr>
                <td>Tên sách</td>
                <td><input type="text" name="search"></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit">Tìm kiếm</button>
                </td>
            </tr>
        </table>
    </form>
    <?php
        if (isset($rows) && count($rows) > 0) {
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Tên sách</th><th>Mô tả</th><th>Giá</th><th>Pub</th><th>Cat</th></tr>";
            foreach ($rows as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['book_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['book_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                echo "<td>" . htmlspecialchars($row['pub_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['cat_id']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Không tìm thấy sách nào phù hợp.</p>";
        }
    ?>
</body>

</html> -->


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Lab8_2 - PDO - MySQL - select - insert - parameter</title>

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background: #f2f2f2;
        }

        .pagination {
            margin-top: 15px;
            text-align: center;
        }

        .pagination a,
        .pagination span {
            display: inline-block;
            padding: 6px 12px;
            margin: 0 3px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .pagination a:hover {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .pagination .active {
            background: #007bff;
            color: white;
            border-color: #007bff;
            font-weight: bold;
        }

        .pagination .disabled {
            color: #aaa;
            border-color: #eee;
        }
    </style>
</head>

<body>

    <?php
    // ------------------- KẾT NỐI CSDL -------------------
    try {
        $pdh = new PDO("mysql:host=localhost; dbname=bookstore", "root", "");
        $pdh->query("set names 'utf8'");
    } catch (Exception $e) {
        echo $e->getMessage();
        exit;
    }

    // --- PHÂN TRANG ---
    $limit = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    // --- TÌM KIẾM ---
    $search = isset($_POST['search']) ? $_POST['search'] : (isset($_GET['search']) ? $_GET['search'] : '');

    // TRUY VẤN DỮ LIỆU CÓ PHÂN TRANG
    $sql = "SELECT * FROM book WHERE book_name LIKE :search LIMIT :offset, :limit";
    $stm = $pdh->prepare($sql);
    $stm->bindValue(":search", "%$search%", PDO::PARAM_STR);
    $stm->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stm->bindValue(":limit", $limit, PDO::PARAM_INT);
    $stm->execute();
    $rows = $stm->fetchAll(PDO::FETCH_ASSOC);

    // ĐẾM TỔNG DÒNG
    $countSql = "SELECT COUNT(*) FROM book WHERE book_name LIKE :search";
    $countStm = $pdh->prepare($countSql);
    $countStm->bindValue(":search", "%$search%");
    $countStm->execute();
    $totalRows = $countStm->fetchColumn();
    $totalPages = ceil($totalRows / $limit);
    ?>

    <!-- FORM TÌM KIẾM -->
    <form action="" method="POST" id="bookForm">
        <table>
            <tr>
                <td>Tên sách</td>
                <td><input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit">Tìm kiếm</button>
                </td>
            </tr>
        </table>
    </form>

    <?php
    // --- HIỂN THỊ BẢNG ---
    if ($totalRows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Tên sách</th><th>Mô tả</th><th>Giá</th><th>Pub</th><th>Cat</th></tr>";

        foreach ($rows as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['book_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['book_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
            echo "<td>" . htmlspecialchars($row['price']) . "</td>";
            echo "<td>" . htmlspecialchars($row['pub_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['cat_id']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        // --- PHÂN TRANG ---
        echo "<div class='pagination'>";

        // Prev
        if ($page > 1) {
            $prev = $page - 1;
            echo "<a href='?page=$prev&search=$search'>Prev</a>";
        } else {
            echo "<span class='disabled'>Prev</span>";
        }

        // Số trang
        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i == $page)
                echo "<span class='active'>$i</span>";
            else
                echo "<a href='?page=$i&search=$search'>$i</a>";
        }

        // Next
        if ($page < $totalPages) {
            $next = $page + 1;
            echo "<a href='?page=$next&search=$search'>Next</a>";
        } else {
            echo "<span class='disabled'>Next</span>";
        }

        echo "</div>";
    } else {
        echo "<p>Không tìm thấy sách nào phù hợp.</p>";
    }
    ?>

</body>

</html>
