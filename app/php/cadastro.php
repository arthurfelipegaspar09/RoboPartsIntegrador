<?php
declare(strict_types=1);

session_start();

$erros   = $_SESSION['erros'] ?? [];
$antigos = $_SESSION['antigos'] ?? [];
unset($_SESSION['erros'], $_SESSION['antigos']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastro</title>
</head>
<body>
    <h1>Cadastro de usuários</h1>

    <?php foreach ($erros as $erro): ?>
        <p style="color: red;"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endforeach; ?>

    <form action="processa_cadastro.php" method="post">
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" required maxlength="100"
               value="<?= htmlspecialchars($antigos['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required maxlength="150"
               value="<?= htmlspecialchars($antigos['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required minlength="6">

        <button type="submit">Cadastrar</button>
    </form>

    <p>Já tem conta? <a href="login.php">Entrar</a></p>
</body>
</html>