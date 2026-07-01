<?php
require_once 'Model.php';

class ContaMetodo extends Model {
    
    public function selectAllContas() {
        $query = "SELECT * FROM contas_metodos";
        $stmt = $this->pdo->query($query);

        return $stmt->fetchAll();
    }

    public function insert($nome) {
        $query = "INSERT INTO contas_metodos (nome) VALUES (:nome)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':nome', $nome);
        $stmt->execute();

    }
}