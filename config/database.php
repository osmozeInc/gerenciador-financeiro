<?php

$host   = getenv('DB_HOST');
$db     = getenv('DB_NAME');
$user   = getenv('DB_USER');
$pass   = getenv('DB_PASS');
$port   = getenv('DB_PORT');

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