<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Cofre.php';

class CofresController extends Controller {

    public function index() {
        $this->render('cofres');
    }
    
    public function selectNomesCofres() {
        header('Content-Type: application/json');

        try {
            $cofreModel = new Cofre();
            $cofres = $cofreModel->selectNomesCofres($this->idUsuarioLogado);
            
            echo json_encode([
                'resposta' => $this->mensagensModel['silenciosas']['selecionar_dados']['busca_com_sucesso'],
                'cofres' => $cofres,
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

    public function selectDados() {
        header('Content-Type: application/json');

        try {
            $cofreModel = new Cofre();
            $cofres = $cofreModel->selectAllCofres($this->idUsuarioLogado);
            
            echo json_encode([
                'resposta' => $this->mensagensModel['silenciosas']['selecionar_dados']['busca_com_sucesso'],
                'cofres' => $cofres,
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
}