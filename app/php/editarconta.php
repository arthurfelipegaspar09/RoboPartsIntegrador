<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ir('perfil.php');
}

csrf_validar();

$id         = (int) $_SESSION['usuario_id'];
$nome       = trim($_POST['nome'] ?? '');
$senhaAtual = $_POST['senha_atual'] ?? '';
$novaSenha  = $_POST['nova_senha'] ?? '';
$confirmar  = $_POST['confirmar_senha'] ?? '';

$pdo     = db();
$usuario = buscarUsuarioPorId($id);

if (!$pdo || !$usuario) {
    flash('aviso', 'Não foi possível editar a conta.');
    ir('perfil.php');
}

if (!password_verify($senhaAtual, $usuario['senha'])) {
    flash('aviso', 'Senha atual incorreta. Nada foi alterado.');
    ir('perfil.php');
}

if (mb_strlen($nome) < 2 || mb_strlen($nome) > 100) {
    flash('aviso', 'Informe um nome com 2 a 100 caracteres.');
    ir('perfil.php');
}

if ($novaSenha !== '' && mb_strlen($novaSenha) < 6) {
    flash('aviso', 'A nova senha deve ter pelo menos 6 caracteres.');
    ir('perfil.php');
}

if ($novaSenha !== '' && $novaSenha !== $confirmar) {
    flash('aviso', 'A confirmação da nova senha não confere.');
    ir('perfil.php');
}

$dados = ['nome' => $nome];
if ($novaSenha !== '') {
    $dados['senha'] = password_hash($novaSenha, PASSWORD_DEFAULT);
}

try {
    update($pdo, 'usuarios', $dados, 'id = ?', [$id]);
} catch (PDOException $e) {
    error_log('Erro ao editar usuário: ' . $e->getMessage());
    flash('aviso', 'Não foi possível salvar. Tente novamente.');
    ir('perfil.php');
}

$_SESSION['usuario_nome'] = $nome;
flash('aviso', 'Dados atualizados com sucesso.');
ir('perfil.php');
