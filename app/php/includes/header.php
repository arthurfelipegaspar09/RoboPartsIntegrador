<?php

$css            = $css ?? [];
$categoriaAtual = $categoriaAtual ?? null;
$qtdCarrinho    = carrinho_qtd();
$aviso          = flash('aviso');
$paginaAtual    = basename($_SERVER['SCRIPT_NAME']);
$busca          = trim((string) ($_GET['q'] ?? ''));

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($titulo) ?> | <?= e(LOJA_NOME) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="icon" type="image/svg+xml" href="<?= e(asset('img/favicon.svg')) ?>">

    <link rel="stylesheet" href="<?= e(asset('css/estilo.css')) ?>">

    <?php foreach ($css as $arquivo): ?>
        <link rel="stylesheet" href="<?= e(asset('css/' . $arquivo)) ?>">
    <?php endforeach; ?>

    <script>
        (function () {
            var raiz = document.documentElement;
            var salvo = null;

            try {
                salvo = localStorage.getItem('tema');
            } catch (e) {}

            raiz.dataset.tema = salvo ||
                (matchMedia('(prefers-color-scheme: dark)').matches
                    ? 'escuro'
                    : 'claro');
        })();
    </script>

</head>

<body>

<header class="topo">
    <div class="topo-in">

        <a class="logo" href="<?= e(asset('index.php')) ?>">
            <img src="<?= e(asset('img/logo.svg')) ?>" alt="">
            <span><?= e(LOJA_NOME) ?></span>
        </a>

        <form class="busca" method="get" action="<?= e(pg('produtos.php')) ?>" role="search">

            <input
                type="search"
                name="q"
                value="<?= e($busca) ?>"
                placeholder="Buscar robôs e componentes"
                aria-label="Buscar"
            >

            <button type="submit">Buscar</button>

        </form>

        <div class="topo-conta">

            <?php if (logado()): ?>

                <div class="menu-usuario">

                    <button class="user-button" type="button" onclick="toggleNav()">
                        <?= e(explode(' ', trim($_SESSION['usuario_nome'] ?? ''))[0]) ?>
                    </button>

                    <nav class="navbar" aria-label="Conta">

                        <a href="<?= e(pg('perfil.php')) ?>">Perfil</a>

                        <a href="<?= e(pg('carrinho.php')) ?>">Compras</a>

                        <a href="<?= e(pg('sobre.php')) ?>">Sobre</a>

                        <?php if (admin()): ?>
                            <a href="<?= e(pg('admin.php')) ?>">Painel</a>
                        <?php endif; ?>

                        <button type="button" class="tema-btn" id="temaBtn">
                            Tema escuro
                        </button>

                        <a href="<?= e(asset('index.php')) ?>?logout">Sair</a>

                    </nav>

                </div>

            <?php else: ?>

                <a href="<?= e(pg('login.php')) ?>">Entrar</a>

                <a href="<?= e(pg('cadastro.php')) ?>">Criar conta</a>

            <?php endif; ?>

            <a class="carrinho" href="<?= e(pg('carrinho.php')) ?>">
                Carrinho <span><?= $qtdCarrinho ?></span>
            </a>

        </div>

    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {

    var raiz = document.documentElement;
    var botao = document.getElementById('temaBtn');

    if (!botao) return;

    function mostrar() {

        var escuro = raiz.dataset.tema === 'escuro';

        botao.textContent = escuro ? 'Tema claro' : 'Tema escuro';

        botao.setAttribute(
            'aria-label',
            escuro ? 'Mudar para o tema claro' : 'Mudar para o tema escuro'
        );

    }

    botao.addEventListener('click', function () {

        raiz.dataset.tema =
            raiz.dataset.tema === 'escuro' ? 'claro' : 'escuro';

        try {
            localStorage.setItem('tema', raiz.dataset.tema);
        } catch (e) {}

        mostrar();

    });

    mostrar();

});
</script>

<nav class="menu-categorias" aria-label="Categorias">

    <div class="menu-in">

        <a
            href="<?= e(pg('produtos.php')) ?>"
            class="<?= $paginaAtual === 'produtos.php' ? 'ativo' : '' ?>"
        >
            Todos os produtos
        </a>

        <?php foreach (categorias() as $c): ?>

            <a
                href="<?= e(pg('categoria.php')) ?>?c=<?= e($c['slug']) ?>"
                class="<?= $categoriaAtual && $categoriaAtual['id'] === $c['id'] && $paginaAtual === 'categoria.php' ? 'ativo' : '' ?>"
            >
                <?= e($c['nome']) ?>
            </a>

        <?php endforeach; ?>

    </div>

</nav>

<?php if ($aviso): ?>

    <p class="aviso">
        <?= e($aviso) ?>
    </p>

<?php endif; ?>