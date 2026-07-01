<?php
require_once 'Model.php';

class Transacao extends Model {
    
    public function selectAllTransacoes() {
        $query = "
            SELECT t.id, t.data_transacao, t.descricao, t.valor_total, 
                   c.nome AS categoria_nome, c.tipo AS categoria_tipo, 
                   cm.nome AS conta_nome 
            FROM transacoes t 
            INNER JOIN categorias c ON t.id_categoria = c.id 
            LEFT JOIN contas_metodos cm ON t.id_conta_metodo = cm.id 
            ORDER BY t.data_transacao DESC
        ";
        
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    public function salvarTransacaoCompleta($dadosPai, $dadosFilha, $tipo) {
        try {
            $this->pdo->beginTransaction();

            $sqlPai = "INSERT INTO transacoes (id_categoria, id_conta_metodo, descricao, valor_total, data_transacao) 
                       VALUES (:categoria, :conta, :descricao, :valor, :data)";
            $stmtPai = $this->pdo->prepare($sqlPai);
            
            $stmtPai->execute([
                ':categoria' => $dadosPai['categoria_id'],
                ':conta'     => $dadosPai['conta_id'],
                ':descricao' => $dadosPai['descricao'],
                ':valor'     => $dadosPai['valor'],
                ':data'      => $dadosPai['data']
            ]);

            $idTransacao = $this->pdo->lastInsertId();

            if ($tipo === 'D') {
                $sqlFilha = "INSERT INTO t_despesas (id_transacao, parcelado, qtd_parcelas) VALUES (?, ?, ?)";
                $this->pdo->prepare($sqlFilha)->execute([
                    $idTransacao, 
                    $dadosFilha['parcelado'], 
                    $dadosFilha['qtd_parcelas']
                ]);
            } 
            elseif ($tipo === 'I') {
                $sqlFilha = "INSERT INTO t_investimentos (id_transacao, ativo, classe, quantidade, preco_unitario_compra) VALUES (?, ?, ?, ?, ?)";
                $this->pdo->prepare($sqlFilha)->execute([
                    $idTransacao, 
                    $dadosFilha['ativo'], 
                    $dadosFilha['classe'], 
                    $dadosFilha['quantidade'], 
                    $dadosFilha['preco']
                ]);
            } 
            elseif ($tipo === 'C') {
                $sqlFilha = "INSERT INTO t_cofres (id_transacao, id_cofre) VALUES (?, ?)";
                $this->pdo->prepare($sqlFilha)->execute([
                    $idTransacao, 
                    $dadosFilha['id_cofre']
                ]);
            }
            // Se for Receita ('R'), não tem tabela filha, apenas passa direto.

            // Confirma a gravação final de tudo
            $this->pdo->commit();
            return $idTransacao;

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}