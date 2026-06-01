<?php

class Model {
    protected $pdo;

    public function __construct() {
        // Importa a conexão que configuramos anteriormente com o .env
        require_once __DIR__ . '/../../config/database.php';
        
        $this->pdo = $pdo;
    }
}