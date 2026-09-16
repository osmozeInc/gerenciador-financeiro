<?php
require_once 'Model.php';

class Assinatura extends Model {
    
    public function selectAllAssinaturas($tenantId) {
        $query = "
            SELECT 
                a.id, 
                a.nome, 
                a.valor_mensal, 
                a.dia_vencimento, 
                a.status,
                cm.nome AS conta_nome
            FROM assinaturas a
            LEFT JOIN contas_metodos cm ON a.id_conta_metodo = cm.id
            WHERE a.tenant_id = :tenant_id
            ORDER BY a.status ASC, a.dia_vencimento ASC
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':tenant_id', $tenantId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function salvarAssinatura($dados) {
        $query = "
            INSERT INTO assinaturas (tenant_id, id_conta_metodo, nome, valor_mensal, dia_vencimento, status) 
            VALUES (:tenant_id, :conta, :nome, :valor, :dia, :status)
        ";
        
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':tenant_id' => $dados['tenant_id'],
            ':conta'     => $dados['id_conta_metodo'],
            ':nome'      => $dados['nome'],
            ':valor'     => $dados['valor_mensal'],
            ':dia'       => $dados['dia_vencimento'],
            ':status'    => $dados['status']
        ]);
    }
    
    public function alterarStatusPorId($id, $status, $tenantId) {
        $query = "UPDATE assinaturas SET status = :status WHERE id = :id AND tenant_id = :tenant_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':tenant_id', $tenantId);
        return $stmt->execute();
    }
}