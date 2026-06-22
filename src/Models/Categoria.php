<?php
require_once 'Model.php';

class Categoria extends Model {
    
    public function selectAllIdNomeTipo() {
        $query = "SELECT id, nome, tipo FROM categorias";
        $stmt = $this->pdo->query($query);
        
        return $stmt->fetchAll(); 
    }
}