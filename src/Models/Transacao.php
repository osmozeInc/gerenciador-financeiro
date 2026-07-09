<?php
require_once 'Model.php';

class Transacao extends Model {
    
    public function selectAllTransacoes($tenantId) {
        $query = "
            SELECT t.id, t.data_transacao, t.descricao, t.valor_total, 
                   c.nome AS categoria_nome, c.tipo AS categoria_tipo, 
                   cm.nome AS conta_nome 
            FROM transacoes t 
            INNER JOIN categorias c ON t.id_categoria = c.id 
            LEFT JOIN contas_metodos cm ON t.id_conta_metodo = cm.id 
            WHERE t.tenant_id = :tenant_id
            ORDER BY t.data_transacao DESC LIMIT 100
        ";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':tenant_id', $tenantId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function salvarTransacaoReceita($dados) {
        $query = "INSERT INTO transacoes (id_categoria, id_conta_metodo, descricao, data_transacao, valor_total, tenant_id) VALUES (:categoria, :conta, :descricao, :data, :valor, :tenant_id)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':categoria', $dados['categoria_id']);
        $stmt->bindValue(':conta', $dados['conta_id']);
        $stmt->bindValue(':descricao', $dados['descricao']);
        $stmt->bindValue(':valor', $dados['valor']);
        $stmt->bindValue(':data', $dados['data']);
        $stmt->bindValue(':tenant_id', $dados['tenant_id']);
        $stmt->execute();
    }

    public function salvarTransacaoDespesa($dadosTransacao, $dadosDespesa) {
        try {
            $this->pdo->beginTransaction();

            $sqlTransacao = "INSERT INTO transacoes (id_categoria, id_conta_metodo, descricao, data_transacao, valor_total, tenant_id) 
                             VALUES (:categoria, :conta, :descricao, :data, :valor, :tenant_id)";
            $stmtTransacao = $this->pdo->prepare($sqlTransacao);
            
            $stmtTransacao->execute([
                ':categoria' => $dadosTransacao['categoria_id'],
                ':conta'     => $dadosTransacao['conta_id'],
                ':descricao' => $dadosTransacao['descricao'],
                ':valor'     => $dadosTransacao['valor'],
                ':data'      => $dadosTransacao['data'],
                ':tenant_id' => $dadosTransacao['tenant_id']
            ]);

            $idTransacao = $this->pdo->lastInsertId();

            $sqlDespesa = "INSERT INTO t_despesas (id_transacao, parcelado, qtd_parcelas) VALUES (?, ?, ?)";
                $this->pdo->prepare($sqlDespesa)->execute([
                    $idTransacao, 
                    $dadosDespesa['parcelado'], 
                    $dadosDespesa['qtd_parcelas']
                ]);

            $this->pdo->commit();
            return $idTransacao;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function salvarTransacaoInvestimento($dadosTransacao, $dadosInvestimento) {
        try {
            $this->pdo->beginTransaction();

            $sqlTransacao = "INSERT INTO transacoes (id_categoria, id_conta_metodo, descricao, data_transacao, valor_total, tenant_id) 
                             VALUES (:categoria, :conta, :descricao, :data, :valor, :tenant_id)";
            $stmtTransacao = $this->pdo->prepare($sqlTransacao);
            
            $stmtTransacao->execute([
                ':categoria' => $dadosTransacao['categoria_id'],
                ':conta'     => $dadosTransacao['conta_id'],
                ':descricao' => $dadosTransacao['descricao'],
                ':valor'     => $dadosTransacao['valor'],
                ':data'      => $dadosTransacao['data'],
                ':tenant_id' => $dadosTransacao['tenant_id']
            ]);

            $idTransacao = $this->pdo->lastInsertId();

            $sqlInvestimento = "INSERT INTO t_investimentos (id_transacao, ativo, classe, quantidade, preco_unitario_compra) VALUES (?, ?, ?, ?, ?)";
                $this->pdo->prepare($sqlInvestimento)->execute([
                    $idTransacao,
                    $dadosInvestimento['ativo'],
                    $dadosInvestimento['classe_id'],
                    $dadosInvestimento['quantidade'],
                    $dadosInvestimento['preco']
                ]);

            $this->pdo->commit();
            return $idTransacao;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function salvarTransacaoCofre($dadosTransacao, $dadosCofre) {
        try {
            $this->pdo->beginTransaction();

            $sqlTransacao = "INSERT INTO transacoes (id_categoria, id_conta_metodo, descricao, data_transacao, valor_total, tenant_id) 
                             VALUES (:categoria, :conta, :descricao, :data, :valor, :tenant_id)";
            $stmtTransacao = $this->pdo->prepare($sqlTransacao);
            
            $stmtTransacao->execute([
                ':categoria' => $dadosTransacao['categoria_id'],
                ':conta'     => $dadosTransacao['conta_id'],
                ':descricao' => $dadosTransacao['descricao'],
                ':valor'     => $dadosTransacao['valor'],
                ':data'      => $dadosTransacao['data'],
                ':tenant_id' => $dadosTransacao['tenant_id']
            ]);

            $idTransacao = $this->pdo->lastInsertId();

            $sqlCofre = "INSERT INTO t_cofres (id_transacao, id_cofre) VALUES (?, ?)";
                $this->pdo->prepare($sqlCofre)->execute([
                    $idTransacao,
                    $dadosCofre['cofre_id']
                ]);

            $this->pdo->commit();
            return $idTransacao;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}