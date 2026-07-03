<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Categoria.php';
require_once __DIR__ . '/../Models/ContaMetodo.php';
require_once __DIR__ . '/../Models/Transacao.php';

class TransacoesController extends Controller {
    
    public function index() {
        $this->render('transacoes');
    }
    
    public function selectDados() {
        header('Content-Type: application/json');

        try {
            $categoriaModel = new Categoria();
            $contaModel = new ContaMetodo();
            $transacaoModel = new Transacao();

            $categorias = $categoriaModel->selectAllCategorias();
            $contas = $contaModel->selectAllContas();
            $transacoes = $transacaoModel->selectAllTransacoes();

            $categoriasAgrupadas = ['R' => [], 'D' => [],  'I' => [], 'C' => []];

            foreach ($categorias as $categoria) {
                $categoriasAgrupadas[$categoria['tipo']][] = $categoria;
            }

            echo json_encode([
                'resposta'   => $this->mensagensModel['silenciosas']['selecionar_dados']['busca_com_sucesso'],
                'categorias' => $categoriasAgrupadas,
                'contas'     => $contas,
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
    
    public function selectTransacoes() {
        header('Content-Type: application/json');

        try {
            $transacaoModel = new Transacao();

            $transacoes = $transacaoModel->selectAllTransacoes();


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

    public function salvar() {
        header('Content-Type: application/json');

        $descricao    = trim(filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $categoria_id = trim(filter_input(INPUT_POST, 'categoria_id', FILTER_SANITIZE_NUMBER_INT) ?? '');
        $conta_id     = trim(filter_input(INPUT_POST, 'conta_id', FILTER_SANITIZE_NUMBER_INT) ?? '');
        $valor        = trim(filter_input(INPUT_POST, 'valor', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? '');
        $data         = trim(filter_input(INPUT_POST, 'data', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        
        // Esta variável é o maestro da requisição. O JS precisa enviá-la no FormData.
        $tipo         = trim(filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS) ?? ''); 

        // Validação Base
        if (empty($descricao) || empty($categoria_id) || empty($valor) || empty($data) || empty($tipo)) {
            echo json_encode([
                'resposta' => $mensagensModel['genericas']['formulario_incompleto']
            ]);
            exit;
        }

        $dadosPai = [
            'categoria_id' => $categoria_id,
            'conta_id'     => !empty($conta_id) ? $conta_id : null, // Conta pode ser nula para cofres
            'descricao'    => $descricao,
            'valor'        => $valor,
            'data'         => $data
        ];

        $dadosFilha = [];

        // 2. A Triagem (Captura e Validação Condicional)
        if ($tipo === 'D') {
            $parcelado = filter_input(INPUT_POST, 'parcelado', FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            $qtd_parcelas = trim(filter_input(INPUT_POST, 'qtd_parcelas', FILTER_SANITIZE_NUMBER_INT) ?? 1);

            $dadosFilha = [
                'parcelado'    => $parcelado,
                'qtd_parcelas' => $qtd_parcelas
            ];
        } 
        elseif ($tipo === 'I') {
            $ativo      = trim(filter_input(INPUT_POST, 'ativo', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
            $classe     = trim(filter_input(INPUT_POST, 'classe', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
            $quantidade = trim(filter_input(INPUT_POST, 'quantidade', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? '');
            $preco      = trim(filter_input(INPUT_POST, 'preco', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? '');

            if (empty($ativo) || empty($quantidade) || empty($preco)) {
                echo json_encode(['resposta' => $this->mensagensModel['genericas']['formulario_incompleto']]);
                exit;
            }

            $dadosFilha = [
                'ativo'      => $ativo,
                'classe'     => $classe,
                'quantidade' => $quantidade,
                'preco'      => $preco
            ];
        } 
        elseif ($tipo === 'C') {
            $id_cofre = trim(filter_input(INPUT_POST, 'id_cofre', FILTER_SANITIZE_NUMBER_INT) ?? '');

            if (empty($id_cofre)) {
                echo json_encode(['resposta' => $this->mensagensModel['transacoes']['salvar']['cofre_invalido']]);
                exit;
            }

            $dadosFilha = ['id_cofre' => $id_cofre];
        }

        try {
            $transacaoModel = new Transacao();
            $transacaoModel->salvarTransacaoCompleta($dadosPai, $dadosFilha, $tipo);

            echo json_encode(['sucesso' => true, 'msgTipo' => 'success', 'mensagem' => 'Transação cadastrada com sucesso!']);
            
        } catch (Exception $e) {
            echo json_encode(['resposta' => $this->mensagensModel['genericas']['erro_interno'] ]);
        }
        exit;
    }
}