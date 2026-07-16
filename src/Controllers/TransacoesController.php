<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Categoria.php';
require_once __DIR__ . '/../Models/ContaMetodo.php';
require_once __DIR__ . '/../Models/Transacao.php';

class TransacoesController extends Controller {
    
    public function index() {
        $this->render('transacoes');
    }
    
    public function selectDadosTransacoes() {
        header('Content-Type: application/json');

        try {
            $transacaoModel = new Transacao();
            $transacoes = $transacaoModel->selectAllTransacoes($this->idUsuarioLogado);

            echo json_encode([
                'resposta'   => $this->mensagensModel['silenciosas']['selecionar_dados']['busca_com_sucesso'],
                'transacoes' => $transacoes
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

    public function selectDados100Transacoes() {
        header('Content-Type: application/json');

        try {
            $transacaoModel = new Transacao();
            $transacoes = $transacaoModel->select100Transacoes($this->idUsuarioLogado);

            echo json_encode([
                'resposta'   => $this->mensagensModel['silenciosas']['selecionar_dados']['busca_com_sucesso'],
                'transacoes' => $transacoes
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

    public function salvarReceita() {
        header('Content-Type: application/json');

        $descricao    = trim(filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $categoria_id = trim(filter_input(INPUT_POST, 'categoria_id', FILTER_SANITIZE_NUMBER_INT) ?? '');
        $conta_id     = trim(filter_input(INPUT_POST, 'conta_id', FILTER_SANITIZE_NUMBER_INT) ?? '');
        $valor        = trim(filter_input(INPUT_POST, 'valor', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? '');
        $data         = trim(filter_input(INPUT_POST, 'data', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

        if (empty($descricao) || empty($categoria_id) || empty($conta_id) || empty($valor) || empty($data)) {
            echo json_encode([
                'resposta' => $mensagensModel['genericas']['formulario_incompleto']
            ]);
            exit;
        }

        $dados = [
            'categoria_id' => $categoria_id,
            'conta_id'     => $conta_id,
            'descricao'    => $descricao,
            'valor'        => $valor,
            'data'         => $data,
            'tenant_id'    => $this->idUsuarioLogado
        ];

        try {
            $transacaoModel = new Transacao();
            $transacaoModel->salvarTransacaoReceita($dados);

            echo json_encode(['resposta' => $this->mensagensModel['transacao']['salvar']['salvo_com_sucesso']]);
            
        } catch (Exception $e) {
            echo json_encode(['resposta' => $this->mensagensModel['genericas']['erro_interno'] ]);
        }
        exit;
    }

    public function salvarDespesa() {
        header('Content-Type: application/json');

        $descricao    = trim(filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $categoria_id = trim(filter_input(INPUT_POST, 'categoria_id', FILTER_SANITIZE_NUMBER_INT) ?? '');
        $conta_id     = trim(filter_input(INPUT_POST, 'conta_id', FILTER_SANITIZE_NUMBER_INT) ?? '');
        $valor        = trim(filter_input(INPUT_POST, 'valor', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? '');
        $data         = trim(filter_input(INPUT_POST, 'data', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

        $parcelado    = filter_input(INPUT_POST, 'parcelado', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $qtd_parcelas = trim(filter_input(INPUT_POST, 'qtd_parcelas', FILTER_SANITIZE_NUMBER_INT) ?? 1);

        if (empty($descricao) || empty($categoria_id) || empty($conta_id) || empty($valor) || empty($data) || is_null($parcelado)) {
            echo json_encode([
                'resposta' => $this->mensagensModel['genericas']['formulario_incompleto'],
            ]);
            exit;
        }

        $dadosTransacao = [
            'categoria_id' => $categoria_id,
            'conta_id'     => $conta_id,
            'descricao'    => $descricao,
            'valor'        => $valor,
            'data'         => $data,
            'tenant_id'    => $this->idUsuarioLogado
        ];

        if (!$parcelado) $qtd_parcelas = null;

        $dadosDespesa = [
            'parcelado'    => $parcelado ? 1 : 0,
            'qtd_parcelas' => $qtd_parcelas
        ];

        try {
            $transacaoModel = new Transacao();
            $transacaoModel->salvarTransacaoDespesa($dadosTransacao, $dadosDespesa);

            echo json_encode(['resposta' => $this->mensagensModel['transacao']['salvar']['salvo_com_sucesso']]);
            
        } catch (Exception $e) {
            echo json_encode([
                'resposta' => $this->mensagensModel['genericas']['erro_interno'],
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
}