<?php
require_once 'Model.php';

class Categoria extends Model {
    
    public function listarTodas() {
        $query = "SELECT id, nome, tipo FROM categorias ORDER BY nome ASC";
        $stmt = $this->pdo->query($query);
        
        return $stmt->fetchAll(); 
    }
}