<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Categoria.php';
require_once __DIR__ . '/../Models/Pagamentos.php';

class TransacoesController extends Controller {
    
    public function index() {

        $this->render('transacoes');
    }
    
    public function selectDados() {
        header('Content-Type: application/json');

        $categoriaModel = new Categoria();
        $pagamentosModel = new Pagamentos();

        $categorias = $categoriaModel->selectAllCategorias();
        $pagamentos = $pagamentosModel->selectAllPagamentos();

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