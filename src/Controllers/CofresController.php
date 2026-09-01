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

    public function selectCofre($id = null) {
        header('Content-Type: application/json');

        if ($id === null || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode([ 'resposta' => $this->mensagensModel['transacao']['deletar']['id_invalido'] ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode([ 'resposta' => $this->mensagensModel['transacao']['deletar']['metodo_invalido'] ]);
            return;
        }

        try {
            $cofreModel = new Cofre();
            $cofre = $cofreModel->selectCofrePorId($id, $this->idUsuarioLogado);
            
            if ($cofre) {
                echo json_encode([
                    'resposta' => $this->mensagensModel['cofre']['buscar']['busca_com_sucesso'],
                    'cofre' => $cofre
                ]);
            } else {
                echo json_encode([
                    'resposta' => $this->mensagensModel['cofre']['buscar']['busca_vazia']
                ]);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'resposta' => $this->mensagensModel['genericas']['erro_interno'],
                'detalhes' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function salvarCofre() {
        header('Content-Type: application/json');

        $nome = trim(filter_input(INPUT_POST, 'nomeCofre', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $descricao = trim(filter_input(INPUT_POST, 'descricaoCofre', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $meta = trim(filter_input(INPUT_POST, 'metaCofre', FILTER_SANITIZE_NUMBER_INT) ?? '');
        $local = trim(filter_input(INPUT_POST, 'localCofre', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

        $dados = [
            'nome' => $nome,
            'descricao' => $descricao,
            'local' => $local,
            'valor_meta' => $meta,
            'data_criacao' => date('Y-m-d'),
            'tenant_id' => $this->idUsuarioLogado
        ];

        try {
            $cofreModel = new Cofre();
            $cofreModel->salvarCofre($dados);

            echo json_encode(['resposta' => $this->mensagensModel['cofre']['salvar']['salvo_com_sucesso']]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'resposta' => $this->mensagensModel['cofre']['salvar']['erro_interno'],
                'detalhes' => $e->getMessage()
            ]);
        }
        exit;
    }
}