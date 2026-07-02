<?php
require_once 'Model.php';

class ClasseInvestimento extends Model {
    
    public function selectAllClasses() {
        $query = "
            SELECT * FROM classes_investimento
        ";
        
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function insert($nome) {
        $query = "INSERT INTO classes_investimento (nome) VALUES (:nome)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':nome', $nome);
        $stmt->execute();
    }

}