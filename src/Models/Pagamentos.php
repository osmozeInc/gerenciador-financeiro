<?php
require_once 'Model.php';

class Pagamentos extends Model {
    
    public function selectAllPagamentos() {
        $query = "SELECT * FROM pagamentos";
        $stmt = $this->pdo->query($query);
        
        return $stmt->fetchAll(); 
    }
}