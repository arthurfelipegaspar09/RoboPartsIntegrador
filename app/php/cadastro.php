<?php
declare(strict_types=1);

session_start();

$erros   = $_SESSION['erros'] ?? [];
$antigos = $_SESSION['antigos'] ?? [];

unset(
    $_SESSION['erros'],
    $_SESSION['antigos']
);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cadastro | RoboParts</title>

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

            <h1>Crie sua conta</h1>

            <p class="auth-sub">
                Cadastre-se para explorar nossos produtos.
            </p>

            <?php foreach ($erros as $erro): ?>
                <p class="msg msg-erro">
                    <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php endforeach; ?>

            <form action="processa_cadastro.php" method="post">

                <label for="nome">Nome</label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    required
                    maxlength="100"
                    autocomplete="name"
                    value="<?= htmlspecialchars($antigos['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >

                <label for="email">E-mail</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    maxlength="150"
                    autocomplete="email"
                    value="<?= htmlspecialchars($antigos['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >

                <label for="senha">Senha</label>

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    required
                    minlength="6"
                    autocomplete="new-password"
                >

                <button type="submit" class="btn-acessar">
                    Cadastrar
                </button>

            </form>

            <p class="auth-troca">
                Já tem conta?
                <a href="login.php">Entrar</a>
            </p>

        </section>

    </main>

</body>
</html>