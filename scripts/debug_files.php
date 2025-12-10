<?php
// scripts/debug_files.php

// Đường dẫn cần kiểm tra
$path = __DIR__ . '/../public/assets/uploads/';
$realPath = realpath($path);

echo "--- THÔNG TIN DEBUG ---\n";
echo "Đường dẫn script: " . __DIR__ . "\n";
echo "Đường dẫn thư mục ảnh (tương đối): $path\n";
echo "Đường dẫn thư mục ảnh (thực tế): " . ($realPath ? $realPath : "KHÔNG TỒN TẠI") . "\n";

if ($realPath && is_dir($realPath)) {
    $files = scandir($realPath);
    echo "Số lượng file tìm thấy: " . (count($files) - 2) . "\n"; // Trừ . và ..
    
    echo "\n--- DANH SÁCH 10 FILE ĐẦU TIÊN ---\n";
    $count = 0;
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        // Kiểm tra xem có khớp mẫu số_tên hay không
        $isMatch = preg_match('/^(\d+)_(.+\.(jpg|jpeg|png|gif))$/i', $file) ? "[OK - Khớp]" : "[KHÔNG Khớp]";
        
        echo "$file  => $isMatch\n";
        
        $count++;
        if ($count >= 10) break; 
    }
} else {
    echo "LỖI: Thư mục không tồn tại hoặc không đọc được.\n";
}
echo "-----------------------\n";
?>