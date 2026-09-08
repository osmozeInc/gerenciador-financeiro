<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Cofre.php';
require_once __DIR__ . '/../Models/Categoria.php';
require_once __DIR__ . '/../Models/Transacao.php';

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

    public function excluirCofre($id = null) {
        header('Content-Type: application/json');

        if ($id === null || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode([ 'resposta' => $this->mensagensModel['cofre']['buscar']['busca_vazia'] ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode([ 'resposta' => $this->mensagensModel['cofre']['deletar']['metodo_invalido'] ]);
            return;
        }

        try {
            $cofreModel = new Cofre();
            $cofre = $cofreModel->selectCofrePorId($id, $this->idUsuarioLogado);

            if (!$cofre) {
                http_response_code(404);
                echo json_encode([ 'resposta' => $this->mensagensModel['cofre']['deletar']['id_invalido'] ]);
                return;
            }

            if ($cofre['valor_total'] == 0) {
                $cofreModel->alterarStatusPorId($id, "cancelado", $this->idUsuarioLogado);
            }
            else if ($cofre['valor_total'] > 0) {
                $cofreModel->alterarStatusPorId($id, "cancelado", $this->idUsuarioLogado);

                $categoriaModel = new Categoria();
                $categoria = $categoriaModel->getIdCategoriaCofre($this->idUsuarioLogado);

                $dados = [
                    'id_categoria' => $categoria,
                    'id_conta_metodo' => 1,
                    'descricao' => "Resgate do cofre: " . $cofre['nome'],
                    'data_transacao' => date('Y-m-d'),
                    'valor_total' => $cofre['valor_total'],
                    'tenant_id' => $this->idUsuarioLogado
                ];

                $transacaoModel = new Transacao();
                $transacaoModel->salvarTransacaoReceita($dados);
            }

            echo json_encode(['resposta' => $this->mensagensModel['cofre']['deletar']['deletado_com_sucesso']]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'resposta' => $this->mensagensModel['cofre']['deletar']['erro_interno'],
                'detalhes' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function resgatarSaldo($id = null) {
        header('Content-Type: application/json');

        if ($id === null || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode([ 'resposta' => $this->mensagensModel['cofre']['buscar']['busca_vazia'] ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode([ 'resposta' => $this->mensagensModel['cofre']['deletar']['metodo_invalido'] ]);
            return;
        }

        try {
            $cofreModel = new Cofre();
            $cofre = $cofreModel->selectCofrePorId($id, $this->idUsuarioLogado);

            if (!$cofre) {
                http_response_code(404);
                echo json_encode([ 'resposta' => $this->mensagensModel['cofre']['deletar']['id_invalido'] ]);
                return;
            }
            else {
                if ($cofre['valor_total'] == 0) {
                    $cofreModel->excluirCofre($id, $this->idUsuarioLogado);
                    echo json_encode(['resposta' => $this->mensagensModel['cofre']['deletar']['deletado_com_sucesso']]);
                }
                else if ($cofre['valor_total'] > 0) {
                    $cofreModel->alterarStatusPorId($id, "cancelado", $this->idUsuarioLogado);

                    $categoriaModel = new Categoria();
                    $categoria = $categoriaModel->getIdCategoriaCofre($this->idUsuarioLogado);

                    $dados = [
                        'id_categoria' => $categoria,
                        'id_conta_metodo' => 1,
                        'descricao' => "Resgate do cofre: " . $cofre['nome'],
                        'data_transacao' => date('Y-m-d'),
                        'valor_total' => $cofre['valor_total'],
                        'tenant_id' => $this->idUsuarioLogado
                    ];

                    $transacaoModel = new Transacao();
                    $transacaoModel->salvarTransacaoReceita($dados);

                    echo json_encode(['resposta' => $this->mensagensModel['cofre']['deletar']['deletado_com_sucesso']]);
                }
            }

            echo json_encode(['resposta' => $this->mensagensModel['cofre']['deletar']['deletado_com_sucesso']]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'resposta' => $this->mensagensModel['cofre']['deletar']['erro_interno'],
                'detalhes' => $e->getMessage()
            ]);
        }
        exit;
    }
}