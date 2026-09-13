<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Assinatura.php'; // Crie este model futuramente

class AssinaturasController extends Controller {

    public function index() {
        $this->render('assinaturas');
    }

    public function selectAll() {
        header('Content-Type: application/json');
        try {
            $assinaturaModel = new Assinatura();
            $assinaturas = $assinaturaModel->selectAllAssinaturas($this->idUsuarioLogado);
            
            echo json_encode([
                'resposta' => $this->mensagensModel['silenciosas']['selecionar_dados']['busca_com_sucesso'],
                'assinaturas' => $assinaturas,
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'resposta' => $this->mensagensModel['silenciosas']['selecionar_dados']['erro_interno'],
                'detalhes' => $e->getMessage()
            ]);
        }
        exit;
    }

    public function salvar() {
        header('Content-Type: application/json');

        $nome = trim(filter_input(INPUT_POST, 'nomeAssinatura', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $valor = trim(filter_input(INPUT_POST, 'valorAssinatura', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) ?? '');
        $dia = trim(filter_input(INPUT_POST, 'diaVencimento', FILTER_SANITIZE_NUMBER_INT) ?? '');
        $conta_id = trim(filter_input(INPUT_POST, 'metodoContaAssinatura', FILTER_SANITIZE_NUMBER_INT) ?? '');

        $dados = [
            'nome' => $nome,
            'valor_mensal' => $valor,
            'dia_vencimento' => $dia,
            'id_conta_metodo' => $conta_id,
            'status' => 'ativa',
            'tenant_id' => $this->idUsuarioLogado
        ];

        try {
            $assinaturaModel = new Assinatura();
            $assinaturaModel->salvarAssinatura($dados);
            echo json_encode(['resposta' => ['sucesso' => true, 'msgTipo' => 'success', 'mensagem' => 'Assinatura registrada!']]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'resposta' => ['sucesso' => false, 'msgTipo' => 'error', 'mensagem' => 'Erro ao salvar.'],
                'detalhes' => $e->getMessage()
            ]);
        }
        exit;
    }
}