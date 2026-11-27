<?php

require_once __DIR__ . "/init.php";
require_once __DIR__ . "/includes/financeiro.php";

if (!isset($_POST["total"])) {
    exit("[ERRO] - Não foi possível carregar os produtos do carrinho. (<a href='./carrinho.php'>Voltar</a>)");
}

for ($i = 0; $i <= $_POST["total"] - 1; $i ++) {
    $_SESSION["historico"][] = [
        "nome" => $_POST["nome$i"], // Nome
        "quantidade" => $_POST["quantidade$i"], // Quantidade
        "total" => $_POST["total$i"], // Total
        "id" => $_POST["id$i"] // ID
    ];

     // Histórico de Renda da Empresa
    $_SESSION["renda_historico"][] = [
        "id" => $_POST["id$i"], // Adiciona o Id
        "nome" => $_POST["nome$i"], // Adiciona o Nome do produto
        "valor" => $_POST["total$i"], // Adiciona o Valor do pedido
        "quantidade" => $_POST["quantidade$i"], // Adiciona a Quantidade do pedido
    ];
    
    $_SESSION["infoSite"]["renda"] = ($_SESSION["infoSite"]["renda"] ?? 0) + $_POST["total$i"];

    // Gestão financeira: a venda vira uma receita já recebida (visível em financeiro.php)
    fin_registrar_venda(
        (string) ($_SESSION["usuario_nome"] ?? ""),
        (string) $_POST["nome$i"],
        (int) $_POST["quantidade$i"],
        (float) $_POST["total$i"],
        (string) $_POST["id$i"]
    );
}

unset($_SESSION["carrinho"]);
header("Location: ../index.php");
?>
