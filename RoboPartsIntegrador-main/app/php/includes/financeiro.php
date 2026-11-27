<?php

if (!defined('RAIZ')) {
    define('RAIZ', dirname(__DIR__, 2));
}
const ARQUIVO_FINANCEIRO = RAIZ . '/data/financeiro.json';

const FIN_CATEGORIAS = [
    'receita' => ['Venda', 'Locação', 'Serviço', 'Outros'],
    'despesa' => ['Operacional', 'Financeiro', 'Administrativo', 'Investimento', 'Outros'],
];

function fin_ler(): array
{
    if (!is_file(ARQUIVO_FINANCEIRO)) {
        return [];
    }
    $json = json_decode((string) file_get_contents(ARQUIVO_FINANCEIRO), true);
    return is_array($json['lancamentos'] ?? null) ? array_values($json['lancamentos']) : [];
}

/** Lê, deixa $alterar mexer na lista e grava (com trava, para duas compras não se atropelarem). */
function fin_atualizar(callable $alterar): bool
{
    $dir = dirname(ARQUIVO_FINANCEIRO);
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        return false;
    }
    $trava = fopen(ARQUIVO_FINANCEIRO . '.lock', 'c');
    if (!$trava) {
        return false;
    }
    flock($trava, LOCK_EX);

    $nova = $alterar(fin_ler());
    $ok   = true;
    if (is_array($nova)) {
        $tmp = ARQUIVO_FINANCEIRO . '.' . bin2hex(random_bytes(4)) . '.tmp';
        $ok  = file_put_contents($tmp, json_encode(['lancamentos' => array_values($nova)], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) !== false
            && rename($tmp, ARQUIVO_FINANCEIRO);
        if (!$ok && is_file($tmp)) {
            unlink($tmp);
        }
    }
    flock($trava, LOCK_UN);
    fclose($trava);
    return $ok;
}

function fin_proximo_id(array $lista): int
{
    return ($lista ? max(array_map(fn($l) => (int) ($l['id'] ?? 0), $lista)) : 0) + 1;
}

function fin_status(array $l): string
{
    if (!empty($l['data_baixa'])) {
        return $l['tipo'] === 'receita' ? 'Recebido' : 'Quitado';
    }
    return ($l['vencimento'] ?? '') < date('Y-m-d') ? 'Atrasado' : 'Em Aberto';
}

function fin_data_valida(?string $s): bool
{
    $d = DateTime::createFromFormat('Y-m-d', (string) $s);
    return $d !== false && $d->format('Y-m-d') === $s;
}

/** Chamada quando uma compra é finalizada: cada item vira uma receita já recebida. */
function fin_registrar_venda(string $cliente, string $produto, int $qtd, float $total, string $produtoId): bool
{
    $hoje = date('Y-m-d');
    return fin_atualizar(function (array $lista) use ($cliente, $produto, $qtd, $total, $produtoId, $hoje) {
        $lista[] = [
            'id'         => fin_proximo_id($lista),
            'tipo'       => 'receita',
            'pessoa'     => $cliente !== '' ? $cliente : 'Cliente da loja',
            'descricao'  => $produto . ' (x' . $qtd . ')' . ($produtoId !== '' ? ' #' . $produtoId : ''),
            'categoria'  => 'Venda',
            'valor'      => round($total, 2),
            'vencimento' => $hoje,
            'data_baixa' => $hoje,
            'origem'     => 'venda',
        ];
        return $lista;
    });
}

/** Lançamentos de exemplo (os do dashboard enviado), para ver a tela cheia. */
function fin_exemplo(): array
{
    $r = [ // pessoa, descrição, categoria, valor, vencimento, baixa
        ['Empresa A', '-', 'Venda', 3500, '2026-03-01', '2026-03-01'],
        ['Empresa B', '-', 'Venda', 3200.50, '2026-03-03', '2026-03-04'],
        ['Empresa C', '-', 'Venda', 2400, '2026-03-05', null],
        ['Empresa D', 'Produto x', 'Locação', 1250, '2026-03-07', '2026-03-07'],
        ['Empresa E', '-', 'Venda', 5876.12, '2026-03-25', null],
        ['Empresa F', '-', 'Venda', 7895.23, '2026-03-25', '2026-03-10'],
        ['Empresa G', '-', 'Venda', 12845, '2026-03-18', '2026-03-17'],
        ['Empresa H', 'Produto x', 'Locação', 1245, '2026-03-21', null],
        ['Empresa I', '-', 'Venda', 3259, '2026-03-07', '2026-03-20'],
        ['Empresa J', '-', 'Venda', 8247.26, '2026-03-19', '2026-03-19'],
        ['Empresa K', '-', 'Venda', 5126.39, '2026-03-15', null],
        ['Empresa L', 'Produto Y', 'Locação', 2896.15, '2026-03-31', null],
    ];
    $d = [
        ['Neoenergia', 'Energia', 'Operacional', 896.25, '2026-03-01', '2026-03-01'],
        ['Empresa B', 'Internet', 'Operacional', 350, '2026-03-04', null],
        ['Empresa C', 'Office Supplies', 'Operacional', 419.25, '2026-03-05', '2026-03-05'],
        ['Empresa D', 'Telefonia', 'Operacional', 689.27, '2026-03-15', null],
        ['Simples Nacional', 'Imposto', 'Financeiro', 29863.89, '2026-03-13', '2026-03-15'],
        ['Alvará', 'Licença', 'Administrativo', 8651.59, '2026-03-20', null],
        ['F. de Pagamento', 'Salários', 'Operacional', 79569.15, '2026-03-05', '2026-03-01'],
        ['Empresa H', 'Geladeira', 'Investimento', 2899.89, '2026-03-20', null],
        ['DAS MEI', 'Imposto', 'Financeiro', 12630.59, '2026-03-15', '2026-03-15'],
        ['Empresa J', 'Site', 'Administrativo', 399, '2026-03-31', null],
        ['ICMS', 'Imposto', 'Financeiro', 4659.54, '2026-03-29', null],
        ['Empresa K', 'Segurança Priv', 'Operacional', 639.20, '2026-03-31', null],
        ['Vale Transporte', 'Colaboradores', 'Operacional', 26125.69, '2026-03-29', null],
    ];
    $saida = [];
    foreach (['receita' => $r, 'despesa' => $d] as $tipo => $linhas) {
        foreach ($linhas as [$pessoa, $desc, $cat, $valor, $venc, $baixa]) {
            $saida[] = [
                'tipo' => $tipo, 'pessoa' => $pessoa, 'descricao' => $desc, 'categoria' => $cat,
                'valor' => (float) $valor, 'vencimento' => $venc, 'data_baixa' => $baixa, 'origem' => 'exemplo',
            ];
        }
    }
    return $saida;
}
