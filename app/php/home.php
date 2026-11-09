<<<<<<< HEAD
=======

>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f
<?php

declare(strict_types=1);

<<<<<<< HEAD
=======
define('NA_RAIZ', false);

>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f
// Carregar inicialização da loja
require_once __DIR__ . '/includes/bootstrap.php';

// Sair da conta
if (isset($_GET['logout'])) {
    unset(
        $_SESSION['usuario_id'],
        $_SESSION['usuario_nome'],
        $_SESSION['usuario_admin'],
        $_SESSION['carrinho'],
        $_SESSION['pedido_ok'],
        $_SESSION['csrf']
    );

    session_regenerate_id(true);

    header('Location: /php/login.php');
    exit;
}

// Verificar acesso
exigir_acesso();

// Dados da página
$categorias = categorias();

$temHero = is_file(__DIR__ . '/../img/hero.png');

$nCats = count($categorias);

$h3dCats = $nCats
    ? $nCats . ($nCats === 1 ? ' categoria' : ' categorias') . ' de robôs'
    : 'Robôs e componentes';

$titulo = 'Início';

// Cabeçalho
require __DIR__ . '/includes/header.php';

?>

<main>

    <!-- HERO 3D -->
    <section class="h3d" id="hero3d" aria-label="Apresentação da loja">

        <div class="h3d-cena">
            <div class="h3d-cena-pos">
<<<<<<< HEAD
                <canvas class="h3d-canvas" role="img" aria-label="Robô 3D interativo">
=======
                <canvas
                    class="h3d-canvas"
                    role="img"
                    aria-label="Robô 3D interativo">
>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f
                </canvas>
            </div>
        </div>

        <div class="h3d-luz" aria-hidden="true"></div>

        <div class="h3d-loader" role="status">
            <span class="h3d-spinner" aria-hidden="true"></span>
            <span class="h3d-loader-txt">Carregando cena 3D</span>
        </div>

        <div class="h3d-texto">
            <div class="h3d-texto-in">

                <h1 class="h3d-titulo">
                    <span class="h3d-l1">Robôs para</span>
                    <span class="h3d-l2">cada tarefa.</span>
                </h1>

                <p class="h3d-desc">
                    Industriais, humanoides, sociais, agrícolas e colaborativos.
                    Escolha a categoria e monte o seu projeto de robótica com a RoboParts.
                </p>

                <div class="h3d-botoes">

                    <a class="h3d-btn h3d-btn-1" href="#catalogo">
                        Ver catálogo

<<<<<<< HEAD
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6" />
                        </svg>
                    </a>

                    <a class="h3d-btn h3d-btn-2" href="<?= e(pg('produtos.php')) ?>">
=======
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6"/>
                        </svg>
                    </a>

                    <a
                        class="h3d-btn h3d-btn-2"
                        href="<?= e(pg('produtos.php')) ?>">
>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f
                        Todos os produtos
                    </a>

                </div>

                <div class="h3d-feats">

                    <div class="h3d-feat">
                        <strong>Catálogo online</strong>
                        <span><?= e($h3dCats) ?></span>
                    </div>

                    <span class="h3d-sep" aria-hidden="true"></span>

                    <div class="h3d-feat">
                        <strong>Atendimento</strong>
                        <span>WhatsApp e e-mail</span>
                    </div>

                </div>

            </div>
        </div>

    </section>

    <script type="module" src="/js/hero3d.js"></script>

    <!-- CATEGORIAS -->
    <section class="secao" id="categorias">

        <div class="secao-in">

            <div class="secao-topo">
                <h2>Categorias</h2>
            </div>

            <div class="grade-categorias">

                <?php foreach ($categorias as $i => $c): ?>
                    <?php $img = imagem_categoria($c); ?>

<<<<<<< HEAD
                    <a class="cat-card" href="<?= e(pg('categoria.php')) ?>?c=<?= e($c['slug']) ?>">

                        <?php if ($img): ?>

                            <img src="<?= e($img) ?>" alt="" loading="lazy">
=======
                    <a
                        class="cat-card"
                        href="<?= e(pg('categoria.php')) ?>?c=<?= e($c['slug']) ?>">

                        <?php if ($img): ?>

                            <img
                                src="<?= e($img) ?>"
                                alt=""
                                loading="lazy">
>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f

                        <?php else: ?>

                            <span class="cat-num">
                                <?= sprintf('%02d', $i + 1) ?>
                            </span>

                        <?php endif; ?>

                        <strong><?= e($c['nome']) ?></strong>

                        <small>
                            <?php
                            $n = contar_produtos($c['id']);

                            echo $n . (
                                $n === 1
<<<<<<< HEAD
                                ? ' produto'
                                : ' produtos'
=======
                                    ? ' produto'
                                    : ' produtos'
>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f
                            );
                            ?>
                        </small>

                    </a>

                <?php endforeach; ?>

            </div>

            <?php if (admin()): ?>

                <p class="acao-admin">
                    <a href="<?= e(pg('nova_categoria.php')) ?>">
                        + Nova categoria
                    </a>
                </p>

            <?php endif; ?>

        </div>

    </section>

    <!-- CATÁLOGO -->
    <section class="secao secao-cinza" id="catalogo">

        <div class="secao-in">

            <div class="secao-topo">

                <h2>Catálogo</h2>

<<<<<<< HEAD
                <a class="ver-todos" href="<?= e(pg('produtos.php')) ?>">
=======
                <a
                    class="ver-todos"
                    href="<?= e(pg('produtos.php')) ?>">
>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f
                    Ver todos os produtos
                </a>

            </div>

            <?php foreach ($categorias as $c): ?>

                <?php $lista = produtos_recentes(4, $c['id']); ?>

                <div class="vitrine">

                    <div class="vitrine-topo">

                        <div>

                            <h3>
<<<<<<< HEAD
                                <a href="<?= e(pg('categoria.php')) ?>?c=<?= e($c['slug']) ?>">
=======
                                <a
                                    href="<?= e(pg('categoria.php')) ?>?c=<?= e($c['slug']) ?>">
>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f
                                    <?= e($c['nome']) ?>
                                </a>
                            </h3>

                            <?php if (!empty($c['descricao'])): ?>
                                <p><?= e($c['descricao']) ?></p>
                            <?php endif; ?>

                        </div>

<<<<<<< HEAD
                        <a class="ver-todos" href="<?= e(pg('categoria.php')) ?>?c=<?= e($c['slug']) ?>">
=======
                        <a
                            class="ver-todos"
                            href="<?= e(pg('categoria.php')) ?>?c=<?= e($c['slug']) ?>">
>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f
                            Ver categoria
                        </a>

                    </div>

                    <?php if ($lista): ?>

                        <div class="grade-produtos">

                            <?php foreach ($lista as $p): ?>
                                <?= card_produto($p) ?>
                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <div class="vazio">

                            <p>Nenhum produto nesta categoria ainda.</p>

                            <?php if (admin()): ?>

<<<<<<< HEAD
                                <a class="btn btn-cheio" href="<?= e(pg('criar_produto.php')) ?>?c=<?= e($c['slug']) ?>">
=======
                                <a
                                    class="btn btn-cheio"
                                    href="<?= e(pg('criar_produto.php')) ?>?c=<?= e($c['slug']) ?>">
>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f
                                    + Novo produto
                                </a>

                            <?php endif; ?>

                        </div>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

            <?php if (!$categorias): ?>

                <div class="vazio">

                    <h3>Nenhuma categoria cadastrada</h3>

                    <?php if (admin()): ?>

<<<<<<< HEAD
                        <a class="btn btn-cheio" href="<?= e(pg('nova_categoria.php')) ?>">
=======
                        <a
                            class="btn btn-cheio"
                            href="<?= e(pg('nova_categoria.php')) ?>">
>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f
                            + Nova categoria
                        </a>

                    <?php endif; ?>

                </div>

            <?php endif; ?>

        </div>

    </section>

    <!-- ATENDIMENTO -->
    <section class="chamada">

        <div class="chamada-in">

            <div>
                <h2>Precisa de ajuda para escolher?</h2>
                <p>Fale com a nossa equipe pelo WhatsApp ou por e-mail.</p>
            </div>

            <div class="chamada-botoes">

<<<<<<< HEAD
                <a class="btn btn-cheio" href="https://wa.me/<?= e(preg_replace('/\D/', '', LOJA_TELEFONE)) ?>">
                    WhatsApp
                </a>

                <a class="btn btn-escuro" href="mailto:<?= e(LOJA_EMAIL) ?>">
=======
                <a
                    class="btn btn-cheio"
                    href="https://wa.me/<?= e(preg_replace('/\D/', '', LOJA_TELEFONE)) ?>">
                    WhatsApp
                </a>

                <a
                    class="btn btn-escuro"
                    href="mailto:<?= e(LOJA_EMAIL) ?>">
>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f
                    E-mail
                </a>

            </div>

        </div>

    </section>

</main>

<!-- CHATBOT -->
<link rel="stylesheet" href="/css/chatbot.css">

<<<<<<< HEAD
<button class="minichat-botao" aria-label="Abrir chat">
=======
<button
    class="minichat-botao"
    aria-label="Abrir chat">
>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f
    💬
</button>

<div class="minichat" data-telefone="<?= e(LOJA_TELEFONE) ?>">

    <div class="minichat-topo">
        <span>Fale com a gente</span>

<<<<<<< HEAD
        <button class="minichat-fechar" aria-label="Fechar chat">
=======
        <button
            class="minichat-fechar"
            aria-label="Fechar chat">
>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f
            ✕
        </button>
    </div>

    <div class="chat"></div>

    <div class="minichat-form">

<<<<<<< HEAD
        <input type="text" class="message" placeholder="Digite sua mensagem..." autocomplete="off">
=======
        <input
            type="text"
            class="message"
            placeholder="Digite sua mensagem..."
            autocomplete="off">
>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f

        <button class="submit_message">
            Enviar
        </button>

    </div>

</div>

<script src="/js/chatbot.js"></script>

<?php

// Rodapé
require __DIR__ . '/includes/footer.php';

<<<<<<< HEAD
?>
=======
?>
>>>>>>> d546d0e74b5e772a6d39373cf86f40116b7eae8f
