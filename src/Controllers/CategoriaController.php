<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Categoria.php';

class CategoriaController extends Controller {
    
    public function salvar() {
        header('Content-Type: application/json');

        $nome = trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS));
        $tipo = trim(filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS));

        if (empty($nome) || empty($tipo)) {
            echo json_encode(['sucesso' => false, 'msgTipo' => 'warning', 'mensagem' => 'Preencha todos os campos obrigatórios!']);
            return;
        }

        $tiposPermitidos = ['R', 'D', 'I', 'C'];
        if (!in_array($tipo, $tiposPermitidos)) {
            echo json_encode(['sucesso' => false, 'msgTipo' => 'warning', 'mensagem' => 'Tipo de categoria inválido!']);
            return;
        }

        try {
            $categoriaModel = new Categoria();
            
            $categoriaModel->insert($nome, $tipo);

            echo json_encode(['sucesso' => true, 'msgTipo' => 'success', 'mensagem' => 'Categoria cadastrada com sucesso!']);
            
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'msgTipo' => 'error', 'mensagem' => 'Erro interno ao salvar na base de dados.']);
        }
    }
}