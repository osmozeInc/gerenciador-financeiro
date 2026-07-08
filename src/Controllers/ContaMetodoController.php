<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/ContaMetodo.php';

class ContaMetodoController extends Controller {

    public function selectDados() {
        header('Content-Type: application/json');

        try {
            $contaMetodoModel = new ContaMetodo();
            $contaMetodo = $contaMetodoModel->selectAllContas($this->idUsuarioLogado);
            
            echo json_encode([
                'resposta' => $this->mensagensModel['silenciosas']['selecionar_dados']['busca_com_sucesso'],
                'metodos' => $contaMetodo
            ]);
        
        } catch (Exception $e) {
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
            $contaMetodoModel = new ContaMetodo();
            $contaMetodoModel->insert($nome); 

            echo json_encode(['resposta' => $this->mensagensModel['contaMetodo']['salvar']['salvo_com_sucesso']]);
            
        } catch (Exception $e) {
            echo json_encode(['resposta' => $this->mensagensModel['silenciosas']['selecionar_dados']['erro_interno']]);
        }
        
        exit;
    }
}