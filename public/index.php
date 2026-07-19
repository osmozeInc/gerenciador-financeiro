<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (preg_match('/\.(?:png|jpg|jpeg|gif|webp|css|js|ico|svg|woff|woff2|ttf|eot)$/i', $_SERVER["REQUEST_URI"])) {
    return false;
}

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($url === '/') {
    $url = '/home';
}

$partesUrl = explode('/', trim($url, '/'));

$rotaBase = $partesUrl[0]; 

$metodoAcao = isset($partesUrl[1]) ? $partesUrl[1] : 'index'; 

$nomeController = ucfirst($rotaBase) . 'Controller'; 
$caminhoArquivo = '../src/Controllers/' . $nomeController . '.php';

if (file_exists($caminhoArquivo)) {
    require_once $caminhoArquivo;
    $controller = new $nomeController(); 
    
    if (method_exists($controller, $metodoAcao)) {
        
        $parametro = isset($partesUrl[2]) ? $partesUrl[2] : null;

        $controller->$metodoAcao($parametro);
        
    } else {
        chamarPagina404();
    }
} else {
    chamarPagina404();
}

function chamarPagina404() {
    require_once '../src/Controllers/NotFoundedController.php';
    $controller = new NotFoundedController(); 
    $controller->index();
}