<?php
require_once 'Model.php';

class Transacao extends Model {
    
    public function selectAllTransacoes() {
        $query = "SELECT t.data_transacao, t.descricao, c.nome AS categoria_nome, c.tipo AS categoria_tipo, p.nome AS pagamento_nome, t.parcelas, t.valor FROM transacoes t INNER JOIN categorias c ON t.categoria_id = c.id INNER JOIN pagamentos p ON t.pagamento_id = p.id ORDER BY t.data_transacao DESC";
        $stmt = $this->pdo->query($query);
        
        return $stmt->fetchAll(); 
    }

    public function insert($descricao, $categoria, $valor, $data, $pagamento, $parcelas) {
        $query = "INSERT INTO transacoes (categoria_id, pagamento_id, parcelas, descricao, valor, data_transacao) VALUES (:categoria, :pagamento, :parcelas, :descricao, :valor, :data)";
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':categoria', $categoria);
        $stmt->bindValue(':pagamento', $pagamento);
        $stmt->bindValue(':parcelas', $parcelas);
        $stmt->bindValue(':descricao', $descricao);
        $stmt->bindValue(':valor', $valor);
        $stmt->bindValue(':data', $data);

        $stmt->execute();
    }
}