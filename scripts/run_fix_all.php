<?php
// scripts/run_fix_all.php

$dir = __DIR__ . '/../public/assets/uploads/';
$realDir = realpath($dir);

if (!$realDir) die("Lỗi: Không tìm thấy thư mục $dir\n");

echo "--- BẮT ĐẦU XỬ LÝ (BAO GỒM CẢ GC) ---\n";

// --- CẤU HÌNH ID (SỬA ID GC Ở DƯỚI) ---
$map = [
    'dior'   => 1,
    'chanel' => 5,
    'ysl'    => 11,
    'ver'    => 14,
    'gc'     => 99  // <--- QUAN TRỌNG: Đổi số 99 thành ID của Gucci trong bảng products của bạn
];

$files = scandir($realDir);
$sqlOutput = [];
$renamedCount = 0;

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;

    // 1. Nếu file đã đổi tên (VD: 1_Dior_1.jpg) -> Chỉ tạo SQL
    if (preg_match('/^(\d+)_/', $file, $matches)) {
        $pid = $matches[1];
        $sqlOutput[] = "INSERT INTO `images` (`product_id`, `file_name`) VALUES ($pid, '$file');";
        continue;
    }

    // 2. Nếu chưa đổi tên -> Tìm từ khóa để đổi
    $foundId = null;
    foreach ($map as $keyword => $id) {
        // stripos để không phân biệt hoa thường (GC hay gc đều được)
        if (stripos($file, $keyword) !== false) {
            $foundId = $id;
            break;
        }
    }

    if ($foundId) {
        $newName = $foundId . '_' . $file;
        $oldPath = $realDir . DIRECTORY_SEPARATOR . $file;
        $newPath = $realDir . DIRECTORY_SEPARATOR . $newName;

        if (rename($oldPath, $newPath)) {
            echo "[ĐÃ ĐỔI TÊN] $file -> $newName\n";
            $sqlOutput[] = "INSERT INTO `images` (`product_id`, `file_name`) VALUES ($foundId, '$newName');";
            $renamedCount++;
        } else {
            echo "[LỖI] Không đổi tên được file $file (Check permission?)\n";
        }
    } else {
        echo "[BỎ QUA] Không nhận diện được file: $file\n";
    }
}

echo "\n--------------------------------------------------\n";
echo "COPY DÒNG DƯỚI VÀO PHPMYADMIN:\n\n";
// Loại bỏ trùng lặp câu SQL nếu chạy nhiều lần
$sqlOutput = array_unique($sqlOutput);
foreach ($sqlOutput as $sql) {
    echo $sql . "\n";
}
echo "\n--------------------------------------------------\n";
?>