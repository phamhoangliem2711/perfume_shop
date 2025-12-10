<?php
// scripts/generate_sql.php

// Cấu hình thư mục chứa ảnh
$uploadDir = __DIR__ . '/../public/assets/uploads/';
// Tên bảng chứa ảnh trong DB
$tableName = 'images'; 
// Tên cột khóa ngoại trỏ về sản phẩm
$fkColumn = 'product_id';
// Tên cột lưu tên file
$fileColumn = 'file_name'; 

if (!is_dir($uploadDir)) {
    die("Không tìm thấy thư mục: $uploadDir\n");
}

$files = scandir($uploadDir);
$sqlStatements = [];

echo "-- COPY CÁC DÒNG DƯỚI ĐÂY VÀO PHPMYADMIN --\n\n";

foreach ($files as $file) {
    // Bỏ qua . và ..
    if ($file === '.' || $file === '..') continue;

    // Kiểm tra định dạng: Số_TênFile (Ví dụ: 5_chanel_1.jpg)
    if (preg_match('/^(\d+)_(.+\.(jpg|jpeg|png|gif))$/i', $file, $matches)) {
        $productId = $matches[1]; // Số 5
        $fileName  = $file;       // 5_chanel_1.jpg

        // Tạo câu SQL
        // Giả sử bảng images có cột: id, product_id, file_name
        $sql = "INSERT INTO `$tableName` (`$fkColumn`, `$fileColumn`) VALUES ($productId, '$fileName');";
        echo $sql . "\n";
    }
}

echo "\n-- KẾT THÚC --\n";
?>