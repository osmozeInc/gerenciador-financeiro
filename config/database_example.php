<?php

$host   = '';
$db     = 'postgres';
$user   = '';
$pass   = '';
$port   = '5432';

$dsn = "pgsql:host=$host;port=$port;dbname=$db;";

try {
    // Cria a conexão persistente e segura via PDO
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    echo "Conexão com o PostgreSQL na AWS realizada com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
}

?>