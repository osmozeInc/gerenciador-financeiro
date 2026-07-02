<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/ClasseInvestimento.php';

class ClassesInvestimentoController extends Controller {
    
    public function selectDados() {
        header('Content-Type: application/json');

        $classesModel = new ClasseInvestimento();

        $classes = $classesModel->selectAllClasses();

        echo json_encode([
            'classes' => $classes
        ]);
        exit;
    }
        public function salvar() {
        header('Content-Type: application/json');

        $nome = trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

        if (empty($nome)) {
            echo json_encode(['sucesso' => false, 'msgTipo' => 'warning', 'mensagem' => 'Preencha todos os campos obrigatórios!']);
            exit;
        }

        try {
            $classeModel = new ClasseInvestimento();
            $classeModel->insert($nome);

            echo json_encode(['sucesso' => true, 'msgTipo' => 'success', 'mensagem' => 'Tipo de conta salvo com sucesso!']);
            
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'msgTipo' => 'error', 'mensagem' => 'Erro interno ao salvar na base de dados.']);
        }
        
        exit;
    }
}