<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/financeiro.php';
exigir_admin();

$MESES = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

function fin_mes_valido(?string $m): string
{
    $m = (string) $m;
    return ($m === 'todos' || preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $m)) ? $m : date('Y-m');
}
function fin_mes_nome(string $m, array $MESES): string
{
    return $m === 'todos' ? 'Todos os períodos' : $MESES[(int) substr($m, 5, 2)] . ' / ' . substr($m, 0, 4);
}
function fin_pct(float $v): string
{
    return number_format($v, 1, ',', '.') . '%';
}
function fin_data(?string $d): string
{
    return $d ? date('d/m/Y', strtotime($d)) : '—';
}

$mesSel = fin_mes_valido($_POST['mes'] ?? $_GET['mes'] ?? null);
$voltar = 'financeiro.php?mes=' . urlencode($mesSel);

/* ===== AÇÕES (POST → grava → redireciona) ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();
    $acao = $_POST['acao'] ?? '';
    $id   = (int) ($_POST['id'] ?? 0);

    if ($acao === 'salvar') {
        $tipo    = $_POST['tipo'] ?? '';
        $pessoa  = trim((string) ($_POST['pessoa'] ?? ''));
        $desc    = trim((string) ($_POST['descricao'] ?? ''));
        $cat     = $_POST['categoria'] ?? '';
        $valor   = paraNumero((string) ($_POST['valor'] ?? ''));
        $venc    = (string) ($_POST['vencimento'] ?? '');
        $baixado = !empty($_POST['baixado']);
        $dataB   = (string) ($_POST['data_baixa'] ?? '');
        $volta   = $voltar . ($id ? '&editar=' . $id : '');

        if (!isset(FIN_CATEGORIAS[$tipo]) || !in_array($cat, FIN_CATEGORIAS[$tipo], true)) {
            flash('aviso', 'Escolha o tipo e a categoria.');
            ir($volta);
        }
        if ($pessoa === '' || mb_strlen($pessoa) > 80 || mb_strlen($desc) > 120) {
            flash('aviso', 'Informe o cliente/fornecedor (até 80 caracteres) e uma descrição de até 120.');
            ir($volta);
        }
        if ($valor === null || $valor <= 0) {
            flash('aviso', 'Informe um valor maior que zero.');
            ir($volta);
        }
        if (!fin_data_valida($venc)) {
            flash('aviso', 'Informe uma data de vencimento válida.');
            ir($volta);
        }
        $baixa = $baixado ? (fin_data_valida($dataB) ? $dataB : date('Y-m-d')) : null;

        $ok = fin_atualizar(function (array $lista) use ($id, $tipo, $pessoa, $desc, $cat, $valor, $venc, $baixa) {
            $novo = [
                'tipo' => $tipo, 'pessoa' => $pessoa, 'descricao' => $desc !== '' ? $desc : '-', 'categoria' => $cat,
                'valor' => round($valor, 2), 'vencimento' => $venc, 'data_baixa' => $baixa, 'origem' => 'manual',
            ];
            foreach ($lista as $i => $l) {
                if ($id && (int) $l['id'] === $id) {
                    $lista[$i] = ['id' => $id] + $novo;
                    $lista[$i]['origem'] = $l['origem'] ?? 'manual'; // vendas da loja continuam marcadas como "loja"
                    return $lista;
                }
            }
            $lista[] = ['id' => fin_proximo_id($lista)] + $novo;
            return $lista;
        });
        flash('aviso', $ok ? ($id ? 'Lançamento atualizado.' : 'Lançamento adicionado.') : 'Não foi possível gravar (permissão da pasta data/).');
        // Volta para o mês do vencimento, para o lançamento aparecer na tela
        ir('financeiro.php?mes=' . urlencode($mesSel === 'todos' ? 'todos' : substr($venc, 0, 7)));
    }

    if ($acao === 'baixa' && $id) {
        fin_atualizar(function (array $lista) use ($id) {
            foreach ($lista as $i => $l) {
                if ((int) $l['id'] === $id) {
                    $lista[$i]['data_baixa'] = empty($l['data_baixa']) ? date('Y-m-d') : null;
                }
            }
            return $lista;
        });
        ir($voltar);
    }

    if ($acao === 'excluir' && $id) {
        fin_atualizar(fn(array $lista) => array_filter($lista, fn($l) => (int) $l['id'] !== $id));
        flash('aviso', 'Lançamento excluído.');
        ir($voltar);
    }

    if ($acao === 'exemplo') {
        fin_atualizar(function (array $lista) {
            if (array_filter($lista, fn($l) => ($l['origem'] ?? '') === 'exemplo')) {
                return null; // já carregado
            }
            $prox = fin_proximo_id($lista);
            foreach (fin_exemplo() as $n => $l) {
                $lista[] = ['id' => $prox + $n] + $l;
            }
            return $lista;
        });
        flash('aviso', 'Dados de exemplo carregados (março de 2026).');
        ir('financeiro.php?mes=2026-03');
    }

    if ($acao === 'limpar_exemplo') {
        fin_atualizar(fn(array $lista) => array_filter($lista, fn($l) => ($l['origem'] ?? '') !== 'exemplo'));
        flash('aviso', 'Dados de exemplo removidos.');
        ir('financeiro.php');
    }

    ir($voltar);
}

/* ===== DADOS ===== */
$todos = fin_ler();
$meses = array_unique(array_merge([date('Y-m')], array_map(fn($l) => substr((string) $l['vencimento'], 0, 7), $todos)));
rsort($meses);
$temExemplo = (bool) array_filter($todos, fn($l) => ($l['origem'] ?? '') === 'exemplo');

$lista = array_values(array_filter($todos, fn($l) => $mesSel === 'todos' || substr((string) $l['vencimento'], 0, 7) === $mesSel));
usort($lista, fn($a, $b) => [$a['vencimento'], $a['id']] <=> [$b['vencimento'], $b['id']]);

function fin_resumo(array $lista, string $tipo): array
{
    $r = ['total' => 0.0, 'baixado' => 0.0, 'aberto' => 0.0, 'atrasado' => 0.0, 'cats' => [], 'linhas' => []];
    foreach ($lista as $l) {
        if ($l['tipo'] !== $tipo) {
            continue;
        }
        $v = (float) $l['valor'];
        $s = fin_status($l);
        $r['total'] += $v;
        $r[$s === 'Atrasado' ? 'atrasado' : ($s === 'Em Aberto' ? 'aberto' : 'baixado')] += $v;
        $r['cats'][$l['categoria']] = ($r['cats'][$l['categoria']] ?? 0) + $v;
        $l['status'] = $s;
        $r['linhas'][] = $l;
    }
    arsort($r['cats']);
    return $r;
}
$rec = fin_resumo($lista, 'receita');
$des = fin_resumo($lista, 'despesa');

$resultado   = $rec['total'] - $des['total'];
$margem      = $rec['total'] > 0 ? $resultado / $rec['total'] * 100 : 0;
$saldoAberto = ($rec['aberto'] + $rec['atrasado']) - ($des['aberto'] + $des['atrasado']);
$caixa       = $rec['baixado'] - $des['baixado'];
$vendas      = array_sum(array_map(fn($l) => ($l['origem'] ?? '') === 'venda' ? (float) $l['valor'] : 0, $rec['linhas']));
$lucro       = $resultado >= 0;
$parteArs     = $lucro ? $resultado * 0.10 : 0;          // Ars recebe 10% do lucro
$parteRobo    = $lucro ? $resultado * 0.90 : $resultado; // RoboParts fica com 90% (ou com o prejuízo todo)

/* Lançamento em edição */
$edit = null;
if (isset($_GET['editar'])) {
    foreach ($todos as $l) {
        if ((int) $l['id'] === (int) $_GET['editar']) {
            $edit = $l;
        }
    }
}
$f = $edit ?? ['tipo' => 'receita', 'pessoa' => '', 'descricao' => '', 'categoria' => 'Venda', 'valor' => '', 'vencimento' => date('Y-m-d'), 'data_baixa' => null];

$titulo = 'Gestão financeira';
$css    = ['admin.css'];
require __DIR__ . '/includes/header.php';

function fin_tabela(array $linhas, string $tipo, string $mesSel): void
{
    $rotuloBaixa = $tipo === 'receita' ? 'Receber' : 'Pagar';
    if (!$linhas) {
        echo '<tr><td colspan="6" class="fin-vazio">Nenhum lançamento neste período.</td></tr>';
        return;
    }
    foreach ($linhas as $l) {
        $cls  = $l['status'] === 'Atrasado' ? 'atrasado' : ($l['status'] === 'Em Aberto' ? 'aberto' : 'ok');
        $baixado = !empty($l['data_baixa']); ?>
        <tr>
            <td title="<?= e($l['descricao']) ?>"><?= e($l['pessoa']) ?><?php if (($l['origem'] ?? '') === 'venda'): ?> <em class="fin-tag">loja</em><?php endif; ?>
                <small><?= e($l['descricao']) ?></small></td>
            <td><?= e($l['categoria']) ?></td>
            <td><?= e(fin_data($l['vencimento'])) ?></td>
            <td class="fin-val"><?= e(preco((float) $l['valor'])) ?></td>
            <td><span class="fin-status <?= $cls ?>"><?= e($l['status']) ?></span></td>
            <td class="fin-acoes">
                <form method="post">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="acao" value="baixa"><input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
                    <input type="hidden" name="mes" value="<?= e($mesSel) ?>">
                    <button type="submit"><?= $baixado ? 'Estornar' : $rotuloBaixa ?></button>
                </form>
                <a href="financeiro.php?mes=<?= e(urlencode($mesSel)) ?>&editar=<?= (int) $l['id'] ?>#form">Editar</a>
                <form method="post" onsubmit="return confirm('Excluir este lançamento?')">
                    <?= csrf_campo() ?>
                    <input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
                    <input type="hidden" name="mes" value="<?= e($mesSel) ?>">
                    <button type="submit" class="del">Excluir</button>
                </form>
            </td>
        </tr>
    <?php }
}

function fin_progresso(array $cats, float $total, array $cores): void
{
    if (!$cats) {
        echo '<p class="fin-vazio">Sem dados.</p>';
        return;
    }
    $i = 0;
    foreach ($cats as $nome => $v) {
        $p = $total > 0 ? $v / $total * 100 : 0; ?>
        <div class="fin-prog">
            <div><span><?= e($nome) ?></span><b><?= e(preco($v)) ?> <small><?= fin_pct($p) ?></small></b></div>
            <div class="fin-barra"><i style="width:<?= round($p, 1) ?>%;background:<?= $cores[$i++ % count($cores)] ?>"></i></div>
        </div>
    <?php }
}

function fin_legenda(array $itens): void
{
    $total = array_sum(array_column($itens, 1));
    foreach ($itens as [$nome, $v, $cor]) { ?>
        <div class="fin-leg"><span><i style="background:<?= $cor ?>"></i><?= e($nome) ?></span>
            <b><?= e(preco($v)) ?> <small>(<?= fin_pct($total > 0 ? $v / $total * 100 : 0) ?>)</small></b></div>
    <?php }
}

$corRec = [['Recebido', $rec['baixado'], '#2ea44f'], ['Em Aberto', $rec['aberto'], '#58a6ff'], ['Atrasado', $rec['atrasado'], '#f85149']];
$corDes = [['Quitado', $des['baixado'], '#2ea44f'], ['Em Aberto', $des['aberto'], '#58a6ff'], ['Atrasado', $des['atrasado'], '#f85149']];

/* Texto da análise automática */
if (!$todos) {
    $insTom = 'warn';
    $insTit = 'Nenhum lançamento ainda';
    $insTxt = 'As vendas da loja entram aqui sozinhas quando um cliente finaliza a compra. Lance receitas e despesas pelo formulário abaixo ou carregue os dados de exemplo.';
} elseif ($rec['total'] + $des['total'] == 0) {
    $insTom = 'warn';
    $insTit = 'Sem lançamentos neste período';
    $insTxt = 'Escolha outro mês no seletor ou adicione um lançamento com vencimento em ' . fin_mes_nome($mesSel, $MESES) . '.';
} elseif (!$lucro) {
    $insTom = 'danger';
    $insTit = 'Atenção: resultado negativo no período';
    $insTxt = 'As despesas (' . preco($des['total']) . ') superam as receitas (' . preco($rec['total']) . ') em ' . preco(abs($resultado)) . ' — margem de ' . fin_pct($margem) . '. '
        . ($rec['atrasado'] > 0 ? 'Há ' . preco($rec['atrasado']) . ' em receitas atrasadas que, se recebidas, melhoram o resultado. ' : '')
        . 'Revise as despesas das categorias maiores para reequilibrar o fluxo.';
} else {
    $insTom = 'ok';
    $insTit = 'Resultado positivo — acompanhe o que está pendente';
    $insTxt = 'Lucro de ' . preco($resultado) . ' no período (margem ' . fin_pct($margem) . '). Ainda há ' . preco($des['aberto'] + $des['atrasado'])
        . ' em despesas a pagar e ' . preco($rec['aberto'] + $rec['atrasado']) . ' em receitas a receber.';
}

$grafico = [
    'rec' => [$rec['total'], $rec['baixado']], 'des' => [$des['total'], $des['baixado']],
    'recStatus' => array_column($corRec, 1), 'desStatus' => array_column($corDes, 1),
    'rotRec' => array_column($corRec, 0), 'rotDes' => array_column($corDes, 0), 'cores' => array_column($corRec, 2),
];
?>
<main class="fin">
    <p class="trilha"><a href="<?= e(pg('admin.php')) ?>">Painel</a> / Gestão financeira</p>

    <div class="fin-topo">
        <div>
            <h1>Gestão financeira</h1>
            <p class="fin-mudo"><?= e(LOJA_NOME) ?> · período por data de vencimento</p>
        </div>
        <form method="get" class="fin-mes">
            <label for="mes">Período</label>
            <select name="mes" id="mes" onchange="this.form.submit()">
                <?php foreach ($meses as $m): ?>
                    <option value="<?= e($m) ?>" <?= $m === $mesSel ? 'selected' : '' ?>><?= e(fin_mes_nome($m, $MESES)) ?></option>
                <?php endforeach; ?>
                <option value="todos" <?= $mesSel === 'todos' ? 'selected' : '' ?>>Todos os períodos</option>
            </select>
            <noscript><button type="submit">Ver</button></noscript>
        </form>
    </div>

    <section class="fin-hero <?= $lucro ? 'lucro' : 'prejuizo' ?>">
        <div>
            <p class="fin-rot">Resultado — <?= e(fin_mes_nome($mesSel, $MESES)) ?> (receitas − despesas)</p>
            <p class="fin-grande"><?= e(preco(abs($resultado))) ?></p>
            <span class="fin-selo"><?= $lucro ? '▲ LUCRO' : '▼ PREJUÍZO' ?></span>
        </div>
        <div class="fin-meta">
            <div><b class="verde"><?= e(preco($rec['total'])) ?></b><span>Total receitas</span></div>
            <div><b class="verm"><?= e(preco($des['total'])) ?></b><span>Total despesas</span></div>
            <div><b class="amar"><?= fin_pct($margem) ?></b><span>Margem</span></div>
        </div>
    </section>

    <div class="fin-kpis">
        <div class="fin-kpi verde"><span>Receitas (a receber)</span><b><?= e(preco($rec['total'])) ?></b>
            <small>Recebido <?= e(preco($rec['baixado'])) ?> · Pendente <?= e(preco($rec['aberto'])) ?> · Atrasado <?= e(preco($rec['atrasado'])) ?></small></div>
        <div class="fin-kpi verm"><span>Despesas (a pagar)</span><b><?= e(preco($des['total'])) ?></b>
            <small>Pago <?= e(preco($des['baixado'])) ?> · Em aberto <?= e(preco($des['aberto'])) ?> · Atrasado <?= e(preco($des['atrasado'])) ?></small></div>
        <div class="fin-kpi azul"><span>Total recebido</span><b><?= e(preco($rec['baixado'])) ?></b>
            <small><?= fin_pct($rec['total'] > 0 ? $rec['baixado'] / $rec['total'] * 100 : 0) ?> das receitas</small></div>
        <div class="fin-kpi amar"><span>Total pago</span><b><?= e(preco($des['baixado'])) ?></b>
            <small><?= fin_pct($des['total'] > 0 ? $des['baixado'] / $des['total'] * 100 : 0) ?> das despesas</small></div>
        <div class="fin-kpi neutro"><span>Saldo em aberto</span><b class="<?= $saldoAberto >= 0 ? 'pos' : 'neg' ?>"><?= e(preco($saldoAberto)) ?></b>
            <small>Pendente a receber − pendente a pagar</small></div>
        <div class="fin-kpi neutro"><span>Caixa realizado</span><b class="<?= $caixa >= 0 ? 'pos' : 'neg' ?>"><?= e(preco($caixa)) ?></b>
            <small>Recebido − pago</small></div>
        <div class="fin-kpi azul"><span>Vendas da loja</span><b><?= e(preco($vendas)) ?></b>
            <small>Total vendido na loja no período</small></div>
        <div class="fin-kpi azul"><span>Lucro RoboParts (90%)</span><b class="<?= $parteRobo >= 0 ? 'pos' : 'neg' ?>"><?= e(preco($parteRobo)) ?></b>
            <small>Resultado do período − parte da Ars</small></div>
        <div class="fin-kpi amar"><span>Repasse Ars (10%)</span><b><?= e(preco($parteArs)) ?></b>
            <small><?= $lucro ? '10% do lucro de ' . e(preco($resultado)) : 'Sem lucro no período: sem repasse' ?></small></div>
    </div>

    <div class="fin-insight <?= $insTom ?>"><h4><?= e($insTit) ?></h4><p><?= e($insTxt) ?></p></div>

    <div class="fin-graficos">
        <div class="fin-caixa"><h3>Receitas vs despesas</h3><p class="fin-mudo">Total do período e o que já foi realizado</p>
            <div class="fin-cv" style="height:260px"><canvas id="gBarras"></canvas></div></div>
        <div class="fin-caixa"><h3>Composição das receitas</h3><p class="fin-mudo">Por status</p>
            <div class="fin-cv" style="height:180px"><canvas id="gRec"></canvas></div><?php fin_legenda($corRec); ?></div>
        <div class="fin-caixa"><h3>Composição das despesas</h3><p class="fin-mudo">Por status</p>
            <div class="fin-cv" style="height:180px"><canvas id="gDes"></canvas></div><?php fin_legenda($corDes); ?></div>
    </div>

    <div class="fin-duplo">
        <div class="fin-caixa"><h3>Receitas por categoria</h3><?php fin_progresso($rec['cats'], $rec['total'], ['#2ea44f', '#3fb950', '#56d364', '#7ee787']); ?></div>
        <div class="fin-caixa"><h3>Despesas por categoria</h3><?php fin_progresso($des['cats'], $des['total'], ['#f85149', '#ff7b72', '#ffa198', '#e3b341']); ?></div>
    </div>

    <details class="fin-caixa fin-form" id="form" <?= $edit ? 'open' : '' ?>>
        <summary><?= $edit ? 'Editando lançamento #' . (int) $edit['id'] : '+ Novo lançamento' ?></summary>
        <form method="post" class="fin-grid">
            <?= csrf_campo() ?>
            <input type="hidden" name="acao" value="salvar">
            <input type="hidden" name="id" value="<?= $edit ? (int) $edit['id'] : 0 ?>">
            <input type="hidden" name="mes" value="<?= e($mesSel) ?>">
            <label>Tipo
                <select name="tipo" id="fTipo">
                    <option value="receita" <?= $f['tipo'] === 'receita' ? 'selected' : '' ?>>Receita</option>
                    <option value="despesa" <?= $f['tipo'] === 'despesa' ? 'selected' : '' ?>>Despesa</option>
                </select></label>
            <label><span id="fPessoaRot">Cliente / Fornecedor</span>
                <input name="pessoa" required maxlength="80" value="<?= e($f['pessoa']) ?>"></label>
            <label>Descrição
                <input name="descricao" maxlength="120" value="<?= e($f['descricao'] === '-' ? '' : $f['descricao']) ?>"></label>
            <label>Categoria
                <select name="categoria" id="fCat"></select></label>
            <label>Valor (R$)
                <input name="valor" required inputmode="decimal" placeholder="0,00" value="<?= $f['valor'] === '' ? '' : e(number_format((float) $f['valor'], 2, ',', '.')) ?>"></label>
            <label>Vencimento
                <input type="date" name="vencimento" required value="<?= e($f['vencimento']) ?>"></label>
            <label class="fin-check"><input type="checkbox" name="baixado" id="fBaixado" <?= !empty($f['data_baixa']) ? 'checked' : '' ?>>
                <span id="fBaixadoRot">Já foi recebido</span></label>
            <label>Data do <span id="fBaixaRot">recebimento</span>
                <input type="date" name="data_baixa" value="<?= e($f['data_baixa'] ?? date('Y-m-d')) ?>"></label>
            <div class="fin-botoes">
                <button class="btn btn-cheio" type="submit"><?= $edit ? 'Salvar alterações' : 'Adicionar' ?></button>
                <?php if ($edit): ?><a class="btn" href="financeiro.php?mes=<?= e(urlencode($mesSel)) ?>">Cancelar</a><?php endif; ?>
            </div>
        </form>
    </details>

    <div class="fin-duplo">
        <div class="fin-caixa fin-tab">
            <h3>Receitas <small><?= count($rec['linhas']) ?> registros</small></h3>
            <div class="fin-rolagem"><table>
                <thead><tr><th>Cliente</th><th>Categoria</th><th>Vencimento</th><th>Valor</th><th>Status</th><th></th></tr></thead>
                <tbody><?php fin_tabela($rec['linhas'], 'receita', $mesSel); ?></tbody>
            </table></div>
        </div>
        <div class="fin-caixa fin-tab">
            <h3>Despesas <small><?= count($des['linhas']) ?> registros</small></h3>
            <div class="fin-rolagem"><table>
                <thead><tr><th>Fornecedor</th><th>Categoria</th><th>Vencimento</th><th>Valor</th><th>Status</th><th></th></tr></thead>
                <tbody><?php fin_tabela($des['linhas'], 'despesa', $mesSel); ?></tbody>
            </table></div>
        </div>
    </div>

    <form method="post" class="fin-exemplo">
        <?= csrf_campo() ?>
        <input type="hidden" name="acao" value="<?= $temExemplo ? 'limpar_exemplo' : 'exemplo' ?>">
        <?php if ($temExemplo): ?>
            <button type="submit" class="del" onclick="return confirm('Remover só os lançamentos de exemplo?')">Remover dados de exemplo</button>
        <?php else: ?>
            <button type="submit">Carregar dados de exemplo (março/2026)</button>
        <?php endif; ?>
    </form>
</main>

<style>
.fin { --verde:#2ea44f; --verm:#e5534b; --azul:#58a6ff; --amar:#d9a521; max-width:1300px; margin:0 auto; padding:24px 20px 48px; }
.fin h1 { margin:0 0 2px; } .fin h3 { margin:0 0 4px; font-size:.95rem; }
.fin-mudo, .fin small { color:var(--suave); font-size:.78rem; }
.fin-topo { display:flex; justify-content:space-between; align-items:flex-end; flex-wrap:wrap; gap:12px; margin-bottom:18px; }
.fin-mes { display:flex; align-items:center; gap:8px; } .fin-mes label { font-size:.8rem; color:var(--suave); }
.fin select, .fin input:not([type=checkbox]) { padding:8px 10px; border:1px solid var(--borda-campo); border-radius:6px; background:var(--branco); color:var(--texto); font:inherit; width:100%; }
.fin-mes select { width:auto; }
.fin-hero, .fin-caixa, .fin-kpi, .fin-insight { background:var(--branco); border:var(--borda); border-radius:10px; }
.fin-hero { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px; padding:24px 28px; margin-bottom:18px; border-top:4px solid var(--verde); }
.fin-hero.prejuizo { border-top-color:var(--verm); }
.fin-rot { font-size:.75rem; letter-spacing:.06em; text-transform:uppercase; color:var(--suave); margin:0 0 6px; }
.fin-grande { font-size:2.5rem; font-weight:800; line-height:1; margin:0; }
.lucro .fin-grande { color:var(--verde); } .prejuizo .fin-grande { color:var(--verm); }
.fin-selo { display:inline-block; margin-top:10px; padding:5px 14px; border-radius:20px; font-size:.8rem; font-weight:700; background:color-mix(in srgb, var(--verde) 16%, transparent); color:var(--verde); }
.prejuizo .fin-selo { background:color-mix(in srgb, var(--verm) 16%, transparent); color:var(--verm); }
.fin-meta { display:flex; gap:32px; flex-wrap:wrap; } .fin-meta div { text-align:center; }
.fin-meta b { display:block; font-size:1.15rem; } .fin-meta span { font-size:.68rem; text-transform:uppercase; color:var(--suave); }
.verde { color:var(--verde); } .verm { color:var(--verm); } .azul { color:var(--azul); } .amar { color:var(--amar); }
.fin-kpis { display:grid; grid-template-columns:repeat(auto-fit,minmax(210px,1fr)); gap:14px; margin-bottom:18px; }
.fin-kpi { padding:16px 18px; border-top:3px solid var(--suave); }
.fin-kpi.verde { border-top-color:var(--verde); } .fin-kpi.verm { border-top-color:var(--verm); }
.fin-kpi.azul { border-top-color:var(--azul); } .fin-kpi.amar { border-top-color:var(--amar); }
.fin-kpi span { display:block; font-size:.7rem; text-transform:uppercase; letter-spacing:.05em; color:var(--suave); margin-bottom:8px; }
.fin-kpi b { display:block; font-size:1.4rem; color:var(--texto); } .fin-kpi.verde b { color:var(--verde); } .fin-kpi.verm b { color:var(--verm); }
.fin-kpi b.pos { color:var(--verde); } .fin-kpi b.neg { color:var(--verm); }
.fin-kpi small { display:block; margin-top:6px; }
.fin-insight { border-left:4px solid var(--amar); padding:14px 18px; margin-bottom:18px; }
.fin-insight.danger { border-left-color:var(--verm); } .fin-insight.ok { border-left-color:var(--verde); }
.fin-insight h4 { margin:0 0 4px; font-size:.9rem; } .fin-insight p { margin:0; font-size:.85rem; color:var(--suave); line-height:1.5; }
.fin-graficos { display:grid; grid-template-columns:1.6fr 1fr 1fr; gap:16px; margin-bottom:18px; }
.fin-duplo { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:18px; }
@media (max-width:1100px) { .fin-graficos { grid-template-columns:1fr 1fr; } }
@media (max-width:760px) { .fin-graficos, .fin-duplo { grid-template-columns:1fr; } }
.fin-caixa { padding:18px 20px; } .fin-cv { position:relative; margin-top:12px; }
.fin-leg, .fin-prog > div:first-child { display:flex; justify-content:space-between; gap:8px; font-size:.78rem; margin-top:8px; }
.fin-leg i { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:8px; }
.fin-prog { margin-top:14px; } .fin-barra { height:6px; background:var(--linha); border-radius:4px; overflow:hidden; margin-top:6px; }
.fin-barra i { display:block; height:100%; border-radius:4px; }
.fin-form summary { cursor:pointer; font-weight:600; } .fin-form { margin-bottom:18px; }
.fin-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:12px; margin-top:14px; align-items:end; }
.fin-grid label { display:flex; flex-direction:column; gap:4px; font-size:.78rem; color:var(--suave); }
.fin-check { flex-direction:row !important; align-items:center; gap:8px !important; color:var(--texto) !important; padding-bottom:8px; }
.fin-botoes { display:flex; gap:8px; }
.fin-tab { padding:0; overflow:hidden; } .fin-tab h3 { padding:14px 18px; margin:0; border-bottom:var(--borda); display:flex; justify-content:space-between; }
.fin-rolagem { overflow-x:auto; } .fin table { width:100%; border-collapse:collapse; font-size:.78rem; }
.fin th { background:var(--tabela-cab); color:var(--suave); text-transform:uppercase; font-size:.66rem; letter-spacing:.04em; text-align:left; padding:9px 12px; white-space:nowrap; }
.fin td { padding:9px 12px; border-top:1px solid var(--linha); vertical-align:middle; }
.fin td small { display:block; max-width:170px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.fin-val { font-weight:600; white-space:nowrap; } .fin-vazio { text-align:center; color:var(--suave); padding:18px; }
.fin-tag { font-style:normal; font-size:.62rem; padding:1px 6px; border-radius:10px; background:color-mix(in srgb, var(--azul) 18%, transparent); color:var(--azul); }
.fin-status { display:inline-block; padding:2px 10px; border-radius:20px; font-size:.68rem; font-weight:700; white-space:nowrap; }
.fin-status.ok { background:color-mix(in srgb, var(--verde) 16%, transparent); color:var(--verde); }
.fin-status.aberto { background:color-mix(in srgb, var(--azul) 16%, transparent); color:var(--azul); }
.fin-status.atrasado { background:color-mix(in srgb, var(--verm) 16%, transparent); color:var(--verm); }
.fin-acoes { white-space:nowrap; } .fin-acoes form { display:inline; }
.fin-acoes button, .fin-acoes a, .fin-exemplo button { background:none; border:0; padding:0 6px; color:var(--azul); cursor:pointer; font:inherit; font-size:.76rem; text-decoration:none; }
.fin-exemplo { text-align:center; } .fin-exemplo button { border:1px dashed var(--borda-forte); padding:8px 14px; border-radius:6px; color:var(--suave); }
.fin .del { color:var(--verm); }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
(function () {
    var G = <?= json_encode($grafico, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    var CATS = <?= json_encode(FIN_CATEGORIAS, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    var CAT_ATUAL = <?= json_encode($f['categoria'], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    var fmt = function (v) { return v.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); };

    /* Formulário: categorias e rótulos mudam com o tipo */
    var tipo = document.getElementById('fTipo'), cat = document.getElementById('fCat');
    function ajustarForm(manter) {
        var r = tipo.value === 'receita';
        cat.innerHTML = '';
        CATS[tipo.value].forEach(function (c) {
            var o = document.createElement('option');
            o.value = o.textContent = c;
            if (manter && c === CAT_ATUAL) o.selected = true;
            cat.appendChild(o);
        });
        document.getElementById('fPessoaRot').textContent = r ? 'Cliente' : 'Fornecedor';
        document.getElementById('fBaixadoRot').textContent = r ? 'Já foi recebido' : 'Já foi pago';
        document.getElementById('fBaixaRot').textContent = r ? 'recebimento' : 'pagamento';
    }
    tipo.addEventListener('change', function () { ajustarForm(false); });
    ajustarForm(true);

    /* Gráficos (refeitos quando o tema claro/escuro muda) */
    var graficos = [];
    function css(v) { return getComputedStyle(document.documentElement).getPropertyValue(v).trim(); }
    function montar() {
        graficos.forEach(function (c) { c.destroy(); });
        graficos = [];
        if (typeof Chart === 'undefined') return;
        var texto = css('--suave'), grade = css('--linha'), fundo = css('--branco');
        var eixos = {
            x: { ticks: { color: texto }, grid: { color: grade } },
            y: { ticks: { color: texto, callback: function (v) { return 'R$ ' + (v / 1000).toFixed(0) + 'k'; } }, grid: { color: grade } }
        };
        graficos.push(new Chart(document.getElementById('gBarras'), {
            type: 'bar',
            data: {
                labels: ['Receitas', 'Despesas'],
                datasets: [
                    { label: 'Total do período', data: [G.rec[0], G.des[0]], backgroundColor: ['rgba(46,164,79,.85)', 'rgba(248,81,73,.85)'], borderRadius: 6 },
                    { label: 'Realizado', data: [G.rec[1], G.des[1]], backgroundColor: ['rgba(46,164,79,.35)', 'rgba(248,81,73,.35)'], borderRadius: 6 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false, scales: eixos,
                plugins: { legend: { labels: { color: texto, boxWidth: 12 } }, tooltip: { callbacks: { label: function (c) { return ' ' + fmt(c.parsed.y); } } } }
            }
        }));
        [['gRec', G.recStatus, G.rotRec], ['gDes', G.desStatus, G.rotDes]].forEach(function (d) {
            graficos.push(new Chart(document.getElementById(d[0]), {
                type: 'doughnut',
                data: { labels: d[2], datasets: [{ data: d[1], backgroundColor: G.cores, borderColor: fundo, borderWidth: 3 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { display: false }, tooltip: { callbacks: { label: function (c) { return ' ' + fmt(c.parsed); } } } } }
            }));
        });
    }
    montar();
    new MutationObserver(montar).observe(document.documentElement, { attributes: true, attributeFilter: ['data-tema'] });
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
