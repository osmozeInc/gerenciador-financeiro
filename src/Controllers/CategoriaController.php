<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Categoria.php';

class CategoriaController extends Controller {
    
    public function salvar() {
        header('Content-Type: application/json');

        // Blindagem contra null do PHP 8.1+
        $nome = trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $tipo = trim(filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

        if (empty($nome) || empty($tipo)) {
            echo json_encode(['resposta' => $this->mensagensModel['categoria']['salvar']['formulario_incompleto']]);
            exit;
        }

        $tiposPermitidos = ['R', 'D', 'I', 'C'];
        if (!in_array($tipo, $tiposPermitidos)) {
            echo json_encode(['resposta' => $this->mensagensModel['categoria']['salvar']['categoria_invalida']]);
            exit;
        }

        try {
            $categoriaModel = new Categoria();
            $categoriaModel->insert($nome, $tipo);

            echo json_encode(['resposta' => $this->mensagensModel['categoria']['salvar']['sucesso']]);
            
        } catch (Exception $e) {
            echo json_encode(['resposta' => $this->mensagensModel['categoria']['salvar']['erro']]);
        }
        
        exit; // Trava de segurança final
    }
}