<?php
require_once 'Model.php';

class Pagamento extends Model {
    
    public function selectAllPagamentos() {
        $query = "SELECT * FROM pagamentos";
        $stmt = $this->pdo->query($query);
        
        return $stmt->fetchAll(); 
    }

    public function insert($nome) {
        $query = "INSERT INTO pagamentos (nome) VALUES (:nome)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':nome', $nome);
        $stmt->execute();
    }
}