<?php
// Exibe erros na tela (Essencial para o ambiente de desenvolvimento local)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Captura apenas o caminho da URL (ignorando parâmetros como ?id=1)
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

echo "<h1>Gerenciador Financeiro - Front Controller</h1>";
echo "<p>Você acessou a rota: <strong>{$url}</strong></p>";

// Lógica inicial de roteamento
if ($url === '/' || $url === '/home') {
    echo "<p>Neste ponto, chamaremos o <strong>HomeController</strong>.</p>";
} elseif ($url === '/receitas') {
    echo "<p>Neste ponto, chamaremos o <strong>ReceitaController</strong>.</p>";
} else {
    echo "<p>Erro 404: Rota não encontrada.</p>";
}