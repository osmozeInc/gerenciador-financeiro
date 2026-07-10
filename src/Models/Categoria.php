<?php
require_once 'Model.php';

class Categoria extends Model {
    
    public function selectAllCategorias($tenantId) {
        $query = "SELECT id, nome, tipo FROM categorias WHERE tenant_id = :tenant_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':tenant_id', $tenantId);
        $stmt->execute();
        $categorias = $stmt->fetchAll();

        $categoriasAgrupadas = ['R' => [], 'D' => [],  'I' => [], 'C' => []];

        foreach ($categorias as $categoria) {
            $categoriasAgrupadas[$categoria['tipo']][] = $categoria;
        }
        
        return $categoriasAgrupadas;
    }
    
    public function selectCategoriasReceita($tenantId) {
        $query = "select id, nome from categorias where tipo = 'R' and tenant_id = :tenant_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':tenant_id', $tenantId);
        $stmt->execute();
        $categorias = $stmt->fetchAll(); 
        
        return $categorias;
    }
    
    public function selectCategoriasDespesa($tenantId) {
        $query = "select id, nome from categorias where tipo = 'D' and tenant_id = :tenant_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':tenant_id', $tenantId);
        $stmt->execute();
        $categorias = $stmt->fetchAll();
        
        return $categorias;
    }

    public function insert($nome, $tipo, $tenantId) {
        $query = "INSERT INTO categorias (nome, tipo, tenant_id) VALUES (:nome, :tipo, :tenant_id)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':tipo', $tipo);
        $stmt->bindValue(':tenant_id', $tenantId);
        $stmt->execute();
    }

    public function getIdCategoriaInvestimento() {
        $query = "SELECT id FROM categorias WHERE tipo = 'I' AND nome = 'Investimento' LIMIT 1";
        $stmt = $this->pdo->query($query);
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $categoria ? $categoria['id'] : null;
    }

    public function getIdCategoriaCofre() {
        $query = "SELECT id FROM categorias WHERE tipo = 'C' AND nome = 'Cofre' LIMIT 1";
        $stmt = $this->pdo->query($query);
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $categoria ? $categoria['id'] : null;
    }
}