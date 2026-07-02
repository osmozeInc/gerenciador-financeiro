<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Cofre.php';

class CofresController extends Controller {

    public function index() {
        $this->render('contas');
    }
    
    public function selectDados() {
        header('Content-Type: application/json');

        $cofreModel = new Cofre();

        $cofre = $cofreModel->selectAllCofres();

        echo json_encode([
            'cofres' => $cofre
        ]);
        exit;
    }
}