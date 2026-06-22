<?php
require_once __DIR__ . '/../../config/database.php';

abstract class Model {
    protected $pdo;

    public function __construct() {
        require_once __DIR__ . '/../../config/database.php';
        
        $this->pdo = Database::getConnection();
    }
}