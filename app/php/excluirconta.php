<?php

require_once __DIR__ . '/PASTA_DO_PERFIL/includes/bootstrap.php'; 
exigir_login();

$perfil = 'PASTA_DO_PERFIL/perfil.php'; 


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $perfil);
    exit;
}

csrf_validar();

$id    = (int) $_SESSION['usuario_id'];
$senha = $_POST['senha'] ?? '';

$pdo     = db();
$usuario = buscarUsuarioPorId($id);


if (!$pdo || !$usuario) {
    flash('aviso', 'Não foi possível excluir a conta.');
    header('Location: ' . $perfil);
    exit;
}

if (!password_verify($senha, $usuario['senha'])) {
    flash('aviso', 'Senha incorreta. Sua conta não foi excluída.');
    header('Location: ' . $perfil);
    exit;
}

if ($usuario['admin']) {
    flash('aviso', 'A conta de administrador não pode ser excluída por aqui.');
    header('Location: ' . $perfil);
    exit;
}

// Exclui do banco
try {
    $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = ?');
    $stmt->execute([$id]);
} catch (PDOException $e) {
    error_log('Erro ao excluir usuário: ' . $e->getMessage());
    flash('aviso', 'Não foi possível excluir a conta. Tente novamente.');
    header('Location: ' . $perfil);
    exit;
}

// Encerra a sessão e volta para a home
$_SESSION = [];
session_destroy();

header('Location: index.php?conta=excluida');
exit;