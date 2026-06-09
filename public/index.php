<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js)$/', $_SERVER["REQUEST_URI"])) {
    return false;
}

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($url === '/' || $url === '/home') {
    require_once '../src/Controllers/HomeController.php';
    
    $controller = new HomeController();
    $controller->index();
}