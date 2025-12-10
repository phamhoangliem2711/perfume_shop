<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>4.2 - Search</title>
        <script>
            // Hàm kiểm tra và chọn checkbox "Tất cả" nếu không có loại nào được chọn
            function checkAll() {
                const checkboxes = document.getElementsByName('loai[]');
                const selectAllCheckbox = document.querySelector('input[value="tatca"]');

                // Kiểm tra nếu không có checkbox nào được chọn
                let anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

                // Nếu không có checkbox nào được chọn, tự động chọn "Tất cả"
                if (!anyChecked) {
                    selectAllCheckbox.checked = true;
                    alert('Tất cả được chọn nếu ko chọn cái nào!');
                }
            }
        </script>
    </head>
    <body>
        <fieldset>
            <legend>Form_4.2</legend>
            <form action="index.php" method="get">
                Nhập tên sản phầm cần tìm :<input type="text" name="ten" required>
                <br>
                Cách tìm:<input type="radio" name="ct" value="Gan_dung">
                Gần đúng
		  <input type="radio" name="ct" value="Chinh_xac">
                Chính xác<br>
                Loại sản phẩm:<br>
                <input type="checkbox" name="loai[]" value="loai1">
                Loại 1<br>
                <input type="checkbox" name="loai[]" value="loai2">
                Loại 2<br>
                <input type="checkbox" name="loai[]" value="loai3">
                Loại 3<br>
                <input type="checkbox" name="loai[]" value="tatca">
                Tất cả<br>
                <input type="submit" onclick="checkAll();">
        </fieldset>
        <u>Chưa</u>
        chọn loại. <hr>
        Array()
        <?php
            if(isset($_GET['ten'])){
                echo "Ten san pham: " .htmlspecialchars($_GET['ten']);
                echo "<br>";
            }
            if(isset($_GET['ct'])){
                echo "Cach tim: " .htmlspecialchars($_GET['ct']);
                echo "<br>";
            }
            if(isset($_GET['loai'])){
                echo "Laoi san pham: ";
                if(is_array($_GET['loai'])){
                    echo implode(", ",$_GET['loai']);
                }
            }
            else{
                echo "Chua chon loai";
            }
            echo "<hr>";
            print_r($_GET);
        ?>
    </body>
</html>
