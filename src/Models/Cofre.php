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
}