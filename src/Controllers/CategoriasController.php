<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Categoria.php';

class CategoriasController extends Controller {

    public function selectDados() {
        header('Content-Type: application/json');

        try {
            $categoriaModel = new Categoria();
            $categorias = $categoriaModel->selectAllcategorias();
            
            echo json_encode([
                'resposta' => $this->mensagensModel['silenciosas']['selecionar_dados']['busca_com_sucesso'],
                'categorias' => $categorias
            ]);
        
        } catch (Exception $e) {
            echo json_encode([
                'resposta' => $this->mensagensModel['silenciosas']['selecionar_dados']['erro_interno'],
                'detalhes' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function selectDadosReceita() {
        header('Content-Type: application/json');

        try {
            $categoriaModel = new Categoria();
            $categorias = $categoriaModel->selectCategoriasReceita($this->idUsuarioLogado);
            
            echo json_encode([
                'resposta' => $this->mensagensModel['silenciosas']['selecionar_dados']['busca_com_sucesso'],
                'categorias' => $categorias
            ]);
        
        } catch (Exception $e) {
            echo json_encode([
                'resposta' => $this->mensagensModel['silenciosas']['selecionar_dados']['erro_interno'],
                'detalhes' => $e->getMessage()
            ]);
        }
        exit;
    }

    
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

            echo json_encode(['resposta' => $this->mensagensModel['categoria']['salvar']['salvo_com_sucesso']]);
            
        } catch (Exception $e) {
            echo json_encode(['resposta' => $this->mensagensModel['categoria']['salvar']['erro']]);
        }
        
        exit; // Trava de segurança final
    }
}