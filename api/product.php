<?php
require_once __DIR__ . '/../helpers.php';
$db = db_connect();

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($method === 'GET') {
    if (!$id) json_response(['error' => 'Missing id'], 422);
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) json_response(['error' => 'Product not found'], 404);
    // variants
    $stmt = $db->prepare("SELECT * FROM variants WHERE product_id = ? ORDER BY gia ASC");
    $stmt->execute([$id]);
    $variants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // images
    $stmt = $db->prepare("SELECT url FROM images WHERE product_id = ?");
    $stmt->execute([$id]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
    json_response(['data' => ['product' => $product, 'variants' => $variants, 'images' => $images]]);
}

if ($method === 'PUT' || $method === 'POST') {
    parse_str(file_get_contents('php://input'), $data);
    $name = $data['ten'] ?? null;
    $mo_ta = $data['mo_ta'] ?? null;
    $thuong_hieu_id = $data['thuong_hieu_id'] ?? null;
    $danh_muc_id = $data['danh_muc_id'] ?? null;
    if ($id) {
        $stmt = $db->prepare("UPDATE products SET ten = ?, mo_ta = ?, thuong_hieu_id = ?, danh_muc_id = ? WHERE id = ?");
        $stmt->execute([$name, $mo_ta, $thuong_hieu_id, $danh_muc_id, $id]);
        json_response(['message' => 'Product updated']);
    } else {
        $stmt = $db->prepare("INSERT INTO products (ten, mo_ta, thuong_hieu_id, danh_muc_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $mo_ta, $thuong_hieu_id, $danh_muc_id]);
        json_response(['message' => 'Product created', 'id' => $db->lastInsertId()], 201);
    }
}

if ($method === 'DELETE') {
    if (!$id) json_response(['error' => 'Missing id'], 422);
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    json_response(['message' => 'Product deleted']);
}

http_response_code(405);
json_response(['error' => 'Method not allowed'], 405);
