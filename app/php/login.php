<?php
declare(strict_types=1);

session_start();

$erros   = $_SESSION['erros'] ?? [];
$sucesso = $_SESSION['sucesso'] ?? '';
$antigos = $_SESSION['antigos'] ?? [];
unset($_SESSION['erros'], $_SESSION['sucesso'], $_SESSION['antigos']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>

    <?php if ($sucesso !== ''): ?>
        <p style="color: green;"><?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php foreach ($erros as $erro): ?>
        <p style="color: red;"><?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endforeach; ?>

    <form action="processa_login.php" method="post">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required
               value="<?= htmlspecialchars($antigos['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit">Entrar</button>
    </form>

    <p>Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
</body>
</html>