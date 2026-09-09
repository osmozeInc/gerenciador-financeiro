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
                c.status,
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
                c.id, c.nome, c.descricao, c.local, c.valor_meta, c.data_criacao, c.status, c.id_conta_resgate,
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
        $query = "INSERT INTO cofres (nome, descricao, local, valor_meta, data_criacao, tenant_id, id_conta_resgate)
                  VALUES (:nome, :descricao, :local, :valor_meta, :data_criacao, :tenant_id, :id_conta_resgate)";
        
        $stmt = $this->pdo->prepare($query);

        return $stmt->execute([
            'nome' => $dados['nome'],
            'descricao' => $dados['descricao'],
            'local' => $dados['local'],
            'valor_meta' => $dados['valor_meta'],
            'data_criacao' => $dados['data_criacao'],
            'tenant_id' => $dados['tenant_id'],
            'id_conta_resgate' => $dados['id_conta_resgate']
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

    public function resgatarSaldoCofre($idCofre, $dadosReceita, $dadosCofre, $tenantId) {
        try {
            $this->pdo->beginTransaction();

            // 1. Cria a transação de SAÍDA (negativa)
            $queryTransacaoCofre = "INSERT INTO transacoes (id_categoria, id_conta_metodo, descricao, data_transacao, valor_total, tenant_id)
                                    VALUES (:id_categoria, :id_conta_metodo, :descricao, :data_transacao, :valor_total, :tenant_id)";
            $stmtCofre = $this->pdo->prepare($queryTransacaoCofre);
            $stmtCofre->execute([
                'id_categoria'    => $dadosCofre['id_categoria'],
                'id_conta_metodo' => $dadosCofre['id_conta_metodo'],
                'descricao'       => $dadosCofre['descricao'],
                'data_transacao'  => $dadosCofre['data_transacao'],
                'valor_total'     => $dadosCofre['valor_total'],
                'tenant_id'       => $tenantId
            ]);
            
            $idTransacaoNegativa = $this->pdo->lastInsertId();

            // 2. Relaciona a transação negativa ao cofre
            $queryTCofres = "INSERT INTO t_cofres (id_transacao, id_cofre) VALUES (:id_transacao, :id_cofre)";
            $stmtTCofres = $this->pdo->prepare($queryTCofres);
            $stmtTCofres->execute([
                'id_transacao' => $idTransacaoNegativa,
                'id_cofre'     => $idCofre
            ]);

            // 3. A NOVA TRAVA: Bloqueia todas as transações atreladas a este cofre (histórico + a saída que acabamos de criar)
            $queryBloqueio = "UPDATE transacoes 
                            SET bloqueada = true 
                            WHERE id IN (SELECT id_transacao FROM t_cofres WHERE id_cofre = :id_cofre) 
                            AND tenant_id = :tenant_id";
            $stmtBloqueio = $this->pdo->prepare($queryBloqueio);
            $stmtBloqueio->execute([
                'id_cofre'  => $idCofre,
                'tenant_id' => $tenantId
            ]);

            // 4. Cria a transação de RECEITA (positiva) devolvendo o dinheiro
            $queryTransacaoReceita = "INSERT INTO transacoes (id_categoria, id_conta_metodo, descricao, data_transacao, valor_total, tenant_id)
                                    VALUES (:id_categoria, :id_conta_metodo, :descricao, :data_transacao, :valor_total, :tenant_id)";
            $stmtReceita = $this->pdo->prepare($queryTransacaoReceita);
            $stmtReceita->execute([
                'id_categoria'    => $dadosReceita['id_categoria'],
                'id_conta_metodo' => $dadosReceita['id_conta_metodo'], 
                'descricao'       => $dadosReceita['descricao'],
                'data_transacao'  => $dadosReceita['data_transacao'],
                'valor_total'     => $dadosReceita['valor_total'], 
                'tenant_id'       => $tenantId
            ]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e; 
        }
    }
}