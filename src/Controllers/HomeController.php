<?php
require_once 'Controller.php';
require_once __DIR__ . '/../Models/Categoria.php';

class HomeController extends Controller {
    
    public function index() {
        $categoriaModel = new Categoria();
        // $CategoriasAWS = $categoriaModel->listarTodas();

        $formatador = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);

        $dadosParaTela = [
            'nome_usuario' => 'Caio',
            'sobrenome_usuario' => 'Mendes',
            'receita_bruta' => 220000 / 100,
            'despesa_bruta' => 153000 / 100,
            'formatacao_real' => $formatador,
            'cofre' => 500000 / 100,
            'receita_total' => 600000 / 100,
            'investidos' => 300000 / 100,
            // 'categorias'  => $CategoriasAWS,
        ];

        $this->render('home', $dadosParaTela);
    }
}