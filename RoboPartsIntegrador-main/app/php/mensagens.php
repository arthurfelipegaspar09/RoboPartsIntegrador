<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/mensagens.php';
exigir_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    mensagem_excluir((string) ($_POST['id'] ?? ''));
    flash('aviso', 'Mensagem excluída.');
    ir('mensagens.php');
}

$assuntos  = assuntos_contato();
$mensagens = array_reverse(mensagens_ler()); // mais recentes primeiro

$titulo = 'Mensagens';
require __DIR__ . '/includes/header.php';
?>

<main class="pagina pagina-estreita">
    <p class="trilha"><a href="<?= e(pg('admin.php')) ?>">Painel</a> / Mensagens</p>
    <h1>Mensagens de Contato</h1>
    <p class="texto-suave"><?= count($mensagens) ?> <?= count($mensagens) === 1 ? 'mensagem recebida' : 'mensagens recebidas' ?>.</p>

    <?php if (!$mensagens): ?>
        <p class="vazio-min">Nenhuma mensagem ainda.</p>
    <?php endif; ?>

    <?php foreach ($mensagens as $m): ?>
        <article class="msg-item">
            <header>
                <div>
                    <strong><?= e($m['nome'] ?? '') ?></strong>
                    <a href="mailto:<?= e($m['email'] ?? '') ?>"><?= e($m['email'] ?? '') ?></a>
                </div>
                <time datetime="<?= e($m['data'] ?? '') ?>">
                    <?= e(date('d/m/Y H:i', strtotime($m['data'] ?? 'now'))) ?>
                </time>
            </header>
            <p class="msg-assunto"><?= e($assuntos[$m['assunto'] ?? ''] ?? 'Outro assunto') ?></p>
            <p class="msg-texto"><?= nl2br(e($m['mensagem'] ?? '')) ?></p>
            <form method="post" onsubmit="return confirm('Excluir esta mensagem?')">
                <?= csrf_campo() ?>
                <input type="hidden" name="id" value="<?= e($m['id'] ?? '') ?>">
                <button class="del" type="submit">excluir</button>
            </form>
        </article>
    <?php endforeach; ?>
</main>

<?php require __DIR__ . '/includes/footer.php';
