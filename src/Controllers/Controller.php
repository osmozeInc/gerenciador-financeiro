<?php

class Controller {
    protected function render($view, $dados = []) {
        extract($dados);

        $viewPath = __DIR__ . "/../Views/pages/{$view}.php";
        $layoutPath = __DIR__ . '/../Views/layout.php';

        if (!file_exists($viewPath)) {
            die("Erro de Arquitetura: A View '{$view}' não foi encontrada no sistema.");
        }

        if (file_exists($layoutPath)) {
            require_once $layoutPath;
        } else {
            die("Erro de Arquitetura: Arquivo de layout principal não encontrado.");
        }
    }
}