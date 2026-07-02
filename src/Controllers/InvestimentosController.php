<?php
require_once 'Controller.php';

class InvestimentosController extends Controller {
    
    public function index() {
        $this->render('investimentos');
    }
}