<?php
require_once __DIR__ . '/../helpers.php';
$db = db_connect();

$dir = __DIR__ . '/../public/assets/uploads';
$baseUrl = base_url('/public/assets/uploads/');

if (!is_dir($dir)) {
    echo "Directory not found: $dir\n";
    exit(1);
}

$files = scandir($dir);
$inserted = 0;
$skipped = 0;

foreach ($files as $f) {
    if ($f === '.' || $f === '..') continue;
    if (!preg_match('/\.(jpe?g|png|gif|webp|svg)$/i', $f)) continue;

    // get product id from filename like 123.jpg or 123_any.jpg
    if (!preg_match('/^(\d+)(?:[_-].*)?\./', $f, $m)) {
        echo "Bỏ qua $f (không tìm được product_id)\n";
        $skipped++;
        continue;
    }
    $product_id = intval($m[1]);
    $url = rtrim($baseUrl, '/') . '/' . $f;

    // check product exists
    $stmt = $db->prepare('SELECT id FROM products WHERE id = ?');
    $stmt->execute([$product_id]);
    if (!$stmt->fetch()) {
        echo "Product $product_id không tồn tại — bỏ qua $f\n";
        $skipped++;
        continue;
    }

    // check already exists
    $stmt = $db->prepare('SELECT id FROM images WHERE product_id = ? AND url = ?');
    $stmt->execute([$product_id, $url]);
    if ($stmt->fetch()) {
        echo "Đã tồn tại record cho $f\n";
        $skipped++;
        continue;
    }

    // insert
    $stmt = $db->prepare('INSERT INTO images (product_id, url) VALUES (?, ?)');
    try {
        $stmt->execute([$product_id, $url]);
        echo "Đã gán $f cho product $product_id\n";
        $inserted++;
    } catch (Exception $e) {
        echo "Lỗi khi insert $f: " . $e->getMessage() . "\n";
        $skipped++;
    }
}

echo "\nHoàn thành. Inserted: $inserted, Skipped: $skipped\n";

