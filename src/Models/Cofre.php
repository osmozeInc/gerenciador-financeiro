<?php
require_once 'Model.php';

class Cofre extends Model {
    
    public function selectAllCofres() {
        $query = "
            SELECT * FROM cofres
        ";
        
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
}