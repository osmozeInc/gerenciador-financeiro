<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Pagamento.php';

class PagamentoController extends Controller {
    
    public function salvar() {
        header('Content-Type: application/json');

        $nome = trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS));

        if (empty($nome)) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha todos os campos obrigatórios.']);
            return;
        }

        try {
            $pagamentoModel = new Pagamento();
            
            $pagamentoModel->insert($nome); 

            echo json_encode(['sucesso' => true]);
            
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao salvar na base de dados.']);
        }
    }
}