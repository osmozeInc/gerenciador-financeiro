<?php
require_once 'Controller.php';
require_once __DIR__ . '/../Models/Categoria.php';

class HomeController extends Controller {
    
    public function index() {
        $this->render('home');
    }
}