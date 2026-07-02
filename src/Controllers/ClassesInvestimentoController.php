<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/ClasseInvestimento.php';

class ClassesInvestimentoController extends Controller {
    
    public function selectDados() {
        header('Content-Type: application/json');

        $classesModel = new ClasseInvestimento();

        $classes = $classesModel->selectAllClasses();

        echo json_encode([
            'classes' => $classes
        ]);
        exit;
    }
}