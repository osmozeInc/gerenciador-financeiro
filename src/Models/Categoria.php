<?php
require_once 'Model.php';

class Categoria extends Model {
    
    public function selectAllCategorias() {
        $query = "SELECT * FROM categorias";
        $stmt = $this->pdo->query($query);
        $categorias = $stmt->fetchAll(); 

        $categoriasAgrupadas = ['R' => [], 'D' => [],  'I' => [], 'C' => []];

        foreach ($categorias as $categoria) {
            $categoriasAgrupadas[$categoria['tipo']][] = $categoria;
        }
        
        return $categoriasAgrupadas;
    }

    public function insert($nome, $tipo) {
        $query = "INSERT INTO categorias (nome, tipo) VALUES (:nome, :tipo)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':tipo', $tipo);
        $stmt->execute();
    }
}