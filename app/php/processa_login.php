<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php'; // config.php carrega init.php (sessão + $pdo)

function voltar_com_erro(array $erros, array $antigos = []): void
{
    $_SESSION['erros']   = $erros;
    $_SESSION['antigos'] = $antigos;
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
    voltar_com_erro(['Informe e-mail e senha válidos.'], ['email' => $email]);
}

// Se o banco não conectou, mostra o motivo real em vez de "senha incorreta"
if (!db()) {
    voltar_com_erro(['Sem conexão com o banco de dados: ' . ($GLOBALS['erro_banco'] ?? 'motivo desconhecido')], ['email' => $email]);
}

garantirAdmin(); // cria o admin pré-programado, se ainda não existir

// Procura o usuário no banco
$usuario = buscarUsuarioPorEmail($email);

if ($usuario && password_verify($senha, $usuario['senha'])) {
    session_regenerate_id(true);

    $_SESSION['usuario_id']    = (int) $usuario['id'];
    $_SESSION['usuario_nome']  = $usuario['nome'];
    $_SESSION['usuario_admin'] = !empty($usuario['admin']);

    
    header('Location: ../index.php');
    exit;
}
voltar_com_erro(['E-mail ou senha incorretos.'], ['email' => $email]);