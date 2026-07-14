<?php
require_once 'Controller.php';

class HistoricoCompletoController extends Controller {
    
    public function index() {
        $this->render('historicoCompleto');
    }
}