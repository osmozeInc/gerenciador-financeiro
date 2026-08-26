<?php
require_once 'Controller.php';
require_once __DIR__ . '/../Models/Investimento.php';


class InvestimentosController extends Controller {
    
    public function index() {
        $this->render('investimentos');
    }
    
    public function selectDadosAllInvestimentos() {
        header('Content-Type: application/json');

        try {
            $investimentoModel = new Investimento();
            $investimentos = $investimentoModel->selectAllInvestimentos($this->idUsuarioLogado);

            echo json_encode([
                'resposta'   => $this->mensagensModel['silenciosas']['selecionar_dados']['busca_com_sucesso'],
                'investimentos' => $investimentos
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
    
    public function selectDadosPorClasse() {
        header('Content-Type: application/json');

        try {
            $investimentoModel = new Investimento();
            $investimentos = $investimentoModel->selectPorClasse($this->idUsuarioLogado);

            echo json_encode([
                'resposta'   => $this->mensagensModel['silenciosas']['selecionar_dados']['busca_com_sucesso'],
                'investimentos' => $investimentos
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


    public function salvarInvestimento() {
        header('Content-Type: application/json');

        $descricao    = trim(filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $conta_id     = trim(filter_input(INPUT_POST, 'conta_id', FILTER_SANITIZE_NUMBER_INT) ?? '');
        $data         = trim(filter_input(INPUT_POST, 'data', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

        $ativo        = trim(filter_input(INPUT_POST, 'ativo', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $classe_id    = trim(filter_input(INPUT_POST, 'classe', FILTER_SANITIZE_NUMBER_INT) ?? '');
        $quantidade   = trim(filter_input(INPUT_POST, 'quantidade', FILTER_SANITIZE_NUMBER_INT) ?? '');
        $preco        = trim(filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? '');

        if (empty($descricao) || empty($conta_id) || empty($data) || empty($ativo) || empty($classe_id) || empty($quantidade) || empty($preco)) {
            echo json_encode([
                'resposta' => $this->mensagensModel['genericas']['formulario_incompleto'],
            ]);
            exit;
        }

        $categoriaModel = new Categoria();
        $categoria_id = $categoriaModel->getIdCategoriaInvestimento();

        $dadosTransacao = [
            'categoria_id' => $categoria_id,
            'conta_id'     => $conta_id,
            'descricao'    => $descricao,
            'valor'        => $preco * $quantidade,
            'data'         => $data,
            'tenant_id'    => $this->idUsuarioLogado
        ];

        $dadosInvestimento = [
            'ativo'        => $ativo,
            'classe_id'    => $classe_id,
            'quantidade'   => $quantidade,
            'preco'        => $preco
        ];

        try {
            $transacaoModel = new Transacao();
            $transacaoModel->salvarTransacaoInvestimento($dadosTransacao, $dadosInvestimento);

            echo json_encode(['resposta' => $this->mensagensModel['transacao']['salvar']['salvo_com_sucesso']]);
            
        } catch (Exception $e) {
            echo json_encode([
                'resposta' => $this->mensagensModel['genericas']['erro_interno'],
                'detalhes' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function salvarNoCofre() {
        header('Content-Type: application/json');

        $descricao    = trim(filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $conta_id     = trim(filter_input(INPUT_POST, 'conta_id', FILTER_SANITIZE_NUMBER_INT) ?? '');
        $valor        = trim(filter_input(INPUT_POST, 'valor', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? '');
        $data         = trim(filter_input(INPUT_POST, 'data', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

        $cofre        = trim(filter_input(INPUT_POST, 'id_cofre', FILTER_SANITIZE_NUMBER_INT) ?? '');

        if (empty($descricao) || empty($conta_id) || empty($valor) || empty($data) || empty($cofre)) {
            echo json_encode([
                'resposta' => $this->mensagensModel['genericas']['formulario_incompleto'],
            ]);
            exit;
        }

        $categoriaModel = new Categoria();
        $categoria_id = $categoriaModel->getIdCategoriaCofre();

        $dadosTransacao = [
            'categoria_id' => $categoria_id,
            'conta_id'     => $conta_id,
            'descricao'    => $descricao,
            'valor'        => $valor,
            'data'         => $data,
            'tenant_id'    => $this->idUsuarioLogado
        ];

        $dadosCofre = [
            'cofre_id' => $cofre
        ];

        try {
            $transacaoModel = new Transacao();
            $transacaoModel->salvarTransacaoCofre($dadosTransacao, $dadosCofre);

            echo json_encode(['resposta' => $this->mensagensModel['transacao']['salvar']['salvo_com_sucesso']]);
            
        } catch (Exception $e) {
            echo json_encode([
                'resposta' => $this->mensagensModel['genericas']['erro_interno'],
                'detalhes' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function buscar($id = null) {
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
            $transacaoModel = new Transacao();
            $transacao = $transacaoModel->buscarTransacao($id, $this->idUsuarioLogado);

            echo json_encode([ 
                'resposta' => $this->mensagensModel['transacao']['buscar']['busca_com_sucesso'],
                'transacao' => $transacao
                ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([ 
                'resposta' => $this->mensagensModel['transacao']['buscar']['erro_interno'],
                'detalhes' => $e->getMessage()
                ]);
        }
    }

    public function deletar($id = null) {
        header('Content-Type: application/json');

        if ($id === null || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode([ 'resposta' => $this->mensagensModel['transacao']['deletar']['id_invalido'] ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode([ 'resposta' => $this->mensagensModel['transacao']['deletar']['metodo_invalido'] ]);
            return;
        }

        try {
            $transacaoModel = new Transacao();
            $transacaoModel->deletarTransacao($id, $this->idUsuarioLogado);

            echo json_encode([ 'resposta' => $this->mensagensModel['transacao']['deletar']['deletado_com_sucesso'] ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([ 'resposta' => $this->mensagensModel['transacao']['deletar']['erro_ao_deletar'] ]);
        }
    }
}