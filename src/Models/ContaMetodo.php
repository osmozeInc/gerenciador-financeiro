<?php
require_once 'Model.php';

class Transacao extends Model {
    
    // 1. SELECT ATUALIZADO (O Livro-Razão)
    public function selectAllTransacoes() {
        // Busca apenas os dados base. Deixamos LEFT JOIN na conta caso seja uma transação sem conta (ex: cofre interno)
        $query = "
            SELECT t.id, t.data_transacao, t.descricao, t.valor_total, 
                   c.nome AS categoria_nome, c.tipo AS categoria_tipo, 
                   cm.nome AS conta_nome 
            FROM transacoes t 
            INNER JOIN categorias c ON t.categoria_id = c.id 
            LEFT JOIN contas_metodos cm ON t.id_conta_metodo = cm.id 
            ORDER BY t.data_transacao DESC
        ";
        
        $stmt = $this->pdo->query($query);
        // FETCH_ASSOC é uma boa prática para retornar apenas um array limpo com os nomes das colunas
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }

    // 2. INSERT COM HERANÇA (Transação ACID)
    public function salvarTransacaoCompleta($dadosPai, $dadosFilha, $tipo) {
        try {
            // Trava o banco: inicia a transação segura
            $this->pdo->beginTransaction();

            // A. Insere na tabela pai (transacoes)
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

            // B. Resgata o ID que o PostgreSQL acabou de gerar
            $idTransacao = $this->pdo->lastInsertId();

            // C. Direciona para a tabela filha correspondente
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
            // Se QUALQUER coisa der errado, cancela o insert inteiro
            $this->pdo->rollBack();
            throw $e;
        }
    }
}