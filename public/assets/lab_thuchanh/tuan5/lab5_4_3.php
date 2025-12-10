<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>4.3 - Đăng ký thành viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <script>
    function validateForm() {
            // Lấy giá trị từ các trường input
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirm_password').value;
            var gender = document.querySelector('input[name="gender"]:checked');
            var province = document.getElementById('province').value;

            // Kiểm tra tên đăng nhập
            if (username === "") {
                alert("Tên đăng nhập không được để trống.");
                return false;
            }

            // Kiểm tra mật khẩu
            if (password === "") {
                alert("Mật khẩu không được để trống.");
                return false;
            }

            // Kiểm tra xác nhận mật khẩu
            if (confirmPassword !== password) {
                alert("Mật khẩu và xác nhận mật khẩu không khớp.");
                return false;
            }

            // Kiểm tra giới tính
            if (!gender) {
                alert("Bạn chưa chọn giới tính.");
                return false;
            }

            // Kiểm tra tỉnh thành
            if (province === "") {
                alert("Bạn phải chọn tỉnh.");
                return false;
            }

            // Kiểm tra hình ảnh
            var fileInput = document.getElementById('image');
            if (fileInput.files.length > 0) {
                var file = fileInput.files[0];
                var fileType = file.type;
                var validTypes = ['image/jpeg', 'image/png', 'image/bmp', 'image/gif'];
                
                if (!validTypes.includes(fileType)) {
                    alert("Hình ảnh phải có định dạng jpg, png, bmp, hoặc gif.");
                    return false;
                }
            }

            // Nếu tất cả kiểm tra đều đúng, cho phép gửi form
            return true;
        }
    </script>
</head>
<body class="container">
    <h2>Đăng ký thành viên</h2>
    <form style="background-color: #f9f9f9;" action="" method="post" enctype="multipart/form-data" class="form-control">
        <label for="username">Tên đăng nhập (*):</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Mật khẩu (*):</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="confirm_password">Nhập lại mật khẩu (*):</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>

        <label for="gender">Giới tính (*):</label><br>
        <input type="radio" id="male" name="gender" value="Nam" required>
        <label for="male">Nam</label><br>
        <input type="radio" id="female" name="gender" value="Nữ" required>
        <label for="female">Nữ</label><br><br>

        <label for="hobbies">Sở thích:</label><br>
        <textarea id="hobbies" name="hobbies"></textarea><br><br>

        <label for="image">Hình ảnh (tùy chọn):</label>
        <input type="file" id="image" name="image" accept="image/*"><br><br>

        <label for="province">Tỉnh (*):</label>
        <select id="province" name="province" required>
            <option value="">Chọn tỉnh</option>
            <option value="Hà Nội">Hà Nội</option>
            <option value="Hồ Chí Minh">Hồ Chí Minh</option>
            <option value="Đà Nẵng">Đà Nẵng</option>
            <option value="Khánh Hòa">Khánh Hòa</option>
            <!-- Thêm các tỉnh khác nếu cần -->
        </select><br><br>

        <input type="submit" value="Đăng ký" class="btn btn-primary">
        <input type="reset" value="Reset" class="btn btn-danger">
    </form>
    <?php
        if(isset($_POST['username'])){
            echo "Ten TK: " .$_POST['username'];
            echo "<br>";
        }
        if(isset($_POST['password'])){
            if($_POST['password'] == $_POST['confirm_password']){
                echo "MK: " .$_POST['confirm_password'];
                echo "<br>";
            }
            else{
                echo "MK va nhap lai MK khong hop le <br>";
            }
        }
        if(isset($_POST['gender'])){
            echo "GT: " .$_POST['gender'];
            echo "<br>";
        }
        if(isset($_POST['hobbies'])){
            echo "So thich: " .$_POST['hobbies'];
            echo "<br>";
        }
        if (isset($_FILES['image'])) {
            $allowImage = array('jpg', 'png', 'bmp', 'gif');
            $fileName = $_FILES['image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); // Lấy phần mở rộng của file
    
            if (in_array($fileExtension, $allowImage)) {
                echo "Hình ảnh hợp lệ: " . $fileName;
            } else {
                echo "Chỉ chấp nhận các định dạng hình ảnh: jpg, png, bmp, gif.";
            }
        }
        if(isset($_POST['province'])){
            echo "Tinh thanh: " .$_POST['province'];
            echo "<br>";
        }
    ?>
    </body>
</html>