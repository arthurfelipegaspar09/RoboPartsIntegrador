<?php

declare(strict_types=1);

function database(): PDO
{
    static $connection = null;

    // Reutiliza a conexão caso ela já exista
    if ($connection instanceof PDO) {
        return $connection;
    }

    // Configurações obtidas das variáveis de ambiente do Coolify
    $host = getenv('DB_HOST') ?: 'db';
    $port = getenv('DB_PORT') ?: '3306';
    $database = getenv('DB_DATABASE') ?: 'integrador';
    $username = getenv('DB_USERNAME') ?: 'integrador';
    $password = getenv('DB_PASSWORD') ?: '';

    // Cria a conexão com o MySQL
    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

    $connection = new PDO(
        $dsn,
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    return $connection;
}