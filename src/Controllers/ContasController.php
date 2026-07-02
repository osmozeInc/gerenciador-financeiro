<?php
require_once 'Controller.php';

class ContasController extends Controller {
    
    public function index() {
        $this->render('contas');
    }
}