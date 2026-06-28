<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Categoria.php';
require_once __DIR__ . '/../Models/Pagamento.php';
require_once __DIR__ . '/../Models/Transacao.php';

class TransacoesController extends Controller {
    
    public function index() {

        $this->render('transacoes');
    }
    
    public function selectDados() {
        header('Content-Type: application/json');

        $categoriaModel = new Categoria();
        $pagamentoModel = new Pagamento();
        $transacaoModel = new Transacao();

        $categorias = $categoriaModel->selectAllCategorias();
        $pagamentos = $pagamentoModel->selectAllPagamentos();
        $transacoes = $transacaoModel->selectAllTransacoes();

        $categoriasAgrupadas = ['R' => [], 'D' => [],  'I' => [], 'C' => []];

        foreach ($categorias as $categoria) {
            $categoriasAgrupadas[$categoria['tipo']][] = $categoria;
        }

        echo json_encode([
            'categorias' => $categoriasAgrupadas,
            'pagamentos' => $pagamentos,
            'transacoes' => $transacoes
        ]);

        exit;
    }

    public function salvar() {
        header('Content-Type: application/json');

        $descricao = trim(filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS));
        $categoria = trim(filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_SPECIAL_CHARS));
        $valor = trim(filter_input(INPUT_POST, 'valor', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
        $data = trim(filter_input(INPUT_POST, 'data', FILTER_SANITIZE_SPECIAL_CHARS));
        $pagamento = trim(filter_input(INPUT_POST, 'pagamento', FILTER_SANITIZE_SPECIAL_CHARS));
        $parcelas = trim(filter_input(INPUT_POST, 'parcelas', FILTER_SANITIZE_NUMBER_INT));

        if (empty($descricao) || empty($categoria) || empty($valor) || empty($data) || empty($pagamento) || empty($parcelas)) {
            echo json_encode(['sucesso' => false, 'msgTipo' => 'warning',  'mensagem' => 'Preencha todos os campos obrigatórios.']);
            return;
        }

        try {
            $transacaoModel = new Transacao();
            
            $transacaoModel->insert($descricao, $categoria, $valor, $data, $pagamento, $parcelas);

            echo json_encode(['sucesso' => true, 'msgTipo' => 'success', 'mensagem' => 'Transação cadastrada com sucesso.']);
            
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'msgTipo' => 'danger', 'mensagem' => 'Erro interno ao salvar na base de dados.']);
        }
    }

}