<?php
require_once __DIR__ . '/../helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    json_response(['error' => 'Method not allowed'], 405);
}

if (empty($_FILES['file'])) json_response(['error' => 'No file uploaded'], 400);

$uploadDir = __DIR__ . '/../public/assets/uploads';
if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

$file = $_FILES['file'];
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$allow = ['jpg','jpeg','png','gif'];
if (!in_array(strtolower($ext), $allow)) json_response(['error' => 'Invalid file type'], 422);

$filename = uniqid('img_', true) . '.' . $ext;
$target = $uploadDir . '/' . $filename;
if (move_uploaded_file($file['tmp_name'], $target)) {
    $url = base_url('/public/assets/uploads/' . $filename);
    json_response(['url' => $url]);
}

json_response(['error' => 'Upload failed'], 500);
