<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Categoria.php';
require_once __DIR__ . '/../Models/Pagamento.php';

class TransacoesController extends Controller {
    
    public function index() {

        $this->render('transacoes');
    }
    
    public function selectDados() {
        header('Content-Type: application/json');

        $categoriaModel = new Categoria();
        $pagamentoModel = new Pagamento();

        $categorias = $categoriaModel->selectAllCategorias();
        $pagamentos = $pagamentoModel->selectAllPagamentos();

        $categoriasAgrupadas = ['R' => [], 'D' => [],  'I' => [], 'C' => []];

        foreach ($categorias as $categoria) {
            $categoriasAgrupadas[$categoria['tipo']][] = $categoria;
        }

        echo json_encode([
            'categorias' => $categoriasAgrupadas,
            'pagamentos' => $pagamentos
        ]);

        exit;
    }
}