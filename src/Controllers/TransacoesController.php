<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Categoria.php';
require_once __DIR__ . '/../Models/Pagamentos.php';

class TransacoesController extends Controller {
    
    public function index() {
        $categoriaModel = new Categoria();
        $pagamentosModel = new Pagamentos();

        $categorias = $categoriaModel->selectAllCategorias();
        $pagamentos = $pagamentosModel->selectAllPagamentos();

        $categoriasAgrupadas = [
            'R' => [], // Receitas
            'D' => [], // Despesas
            'I' => [], // Investimentos
            'C' => []  // Cofres
        ];

        foreach ($categorias as $categoria) {
            $categoriasAgrupadas[$categoria['tipo']][] = $categoria;
        }

        $dadosParaTela = [
            'categorias' => $categoriasAgrupadas,
            'pagamentos' => $pagamentos
        ];

        $this->render('transacoes', $dadosParaTela);
    }

    
}