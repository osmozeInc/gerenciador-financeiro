<?php
require_once 'Model.php';

class ClasseInvestimento extends Model {
    
    public function selectAllClasses($tenantId) {
        $query = "SELECT id, nome FROM classes_investimento where tenant_id = :tenant_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':tenant_id', $tenantId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($nome) {
        $query = "INSERT INTO classes_investimento (nome) VALUES (:nome)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':nome', $nome);
        $stmt->execute();
    }
}