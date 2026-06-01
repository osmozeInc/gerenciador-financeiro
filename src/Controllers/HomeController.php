<?php
require_once 'Controller.php';
require_once __DIR__ . '/../Models/Categoria.php';

class HomeController extends Controller {
    
    public function index() {
        $categoriaModel = new Categoria();
        $CategoriasAWS = $categoriaModel->listarTodas();

        $dadosParaTela = [
            'nome_usuario' => 'Caio',
            'categorias'  => $CategoriasAWS,
        ];

        $this->render('home', $dadosParaTela);
    }
}