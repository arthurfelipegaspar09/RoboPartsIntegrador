<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_admin();

$titulo = 'Histórico';
require __DIR__ . '/includes/header.php';
?>

<main class="pagina pagina-estreita">
    <p class="trilha"><a href="<?= e(pg('admin.php')) ?>">Painel</a> / Histórico</p>
    <h1>Histórico de Compras</h1>

    <div class="rd_compras">
        <?php if (empty($_SESSION["renda_historico"])): ?>
            <h2>Nenhuma compra foi realizada.</h2>

        <?php else: foreach ($_SESSION["renda_historico"] as $id => $infos): ?>
            <div class="rd_produtos">
                <span class="rd_detalhes">
                    <p>NOME - <?= $infos["nome"]; ?></p>
                    <p>VALOR - R$<?= number_format($infos["valor"], 2, ",", "."); ?></p>
                    <p>DATA - <input type="date"></p>
                </span>
                
                <span class="rd_detalhes">
                    <p>ID - <?= $infos["id"]; ?></p>
                    <p>QUANTIDADE - <?= $infos["quantidade"]; ?></p>
                    <p>ENTREGUE? - <input type="checkbox"></p>
                </span>
            </div>
        <?php endforeach; endif; ?>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>

<style>
    .rd_compras {
        text-align: center;

        margin-top: 30px;
        padding: 10px;

        border: 1px dashed grey;
        border-radius: 5px;
    }

    .rd_compras h2 {
        margin: 10px;
        text-decoration: underline;
    }

    .rd_produtos {
        display: flex;
        align-items: center;
        justify-content: space-between;

        border: 1px solid gray;
        border-radius: 5px;
    }

    .rd_detalhes {
        display: flex;
        flex-direction: column;

        text-align: left;
    }

    .rd_detalhes p {
        margin: 5px;
    }
</style>
