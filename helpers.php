<?php
session_start();

require_once __DIR__ . '/config/database.php';

function db_connect() {
    $db = new Database();
    return $db->connect();
}

// Base path - auto detect or empty for root deployment
if (!defined('BASE_PATH')) {
    $script = $_SERVER['SCRIPT_NAME'] ?? ($_SERVER['PHP_SELF'] ?? '');
    $basePos = strpos($script, '/perfume_shop');
    if ($basePos !== false) {
        define('BASE_PATH', substr($script, 0, $basePos + strlen('/perfume_shop')));
    } else {
        // For hosting deployment (root level) or local without subdirectory
        define('BASE_PATH', '');
    }
}

function base_url($path = '') {
    $path = ltrim($path, '/');
    return rtrim(BASE_PATH, '/') . '/' . $path;
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . base_url('/public/login.php'));
        exit;
    }
}

function auth_user($user) {
    $_SESSION['user'] = $user;
}

function logout() {
    unset($_SESSION['user']);
}

function json_response($data, $status=200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function input($key, $default = null) {
    return isset($_REQUEST[$key]) ? trim($_REQUEST[$key]) : $default;
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf_token'];
}

function validate_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
