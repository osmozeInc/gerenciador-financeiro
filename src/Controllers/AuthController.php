<?php
require_once __DIR__ . '/Controller.php';
// require_once __DIR__ . '/../Models/Usuario.php';

class AuthController extends Controller {
    
    public function login() {
        if (isset($_SESSION['usuario_id'])) {
            header('Location: /home');
            exit;
        }
        
        $this->render('login', false); 
    }

    public function processar() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /auth/login');
            exit;
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            $_SESSION['erro_login'] = 'Preencha todos os campos.';
            header('Location: /auth/login');
            exit;
        }
            
        // $usuarioModel = new Usuario();
        // $user = $usuarioModel->autenticar($email, $senha);
        
        $usuarioDbSimulado = [
            'id' => 2,
            'email' => 'admin@teste.com',
            'senha_hash' => password_hash('123', PASSWORD_DEFAULT) 
        ];

        if ($email === $usuarioDbSimulado['email'] && password_verify($senha, $usuarioDbSimulado['senha_hash'])) {
            $_SESSION['usuario_id'] = $usuarioDbSimulado['id'];
            header('Location: /home');
        }
        else {
            $_SESSION['erro_login'] = 'Credenciais inválidas.';
            header('Location: /auth/login');
        }
        exit;
    }

    public function sair() {
        $_SESSION = [];
        session_destroy();

        header('Content-Type: application/json');
        echo json_encode( ['resposta' => $this->mensagensModel['conta']['logoutRealizado']] );
        exit;
    }
}