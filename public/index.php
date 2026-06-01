<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($url === '/' || $url === '/home') {
    require_once '../src/Controllers/HomeController.php';
    
    $controller = new HomeController();
    $controller->index();

} elseif ($url === '/receitas') {
    echo "<p>Aqui criaremos o ReceitaController depois.</p>";
} else {
    echo "<h1>Erro 404</h1><p>Página não encontrada.</p>";
}