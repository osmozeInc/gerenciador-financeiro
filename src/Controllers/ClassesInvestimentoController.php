<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/ClasseInvestimento.php';

class ClassesInvestimentoController extends Controller {
    
public function selectDados() {
    header('Content-Type: application/json');

    try {
        $classesModel = new ClasseInvestimento();
        $classes = $classesModel->selectAllClasses();

        echo json_encode([
            'resposta' => $this->mensagensModel['silenciosas']['selecionar_dados']['busca_com_sucesso'],
            'classes' => $classes
        ]);

    } catch (Exception $e) {
        http_response_code(500); 
        
        echo json_encode([
            'resposta' => $this->mensagensModel['silenciosas']['selecionar_dados']['erro_interno'],
            'detalhes' => $e->getMessage()
        ]);
    }
    
    exit;
}

    public function salvar() {
        header('Content-Type: application/json');

        $nome = trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

        if (empty($nome)) {
            echo json_encode(['resposta' => $this->mensagensModel['genericas']['formulario_incompleto']]);
            exit;
        }

        try {
            $classeModel = new ClasseInvestimento();
            $classeModel->insert($nome);

            echo json_encode(['resposta' => $this->mensagensModel['classesInvestimento']['salvar']['salvo_com_sucesso']]);
            
        } catch (Exception $e) {
            echo json_encode(['resposta' => $this->mensagensModel['genericas']['erro_interno']]);
        }
        
        exit;
    }
}