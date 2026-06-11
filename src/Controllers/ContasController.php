<?php
require_once 'Controller.php';

class ContasController extends Controller {
    
    public function index() {

        $dadosParaTela = [
            'nome_usuario' => 'Caio',
            'sobrenome_usuario' => 'Mendes',
            'receita_bruta' => 220000 / 100,
            'despesa_bruta' => 153000 / 100,
            'cofre' => 500000 / 100,
            'receita_total' => 600000 / 100,
            'investidos' => 300000 / 100
        ];

        $this->render('contas', $dadosParaTela);
    }
}