<?php

declare(strict_types=1);

require_once __DIR__ . '/../banco/database.php';
require_once __DIR__ . '/../banco/crud.php';

try {
    // Conecta ao banco de dados
    $pdo = database();

    // Dados que serão cadastrados
    $novoUsuario = [
        'nome' => 'tutu',
        'email' => 'arthur@gmail.com',
        'data_nascimento' => '2009-08-27'
    ];

    // Insere os dados na tabela alunos
    $idNovoUsuario = create($pdo, 'alunos', $novoUsuario);

    echo 'Novo usuário inserido com o ID: '
        . htmlspecialchars((string) $idNovoUsuario, ENT_QUOTES, 'UTF-8');

} catch (PDOException $e) {
    error_log($e->getMessage());

    http_response_code(500);
    echo 'Erro ao cadastrar usuário. Verifique a conexão e a estrutura do banco.';
}
