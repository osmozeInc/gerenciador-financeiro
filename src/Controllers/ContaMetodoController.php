<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/ContaMetodo.php';

class ContaMetodoController extends Controller {
    
    public function salvar() {
        header('Content-Type: application/json');

        $nome = trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

        if (empty($nome)) {
            echo json_encode(['sucesso' => false, 'msgTipo' => 'warning', 'mensagem' => 'Preencha todos os campos obrigatórios!']);
            exit;
        }

        try {
            $contaMetodoModel = new ContaMetodo();
            $contaMetodoModel->insert($nome); 

            echo json_encode(['sucesso' => true, 'msgTipo' => 'success', 'mensagem' => 'Tipo de conta salvo com sucesso!']);
            
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'msgTipo' => 'error', 'mensagem' => 'Erro interno ao salvar na base de dados.']);
        }
        
        exit;
    }
}