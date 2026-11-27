<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_admin();

$titulo = 'Painel';
$css    = ['admin.css'];
require __DIR__ . '/includes/header.php';
?>
<main class="admin-main admin-largo">
    <h1>Painel do admin</h1>
    <p class="admin-acoes">
        <a class="btn btn-cheio" href="nova_categoria.php">+ Nova categoria</a>
        <a class="btn btn-cheio" href="criar_produto.php">+ Novo produto</a>
        <a class="btn btn-cheio" href="financeiro.php">Gestão financeira</a>
    </p>

    <?php if (!categorias()): ?>
        <p class="vazio-min">Nenhuma categoria. <a href="nova_categoria.php">Criar a primeira</a></p>
    <?php endif; ?>

    <?php foreach (categorias() as $c): ?>
        <section class="admin-bloco">
            <div class="admin-linha admin-cat">
                <a href="categoria.php?c=<?= e($c['slug']) ?>"><strong><?= e($c['nome']) ?></strong></a>
                <span class="adm-acoes">
                    <a href="criar_produto.php?c=<?= e($c['slug']) ?>">+ produto</a>
                    <form method="post" action="excluir.php" onsubmit="return confirm('Excluir a categoria e todos os seus produtos?')">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="tipo" value="categoria">
                        <input type="hidden" name="id" value="<?= e($c['id']) ?>">
                        <button class="del" type="submit">excluir</button>
                    </form>
                </span>
            </div>

            <?php $produtos = produtos_da_categoria($c['id']); ?>
            <?php if (!$produtos): ?>
                <p class="vazio-min">Nenhum produto nesta categoria.</p>
            <?php endif; ?>

            <?php foreach ($produtos as $p): ?>
                <div class="admin-linha admin-prod">
                    <span><a href="produto.php?id=<?= e($p['id']) ?>"><?= e($p['nome']) ?></a> · <?= preco($p['preco']) ?></span>
                    <form method="post" action="excluir.php" onsubmit="return confirm('Excluir este produto?')">
                        <?= csrf_campo() ?>
                        <input type="hidden" name="tipo" value="produto">
                        <input type="hidden" name="id" value="<?= e($p['id']) ?>">
                        <button class="del" type="submit">excluir</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>
    
    <br>
    <?php $renda = $_SESSION["infoSite"]["renda"] ?? 0; ?>
    <h2>Renda Total da Empresa -  R$<?= number_format($renda, 2, ",", "."); ?></h2>
    
    <span>
        <h3>Ganho total para a Empresa RoboParts - R$<?= number_format($renda * .9, 2, ",", "."); ?> - (90%)</h3>
        <h3>Ganho total para a Empresa Ars (Parceria) - R$<?= number_format($renda * .1, 2, ",", "."); ?> - (10%)</h3>
    </span>
</main>

    <div class="admin-bloco">

        <h3 style="margin-top: 10px; margin-bottom: 20px;">Renda da Empresa</h3>
        <hr style="border-color: gray; opacity: .15;">

        <span>
            <p style="margin-bottom: 1px;">Total de vendas - R$<?= number_format($renda, 2, ",", "."); ?></p>
            <p style="margin-bottom: 1px;">Ganhos para Empresa RoboParts - R$<?= number_format($renda * .9, 2, ",", "."); ?> - (90%)</p>
            <p style="margin-bottom: 20px;">Ganhos para Empresa Ars (Parceria) - R$<?= number_format($renda * .1, 2, ",", "."); ?> - (10%)</p>
        </span>
        <hr style="border-color: gray; opacity: .15;">

        <h3 style="margin-top: 10px; margin-bottom: 20px;">Vendas no Ano - 2026</h3>

        <span class="grafico">
            <p>Janeiro</p>
            <div id="janeiro" class="mes">
                <div id="barra"><br></div>
            </div>

            <p>Fevereiro</p>
            <div id="fevereiro" class="mes">
                <div id="barra"><br></div>
            </div>

            <p>Março</p>
            <div id="marco" class="mes">
                <div id="barra"><br></div>
            </div>

            <p>Abril</p>
            <div id="abril" class="mes">
                <div id="barra"><br></div>
            </div>

            <p>Maio</p>
            <div id="maio" class="mes">
                <div id="barra"><br></div>
            </div>

            <p>Junho</p>
            <div id="junho" class="mes">
                <div id="barra"><br></div>
            </div>

            <p>Julho</p>
            <div id="julho" class="mes">
                <div id="barra"><br></div>
            </div>

            <p>Agosto</p>
            <div id="agosto" class="mes">
                <div id="barra"><br></div>
            </div>

            <p>Setembro</p>
            <div id="setembro" class="mes">
                <div id="barra"><br></div>
            </div>

            <p>Outubro</p>
            <div id="outubro" class="mes">
                <div id="barra"><br></div>
            </div>

            <p>Novembro</p>
            <div id="novembro" class="mes">
                <div id="barra"><br></div>
            </div>

            <p>Dezembro</p>
            <div id="dezembro" class="mes">
                <div id="barra"><br></div>
            </div>
        </span>
    </div>
</main>

<style>
    .grafico {
        font-weight: bold;
    }

    .mes {
        border: 1px solid gray;
        border-radius: 10px;

        overflow: hidden;
    }

    #barra {
        width: 30%;
        background-color: var(--texto);
    }
</style>

<?php require __DIR__ . '/includes/footer.php';
