<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/mensagens.php';
exigir_acesso();

$assuntos = assuntos_contato();
$usuario  = logado() ? buscarUsuarioPorId((int) $_SESSION['usuario_id']) : null;

$valores = [
    'nome'     => $usuario['nome']  ?? '',
    'email'    => $usuario['email'] ?? '',
    'assunto'  => '',
    'mensagem' => '',
];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    foreach ($valores as $campo => $_) {
        $valores[$campo] = trim((string) ($_POST[$campo] ?? ''));
    }

    // Campo escondido: só robôs preenchem. Finge sucesso e descarta.
    if (trim((string) ($_POST['site'] ?? '')) !== '') {
        flash('aviso', 'Mensagem enviada! Responderemos em breve.');
        ir('contato.php');
    }

    if ($valores['nome'] === '' || mb_strlen($valores['nome']) > 100) {
        $erros['nome'] = 'Informe seu nome (até 100 caracteres).';
    }
    if (!filter_var($valores['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($valores['email']) > 150) {
        $erros['email'] = 'Informe um e-mail válido.';
    }
    if (!isset($assuntos[$valores['assunto']])) {
        $erros['assunto'] = 'Escolha um assunto.';
    }
    $tam = mb_strlen($valores['mensagem']);
    if ($tam < 10 || $tam > 2000) {
        $erros['mensagem'] = 'A mensagem deve ter entre 10 e 2000 caracteres.';
    }

    // Limite simples contra envios em sequência
    if (!$erros && time() - (int) ($_SESSION['contato_ultimo'] ?? 0) < 15) {
        $erros['geral'] = 'Aguarde alguns segundos antes de enviar outra mensagem.';
    }

    if (!$erros) {
        if (mensagem_salvar($valores)) {
            $_SESSION['contato_ultimo'] = time();
            flash('aviso', 'Mensagem enviada! Responderemos em breve.');
            ir('contato.php');
        }
        $erros['geral'] = 'Não foi possível enviar agora. Tente novamente ou fale pelo WhatsApp.';
    }
}

$whats    = preg_replace('/\D/', '', LOJA_TELEFONE);
$whatsUrl = 'https://wa.me/' . $whats . '?text=' . rawurlencode('Olá! Vim pelo site da ' . LOJA_NOME . '.');

$titulo = 'Contato';
require __DIR__ . '/includes/header.php';
?>

<main class="inst contato-pagina">

    <section class="inst-hero inst-hero-baixo contato-hero">
        <div class="inst-hero-in">
            <p class="inst-eyebrow">Contato</p>
            <h1>Fale com a <?= e(LOJA_NOME) ?></h1>
            <p class="inst-lead">
                Dúvidas sobre um robô, orçamento para a sua empresa ou ajuda com o pedido?
                Escolha o canal que for melhor para você.
            </p>
        </div>
    </section>

    <div class="inst-secao contato-layout">

        <section class="contato-form-bloco" aria-labelledby="titulo-form">
            <h2 id="titulo-form">Envie uma mensagem</h2>
            <p class="texto-suave">Os campos marcados com * são obrigatórios.</p>

            <?php if (!empty($erros['geral'])): ?>
                <p class="contato-erro-geral" role="alert"><?= e($erros['geral']) ?></p>
            <?php endif; ?>

            <form class="contato-form" method="post" action="<?= e(pg('contato.php')) ?>" novalidate>
                <?= csrf_campo() ?>

                <div class="campo <?= isset($erros['nome']) ? 'campo-erro' : '' ?>">
                    <label for="nome">Nome *</label>
                    <input type="text" id="nome" name="nome" maxlength="100" required
                           autocomplete="name" value="<?= e($valores['nome']) ?>"
                           <?= isset($erros['nome']) ? 'aria-invalid="true" aria-describedby="erro-nome"' : '' ?>>
                    <?php if (isset($erros['nome'])): ?><small id="erro-nome"><?= e($erros['nome']) ?></small><?php endif; ?>
                </div>

                <div class="campo <?= isset($erros['email']) ? 'campo-erro' : '' ?>">
                    <label for="email">E-mail *</label>
                    <input type="email" id="email" name="email" maxlength="150" required
                           autocomplete="email" value="<?= e($valores['email']) ?>"
                           <?= isset($erros['email']) ? 'aria-invalid="true" aria-describedby="erro-email"' : '' ?>>
                    <?php if (isset($erros['email'])): ?><small id="erro-email"><?= e($erros['email']) ?></small><?php endif; ?>
                </div>

                <div class="campo <?= isset($erros['assunto']) ? 'campo-erro' : '' ?>">
                    <label for="assunto">Assunto *</label>
                    <select id="assunto" name="assunto" required
                            <?= isset($erros['assunto']) ? 'aria-invalid="true" aria-describedby="erro-assunto"' : '' ?>>
                        <option value="">Selecione…</option>
                        <?php foreach ($assuntos as $chave => $rotulo): ?>
                            <option value="<?= e($chave) ?>" <?= $valores['assunto'] === $chave ? 'selected' : '' ?>><?= e($rotulo) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($erros['assunto'])): ?><small id="erro-assunto"><?= e($erros['assunto']) ?></small><?php endif; ?>
                </div>

                <div class="campo <?= isset($erros['mensagem']) ? 'campo-erro' : '' ?>">
                    <label for="mensagem">Mensagem *</label>
                    <textarea id="mensagem" name="mensagem" rows="6" maxlength="2000" required
                              <?= isset($erros['mensagem']) ? 'aria-invalid="true" aria-describedby="erro-mensagem"' : '' ?>><?= e($valores['mensagem']) ?></textarea>
                    <?php if (isset($erros['mensagem'])): ?><small id="erro-mensagem"><?= e($erros['mensagem']) ?></small><?php endif; ?>
                </div>

                <!-- Armadilha para robôs: pessoas não veem nem preenchem este campo -->
                <div class="campo-isca" aria-hidden="true">
                    <label for="site">Não preencha este campo</label>
                    <input type="text" id="site" name="site" tabindex="-1" autocomplete="off">
                </div>

                <button class="btn btn-cheio btn-grande" type="submit">Enviar mensagem</button>
            </form>
        </section>

        <aside class="contato-lateral" aria-label="Outros canais">
            <div class="inst-card contato-canal">
                <p class="inst-rotulo">WhatsApp</p>
                <p class="contato-valor"><?= e(LOJA_TELEFONE) ?></p>
                <p class="texto-suave">Ideal para tirar dúvidas rápidas sobre produtos.</p>
                <a class="btn btn-cheio" href="<?= e($whatsUrl) ?>" target="_blank" rel="noopener">Abrir conversa</a>
            </div>

            <div class="inst-card contato-canal">
                <p class="inst-rotulo">E-mail</p>
                <p class="contato-valor"><a href="mailto:<?= e(LOJA_EMAIL) ?>"><?= e(LOJA_EMAIL) ?></a></p>
                <p class="texto-suave">Para orçamentos, documentos e detalhes técnicos.</p>
            </div>
        </aside>
    </div>


</main>

<?php require __DIR__ . '/includes/footer.php';
