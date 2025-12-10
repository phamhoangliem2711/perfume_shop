<?php
require_once __DIR__ . '/../helpers.php';
logout();
header('Location: ' . base_url('/public/login.php'));
exit;
