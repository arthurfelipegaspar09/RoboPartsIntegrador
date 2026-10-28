<?php

require_once __DIR__ . '/../banco/database.php';
require_once __DIR__ . '/../banco/crud.php';

$pdo = database();

$novoUsuario = [
    "nome" => "tutu",
    "email" => "arthur@gmail.com",
    "data_nascimento" => "2009-08-27"
];

$idNovoUsuario = create($pdo, 'alunos', $novoUsuario);

echo 'Novo usuário inserido com o ID: ' . $idNovoUsuario;