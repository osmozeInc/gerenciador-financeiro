<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js)$/', $_SERVER["REQUEST_URI"])) {
    return false;
}

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Rota padrão
if ($url === '/') 
    $url = '/home';

if ($url === '/' || $url === '/home') {
    require_once '../src/Controllers/HomeController.php';
    
    $controller = new HomeController();
    $controller->index();
} elseif ($url === '/transacoes') {
    require_once '../src/Controllers/TransacoesController.php';

    $controller = new TransacoesController();
    $controller->index();
} else {
    echo '404';
}