<?php
ob_start();
require_once __DIR__ . '/../helpers.php';
$db = db_connect();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    json_response(['error' => 'Method not allowed'], 405);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!$payload) {
    http_response_code(400);
    json_response(['error' => 'Invalid payload'], 400);
    exit;
}

$user_id = intval($payload['user_id'] ?? 0);
$name = trim($payload['name'] ?? '');
$email = trim($payload['email'] ?? '');
$address = trim($payload['address'] ?? '');
$items = $payload['items'] ?? [];

if (!$user_id || empty($items)) {
    http_response_code(422);
    json_response(['error' => 'Missing data: user_id and items are required'], 422);
    exit;
}

try {
    $db->beginTransaction();
    $total = 0;
    foreach ($items as $itm) {
        $total += ($itm['price'] * $itm['quantity']);
    }
    $stmt = $db->prepare("INSERT INTO orders (user_id, tong_tien, trang_thai, ngay_dat) VALUES (?, ?, 'cho_duyet', NOW())");
    $stmt->execute([$user_id, $total]);

    $order_id = $db->lastInsertId();
    $stmt_item = $db->prepare("INSERT INTO order_items (order_id, variant_id, so_luong, don_gia) VALUES (?, ?, ?, ?)");
    foreach ($items as $itm) {
        $stmt_item->execute([$order_id, $itm['variant_id'], $itm['quantity'], $itm['price']]);
    }
    $db->commit();

    http_response_code(201);
    json_response(['message' => 'Order created', 'order_id' => $order_id], 201);
} catch (Exception $e) {
    $db->rollBack();
    http_response_code(500);
    json_response(['error' => $e->getMessage()], 500);
}
ob_end_flush();