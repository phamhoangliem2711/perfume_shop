<?php
// scripts/final_sql_import.php

// Đường dẫn thư mục ảnh
$dir = __DIR__ . '/../public/assets/uploads/';

if (!is_dir($dir)) {
    die("Lỗi: Không tìm thấy thư mục $dir");
}

$files = scandir($dir);
$values = [];

echo "--- COPY ĐOẠN DƯỚI ĐÂY VÀO PHPMYADMIN (TAB SQL) ---\n\n";

foreach ($files as $file) {
    // Chỉ lấy các file đã được đổi tên theo chuẩn: ID_TênGốc.jpg (Ví dụ: 1_Dior_1.jpg)
    if (preg_match('/^(\d+)_(.+\.(jpg|jpeg|png|gif))$/i', $file, $matches)) {
        $productId = $matches[1]; // Lấy số ID ở đầu (ví dụ: 1)
        $fileName  = $file;       // Lấy toàn bộ tên file (ví dụ: 1_Dior_1.jpg)

        // Lưu ý: Nếu web của bạn yêu cầu phải có chữ 'uploads/' ở trước (ví dụ: uploads/1_Dior_1.jpg)
        // thì bỏ comment dòng dưới đây:
        // $fileName = 'uploads/' . $fileName; 

        $values[] = "($productId, '$fileName')";
    }
}

if (count($values) > 0) {
    // Tạo 1 câu lệnh INSERT gộp để chạy nhanh hơn
    echo "INSERT INTO `images` (`product_id`, `url`) VALUES \n";
    echo implode(",\n", $values) . ";\n";
} else {
    echo "-- Không tìm thấy file ảnh nào đúng định dạng (ID_TenAnh.jpg) --\n";
    echo "-- Hãy chắc chắn bạn đã chạy script đổi tên trước đó --\n";
}

echo "\n--- KẾT THÚC ---\n";
?>