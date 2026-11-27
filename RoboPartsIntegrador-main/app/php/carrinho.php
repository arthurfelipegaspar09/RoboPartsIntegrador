<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$cat = catalogo();

$produtos = [];
foreach ($cat['produtos'] ?? [] as $p) {
    $produtos[(string) $p['id']] = $p;
}
function destino_seguro(string $url, string $padrao): string
{
    return preg_match('~^(\.\./)?[a-z_]+\.php(\?[A-Za-z0-9_=&%.+\-]*)?$~', $url) ? $url : $padrao;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $acao   = $_POST['acao'] ?? '';
    $voltar = destino_seguro((string) ($_POST['voltar'] ?? ''), 'carrinho.php');


    if ($acao === 'pagar') {
        $itens = $_SESSION['carrinho'] ?? [];
        $totalPedido = 0;

        foreach ($itens as $idItem => $qtd) {
            if (isset($produtos[(string) $idItem])) {
                $totalPedido += (float) $produtos[(string) $idItem]['preco'] * (int) $qtd;
            }
        }

        if ($totalPedido > 0) {
            $_SESSION['pedido_ok'] = $totalPedido;
            $_SESSION['carrinho'] = [];
        }

        ir('carrinho.php');
    }

    $id = (string) ($_POST['id'] ?? '');

    if (isset($produtos[$id])) {
        $atual = (int) ($_SESSION['carrinho'][$id] ?? 0);

        switch ($acao) {
            case 'adicionar':
                $qtd = min(20, max(1, (int) ($_POST['qtd'] ?? 1)));
                $_SESSION['carrinho'][$id] = $atual + $qtd;
                flash('aviso', $produtos[$id]['nome'] . ' foi adicionado ao carrinho.');
                break;

            case 'mais':
                $_SESSION['carrinho'][$id] = $atual + 1;
                if ($voltar !== 'carrinho.php') {
                    flash('aviso', $produtos[$id]['nome'] . ' foi adicionado ao carrinho.');
                }
                break;

            case 'menos':
                if ($atual > 1) {
                    $_SESSION['carrinho'][$id] = $atual - 1;
                } else {
                    unset($_SESSION['carrinho'][$id]);
                }
                break;

            case 'remover':
                unset($_SESSION['carrinho'][$id]);
                break;
        }
    }

    ir($voltar);
}


$pedidoOk = $_SESSION['pedido_ok'] ?? null;
unset($_SESSION['pedido_ok']);

$carrinho = $_SESSION['carrinho'] ?? [];
$total = 0;

$titulo = 'Meu carrinho';
require __DIR__ . '/includes/header.php';

$total_carrinho = [];
?>

<main class="pagina pagina-estreita">
    <h1>Meu carrinho</h1>

    <?php if ($pedidoOk !== null): ?>
        <div class="pedido-ok">
            <h3>Pedido realizado com sucesso!</h3>
            <p>Total pago: <strong><?= preco($pedidoOk) ?></strong></p>
        </div>
    <?php endif; ?>

    <?php if (empty($carrinho)): ?>

        <div class="vazio">
            <h3>Seu carrinho está vazio</h3>
            <p>Escolha seus produtos e eles aparecem aqui.</p>
            <a class="btn btn-cheio" href="produtos.php">Ver produtos</a>
        </div>

    <?php else: ?>

        <div class="carrinho-lista">
            <?php foreach ($carrinho as $id => $quantidade): ?>
                <?php
                $produto = $produtos[(string) $id] ?? null;
                if (!$produto) continue;

                $quantidade = (int) $quantidade;
                $subtotal   = (float) $produto['preco'] * $quantidade;
                $total     += $subtotal;
                ?>
                <article class="item-carrinho">
                    <a href="produto.php?id=<?= e((string) $id) ?>">
                        <?= img_produto($produto, 'item-foto') ?>
                    </a>

                    <div class="item-info">
                        <h3><a href="produto.php?id=<?= e((string) $id) ?>"><?= e($produto['nome']) ?></a></h3>
                        <span class="preco-unit"><?= preco($produto['preco']) ?> cada</span>

                        <form method="post" class="qtd-controle">
                            <?= csrf_campo() ?>
                            <input type="hidden" name="id" value="<?= e((string) $id) ?>">

                            <button type="submit" name="acao" value="menos" aria-label="Diminuir">−</button>
                            <span class="qtd-num"><?= $quantidade ?></span>
                            <button type="submit" name="acao" value="mais" aria-label="Aumentar">+</button>

                            <button type="submit" name="acao" value="remover" class="btn-remover">Remover</button>
                        </form>
                    </div>

                    <div class="item-subtotal"><?= preco($subtotal) ?></div>
                </article>
            <?php 

                $total_carrinho[] = [
                    "imagem" => img_produto($produto, 'item-foto'),
                    "nome" => $produto["nome"],
                    "quantidade" => $quantidade,
                    "total" => $subtotal,
                    "id" => $id
                ];

                endforeach;
            ?>
        </div>

        <div class="carrinho-total">
            <span>Total</span>
            <strong><?= preco($total) ?></strong>
        </div>

        <form action="./processar_compra.php" method="post" class="form-pagar">
            <?= csrf_campo(); ?>
            
            <?php $total_produtos = count($total_carrinho); echo "<input type='hidden' name='total' value='$total_produtos'>" ?>

            <?php foreach ($total_carrinho as $id => $pedido) {
                echo "<input type='hidden' name='nome$id' value='{$pedido["nome"]}'>";
                echo "<input type='hidden' name='quantidade$id' value='{$pedido["quantidade"]}'>";
                echo "<input type='hidden' name='total$id' value='{$pedido["total"]}'>";
                echo "<input type='hidden' name='id$id' value='{$pedido["id"]}'>";
            } ?>

            <a class="btn" href="produtos.php">Continuar comprando</a>
            <button type="submit" name="acao" value="pagar" class="btn btn-cheio">Finalizar compra</button>
        </form>

    <?php endif; ?>
</main>

<main class="pagina pagina-estreita">
    <h1>Histórico de compras</h1>

    <?php if (empty($_SESSION["historico"])): ?>
    
        <div class="vazio">
            <h3>Histórico vazio</h3>
            <p>Nenhuma compra foi realizada.</p>
        </div>

    <?php else: ?>

        <div class="borda_historico">
            <?php foreach ($_SESSION["historico"] as $id => $info): ?>
                <div class="item_historico">
                    <h3><a href="produto.php?id=<?= e((string) $info["id"]) ?>"> <?= $info["nome"]; ?> </a></h3>

                    <span>
                        <h4>Valor total - R$<?= number_format($info["total"], 2, ".", ","); ?> </h4>
                        <h4>Quantidade de Produtos: <?= $info["quantidade"]; ?> </h4>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</main>

<?php require __DIR__ . '/includes/footer.php';
