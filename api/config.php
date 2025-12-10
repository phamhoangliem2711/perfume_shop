<?php
$servername = "localhost";
$username = "root";
$password = ""; // hoặc mật khẩu bạn dùng
$dbname = "perfume_shop";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
// sẵn sàng sử dụng $conn để query
?>
