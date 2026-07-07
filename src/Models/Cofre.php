<?php
require_once 'Model.php';

class Cofre extends Model {
    
    public function selectAllCofres($tenantId) {
        $query = "SELECT id, nome FROM cofres where tenant_id = :tenant_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':tenant_id', $tenantId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
}