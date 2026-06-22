<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Categoria.php';

class TransacoesController extends Controller {
    
    public function index() {
        $categoriaModel = new Categoria();
        $categorias = $categoriaModel->selectAllIdNomeTipo();

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
            'categorias' => $categoriasAgrupadas
        ];

        $this->render('transacoes', $dadosParaTela);
    }
}