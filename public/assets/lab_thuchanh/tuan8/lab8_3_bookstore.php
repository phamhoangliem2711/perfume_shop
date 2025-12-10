<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Quản lý loại sách</title>
    <script>
        // JavaScript để làm mới form sau khi submit
        function clearForm() {
            document.getElementById("bookForm").reset();
        }
    </script>
    <style>
        /* Khung chứa nội dung chính */
        #container {
            width: 600px;
            margin: 0 auto; /* căn giữa trang */
        }
    </style>
</head>

<body>
    <div id="container">

        <!-- Form nhập dữ liệu loại sách -->
        <form action="" method="post" id="bookForm">
            <table>
                <tr>
                    <td>Mã sách:</td>
                    <td><input type="text" name="book_id" value=""/></td>
                </tr>
                <tr>
                    <td>Tên sách:</td>
                    <td><input type="text" name="book_name" /></td>
                </tr>
                <tr>
                    <td>Mô tả:</td>
                    <td><input type="text" name="description" /></td>
                </tr>
                <tr>
                    <td>Gía:</td>
                    <td><input type="text" name="price" /></td>
                </tr>
                <tr>
                    <td>Hình ảnh:</td>
                    <td><input type="text" name="img" /></td>
                </tr>
                <tr>
                    <td>Pub:</td>
                    <td>
                        <select name="pub_id" id="">
                            <?php
                                $pdh = new PDO("mysql:host=localhost; dbname=bookstore", "root", "");
                                $pdh->query("set names 'utf8'");
                                $sql_pub = "SELECT pub_id, pub_name FROM publisher";
                                $stm_pub = $pdh->prepare($sql_pub);
                                $stm_pub->execute();
                                $publishers = $stm_pub->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($publishers as $publisher) {
                                    echo "<option value='" . $publisher['pub_id'] . "'>" . $publisher['pub_name'] . "</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Cat:</td>
                    <td>
                        <select name="cat_id" id="">
                        <?php
                                $pdh = new PDO("mysql:host=localhost; dbname=bookstore", "root", "");
                                $pdh->query("set names 'utf8'");
                                $sql_pub = "SELECT cat_id, cat_name FROM category";
                                $stm_pub = $pdh->prepare($sql_pub);
                                $stm_pub->execute();
                                $publishers = $stm_pub->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($publishers as $publisher) {
                                    echo "<option value='" . $publisher['cat_id'] . "'>" . $publisher['cat_name'] . "</option>";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="sm" value="Insert" />
                    </td>
                </tr>
            </table>
        </form>

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
        

        if(isset($_POST['sm'])){
            $book_id = $_POST['book_id'];
            $book_name = $_POST['book_name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $pub_id = $_POST['pub_id'];
            $cat_id = $_POST['cat_id'];
            $img = $_POST['img'];
            $check_sql = "SELECT COUNT(*) FROM book WHERE book_id = :book_id";
            $check_stm = $pdh->prepare($check_sql);
            $check_stm->bindValue(":book_id", $book_id);
            $check_stm->execute();
            $exists = $check_stm->fetchColumn();
            if ($exists > 0) {
                echo "<p>Lỗi: Mã sách này đã tồn tại trong cơ sở dữ liệu!</p>";
            } else {
                $sql = "INSERT INTO book (book_id, book_name, description, price, img, pub_id, cat_id) 
                        VALUES (:book_id, :book_name, :description, :price, :img, :pub_id, :cat_id)";
                $stm = $pdh->prepare($sql);
                $stm->bindValue(":book_id", $book_id);
                $stm->bindValue(":book_name", $book_name);
                $stm->bindValue(":description", $description);
                $stm->bindValue(":price", $price);
                $stm->bindValue(":img", $img);
                $stm->bindValue(":pub_id", $pub_id);
                $stm->bindValue(":cat_id", $cat_id);
        
                if ($stm->execute()) {
                    echo "<p>Sách đã được thêm thành công!</p>";
                } else {
                    echo "<p>Lỗi khi thêm sách!</p>";
                }
            }
        }

        if (isset($_POST['delete_id'])) {
            $delete_book_id = $_POST['delete_id'];
            $delete_sql = "DELETE FROM book WHERE book_id = :book_id";
            $delete_stm = $pdh->prepare($delete_sql);
            $delete_stm->bindValue(":book_id", $delete_book_id);
            
            if ($delete_stm->execute()) {
                echo "<p>Sách đã được xóa thành công!</p>";
            } else {
                echo "<p>Lỗi khi xóa sách!</p>";
            }
        }
        $sql = "SELECT * FROM book";
        $stm = $pdh->prepare($sql);
        $stm->execute();
        $rows = $stm->fetchAll(PDO::FETCH_ASSOC);

        function limitWords($string, $limit = 15) {
            $words = explode(" ", $string);
            if (count($words) > $limit) {
                $string = implode(" ", array_slice($words, 0, $limit)) . "...";
            }
            return $string;
        }

        if (isset($rows) && count($rows) > 0) {
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Tên sách</th><th>Mô tả</th><th>Giá</th><th>Pub</th><th>Cat</th></tr>";
            foreach ($rows as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['book_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['book_name']) . "</td>";
                echo "<td>" . htmlspecialchars(limitWords($row['description'],15)) . "</td>";
                echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                echo "<td>" . htmlspecialchars($row['pub_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['cat_id']) . "</td>";
                echo "<td>
                        <form method='post'>
                            <input type='hidden' name='delete_id' value='" . $row['book_id'] . "' />
                            <input type='submit' value='Xóa' />
                        </form>
                      </td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Không tìm thấy sách nào phù hợp.</p>";
        }
        ?>
    </div>
</body>