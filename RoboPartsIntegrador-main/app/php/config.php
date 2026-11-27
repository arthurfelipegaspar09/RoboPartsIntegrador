<?php

require_once __DIR__ . '/../init.php'; 
require_once __DIR__ . '/init.php';       

const LOJA_PUBLICA = false;


function db(): ?PDO
{
    return $GLOBALS['pdo'] ?? null;
}


function normalizarUsuario(array|false $u): ?array
{
    if ($u === false) {
        return null;
    }
    $u['id']          = (int) $u['id'];
    $u['admin']       = (bool) $u['admin'];
    $u['localizacao'] = (bool) $u['localizacao'];
    return $u;
}

function buscarUsuarioPorEmail(string $email): ?array
{
    $pdo = db();
    
    return $pdo ? normalizarUsuario(read($pdo, 'usuarios', 'email = ?', [$email])) : null;
}

function buscarUsuarioPorId(int $id): ?array
{
    $pdo = db();
    return $pdo ? normalizarUsuario(read($pdo, 'usuarios', 'id = ?', [$id])) : null;
}

function criarUsuario(string $nome, string $email, string $senha, bool $localizacao = false): bool
{
    $pdo = db();
    if (!$pdo) {
        throw new RuntimeException('Sem conexão com o banco de dados: ' . ($GLOBALS['erro_banco'] ?? 'motivo desconhecido'));
    }
    try {
        create($pdo, 'usuarios', [
            'nome'        => $nome,
            'email'       => $email,
            'senha'       => password_hash($senha, PASSWORD_DEFAULT),
            'admin'       => 0,
            'localizacao' => (int) $localizacao,
        ]);
        return true;
    } catch (PDOException $e) {
       
        $duplicadoNoEmail = ($e->errorInfo[1] ?? 0) === 1062
            && str_contains($e->getMessage(), 'email');
        if ($duplicadoNoEmail) {
            return false;
        }
        error_log('Erro ao criar usuário: ' . $e->getMessage());
        throw $e; 
    }
}

function atualizarLocalizacao(int $id, bool $permitir): bool
{
    $pdo = db();
    if (!$pdo) {
        return false;
    }
   
    update($pdo, 'usuarios', ['localizacao' => (int) $permitir], 'id = ?', [$id]);
    return true;
}


function garantirAdmin(): void
{
    $pdo = db();
    if (!$pdo) {
        return;
    }

    $email = getenv('ADMIN_EMAIL') ?: 'admin@roboparts.com';
    $senha = getenv('ADMIN_SENHA') ?: 'troque-esta-senha';

    if (read($pdo, 'usuarios', 'email = ?', [$email]) !== false) {
        return; 
    }

    try {
        create($pdo, 'usuarios', [
            'nome'        => 'Administrador',
            'email'       => $email,
            'senha'       => password_hash($senha, PASSWORD_DEFAULT),
            'admin'       => 1,
            'localizacao' => 0,
        ]);
    } catch (PDOException $e) {
        if ($e->getCode() !== '23000') { 
            throw $e;
        }
    }
}