<?php
require_once 'Model.php';

class Cofre extends Model {
    
    public function selectNomesCofres($tenantId) {
        $query = "SELECT id, nome FROM cofres where tenant_id = :tenant_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':tenant_id', $tenantId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function selectAllCofres($tenantId) {
        $query = "
            select 
                c.id,
                c.nome,
                c.valor_meta,
                c.local,
                coalesce(SUM(t.valor_total), 0) as valor_total
            from cofres c
            left join t_cofres tc on c.id = tc.id_cofre
            left join transacoes t on tc.id_transacao = t.id
            where c.tenant_id = :tenant_id
            group by 
                c.id,
                c.nome,
                c.valor_meta;
            ";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':tenant_id', $tenantId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
}