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

    public function selectCofrePorId($id, $tenantId) {
        $queryCofre = "
            SELECT 
                c.id, c.nome, c.descricao, c.local, c.valor_meta, c.data_criacao,
                COALESCE(SUM(t.valor_total), 0) AS valor_total
            FROM cofres c
            LEFT JOIN t_cofres tc ON c.id = tc.id_cofre
            LEFT JOIN transacoes t ON tc.id_transacao = t.id
            WHERE c.id = :id AND c.tenant_id = :tenant_id
            GROUP BY 
                c.id, c.nome, c.descricao, c.local, c.valor_meta, c.data_criacao
        ";
        
        $stmtCofre = $this->pdo->prepare($queryCofre);
        $stmtCofre->bindValue(':id', $id);
        $stmtCofre->bindValue(':tenant_id', $tenantId);
        $stmtCofre->execute();
        
        $cofre = $stmtCofre->fetch(PDO::FETCH_ASSOC);

        if (!$cofre) {
            return false; 
        }

        $queryTransacoes = "
            SELECT 
                t.id, t.valor_total, t.data_transacao, t.descricao
            FROM transacoes t
            INNER JOIN t_cofres tc ON t.id = tc.id_transacao
            INNER JOIN cofres c ON tc.id_cofre = c.id
            WHERE tc.id_cofre = :id AND c.tenant_id = :tenant_id
            ORDER BY t.data_transacao DESC
        ";

        $stmtTrans = $this->pdo->prepare($queryTransacoes);
        $stmtTrans->bindValue(':id', $id);
        $stmtTrans->bindValue(':tenant_id', $tenantId); // Segurança multi-tenant
        $stmtTrans->execute();

        // 3. Acopla o array de transações dentro do array do cofre
        $cofre['transacoes'] = $stmtTrans->fetchAll(PDO::FETCH_ASSOC);

        return $cofre;
    }

    public function salvarCofre($dados) {
        $query = "INSERT INTO cofres (nome, descricao, local, valor_meta, data_criacao, tenant_id)
                  VALUES (:nome, :descricao, :local, :valor_meta, :data_criacao, :tenant_id)";
        
        $stmt = $this->pdo->prepare($query);

        return $stmt->execute([
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'],
            'local' => $dados['local'],
            'valor_meta' => $dados['valor_meta'],
            'data_criacao' => $dados['data_criacao'],
            'tenant_id' => $dados['tenant_id']
        ]);
    }

    public function alterarStatusPorId($id, $motivo, $tenantId) {
        $query = "UPDATE cofres SET status = :status WHERE id = :id AND tenant_id = :tenant_id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            'id' => $id,
            'status' => $motivo,
            'tenant_id' => $tenantId
        ]);
    }
}