<?php
require_once 'Model.php';

class ContaMetodo extends Model {
    
    public function selectAllContas($tenantId) {
        $query = "SELECT id, nome FROM contas_metodos where tenant_id = :tenant_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':tenant_id', $tenantId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function insert($nome) {
        $query = "INSERT INTO contas_metodos (nome) VALUES (:nome)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':nome', $nome);
        $stmt->execute();
    }
}