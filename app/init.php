<?php

require_once __DIR__ . '/banco/database.php';
require_once __DIR__ . '/banco/crud.php';

$pdo = null;
$erro_banco = '';

try {
    $pdo = database();
} catch (Throwable $e) {
    $erro_banco = $e->getMessage();
    error_log('Banco indisponível: ' . $erro_banco);
}
