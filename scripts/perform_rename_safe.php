<?php
// scripts/perform_rename_safe.php

// 1. Đọc file preview
$jsonPath = __DIR__ . '/rename_preview.json';
if (!file_exists($jsonPath)) {
    die("Lỗi: Không tìm thấy file $jsonPath. Hãy chạy script tạo preview trước.\n");
}

$previewData = json_decode(file_get_contents($jsonPath), true);

if (!$previewData) {
    die("Lỗi: File JSON rỗng hoặc không hợp lệ.\n");
}

echo "--- BẮT ĐẦU ĐỔI TÊN AN TOÀN (SCORE >= 2) ---\n";

$countRenamed = 0;
$countSkipped = 0;
$errors = 0;

foreach ($previewData as $item) {
    // Lấy thông tin từ item (giả định cấu trúc JSON từ bước trước)
    // Cấu trúc mong đợi: {'old_path': '...', 'new_path': '...', 'score': N, ...}
    
    $oldPath = isset($item['old_path']) ? $item['old_path'] : null;
    $newPath = isset($item['new_path']) ? $item['new_path'] : null;
    $score   = isset($item['score']) ? (int)$item['score'] : 0;

    // --- LOGIC LỌC AN TOÀN (OPTION 2) ---
    if ($score < 2) {
        echo "[BỎ QUA] Score thấp ($score): " . basename($oldPath) . "\n";
        $countSkipped++;
        continue;
    }

    // Kiểm tra đường dẫn
    if (file_exists($oldPath)) {
        // Đảm bảo thư mục đích tồn tại
        $dir = dirname($newPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // Thực hiện đổi tên
        if (rename($oldPath, $newPath)) {
            echo "[OK] Đã đổi tên: " . basename($oldPath) . " -> " . basename($newPath) . " (Score: $score)\n";
            $countRenamed++;
        } else {
            echo "[LỖI] Không thể đổi tên file: " . basename($oldPath) . "\n";
            $errors++;
        }
    } else {
        echo "[LỖI] File gốc không tồn tại: $oldPath\n";
        $errors++;
    }
}

echo "---------------------------------------------------\n";
echo "TỔNG KẾT:\n";
echo "- Đã đổi tên thành công: $countRenamed file\n";
echo "- Đã bỏ qua (Score < 2): $countSkipped file\n";
echo "- Lỗi: $errors\n";
?>