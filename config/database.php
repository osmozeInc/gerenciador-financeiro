<?php

class Database {
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {
            $host   = getenv('DB_HOST');
            $db     = getenv('DB_NAME');
            $user   = getenv('DB_USER');
            $pass   = getenv('DB_PASS');
            $port   = getenv('DB_PORT');

            // Mantive a sua montagem de DSN intacta
            $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

            try {
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            } catch (PDOException $e) {
                // CORREÇÃO CRÍTICA 1: Trocamos o 'echo' por 'die()'
                die("Erro crítico de conexão com o banco: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}