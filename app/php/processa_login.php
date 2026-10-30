<?php
declare(strict_types=1);

require_once __DIR__ . '/../init.php'; // o init.php já inicia a sessão

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

try {
    $stmt = $pdo->prepare('SELECT id, nome, senha FROM usuarios WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        session_regenerate_id(true);

        $_SESSION['usuario_id']   = (int) $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];

        header('Location: ../index.php');
        exit;
    }

    voltar_com_erro(['E-mail ou senha incorretos.'], ['email' => $email]);

} catch (PDOException $e) {
    error_log($e->getMessage());
    voltar_com_erro(['Erro ao fazer login. Tente novamente mais tarde.']);
}