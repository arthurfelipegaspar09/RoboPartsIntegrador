<?php
declare(strict_types=1);

require_once __DIR__ . '/../init.php';

function voltar_com_erro(array $erros, array $antigos = []): void
{
    $_SESSION['erros']   = $erros;
    $_SESSION['antigos'] = $antigos;
    header('Location: cadastro.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cadastro.php');
    exit;
}

$nome  = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

$antigos = ['nome' => $nome, 'email' => $email];
$erros   = [];

if ($nome === '') {
    $erros[] = 'Informe o nome.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'E-mail inválido.';
}
if (strlen($senha) < 6) {
    $erros[] = 'A senha deve ter pelo menos 6 caracteres.';
}

if ($erros) {
    voltar_com_erro($erros, $antigos);
}

try {
    create($pdo, 'usuarios', [
        'nome'  => $nome,
        'email' => $email,
        'senha' => password_hash($senha, PASSWORD_DEFAULT),
    ]);

    $_SESSION['sucesso'] = 'Cadastro realizado! Faça login para continuar.';
    header('Location: login.php');
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());

    if ($e->getCode() === '23000') {
        voltar_com_erro(['Este e-mail já está cadastrado.'], $antigos);
    }

    voltar_com_erro(['Erro ao cadastrar. Tente novamente mais tarde.'], $antigos);
}