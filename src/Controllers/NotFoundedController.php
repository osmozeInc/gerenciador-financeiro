<?php
require_once 'Controller.php';

class NotFoundedController extends Controller {
    
    public function index() {
        $this->render('404');
    }
}