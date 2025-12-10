<?php
require_once __DIR__ . '/../helpers.php';
$db = db_connect();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // list products with min variant price and first image
    $stmt = $db->query("SELECT p.id, p.ten, p.mo_ta, MIN(v.gia) AS gia, (SELECT url FROM images WHERE product_id=p.id LIMIT 1) AS image FROM products p LEFT JOIN variants v ON v.product_id=p.id GROUP BY p.id ORDER BY p.id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    json_response(['data' => $products]);
}

if ($method === 'POST') {
    // create product + default variant
    $name = input('ten');
    $mo_ta = input('mo_ta');
    $thuong_hieu_id = input('thuong_hieu_id');
    $danh_muc_id = input('danh_muc_id');
    $gia = input('gia', 0);
    if (!$name) json_response(['error' => 'Missing required fields'], 422);
    $stmt = $db->prepare("INSERT INTO products (ten, mo_ta, thuong_hieu_id, danh_muc_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $mo_ta, $thuong_hieu_id, $danh_muc_id]);
    $productId = $db->lastInsertId();
    // add a default variant if price provided
    if ($gia) {
        $stmtv = $db->prepare("INSERT INTO variants (product_id, dung_tich, gia) VALUES (?, ?, ?)");
        $stmtv->execute([$productId, input('dung_tich', 50), $gia]);
    }
    json_response(['message' => 'Product created', 'id' => $productId], 201);
}

http_response_code(405);
json_response(['error' => 'Method not allowed'], 405);
