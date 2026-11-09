<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$erros   = $_SESSION['erros'] ?? [];
$sucesso = $_SESSION['sucesso'] ?? '';
$antigos = $_SESSION['antigos'] ?? [];

unset(
    $_SESSION['erros'],
    $_SESSION['sucesso'],
    $_SESSION['antigos']
);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login | RoboParts</title>

    <link rel="stylesheet" href="../css/login.css">
</head>

<body>

    <main class="auth">

        <section class="auth-arte">

            <div class="auth-marca">
                <img src="../img/logo.svg" alt="">
                <span>RoboParts</span>
            </div>

            <h2 class="auth-slogan">
                As peças certas para suas criações.
            </h2>

        </section>

        <section class="auth-form">

            <a class="auth-logo" href="../index.php">
                <img src="../img/logo.svg" alt="">
                <span>RoboParts</span>
            </a>

            <h1>Bem-vindo de volta</h1>

            <p class="auth-sub">
                Entre na sua conta para continuar.
            </p>

            <?php if ($sucesso !== ''): ?>
                <p class="msg msg-sucesso">
                    <?= e($sucesso) ?>
                </p>
            <?php endif; ?>

            <?php foreach ($erros as $erro): ?>
                <p class="msg msg-erro">
                    <?= e($erro) ?>
                </p>
            <?php endforeach; ?>

            <form action="processa_login.php" method="post">

                <label for="email">E-mail</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    autocomplete="email"
                    value="<?= e($antigos['email'] ?? '') ?>"
                >

                <label for="senha">Senha</label>

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    required
                    autocomplete="current-password"
                >

                <button type="submit" class="btn-acessar">
                    Entrar
                </button>

            </form>

            <p class="auth-troca">
                Não tem conta?
                <a href="cadastro.php">Cadastre-se</a>
            </p>

        </section>

    </main>

</body>
</html>