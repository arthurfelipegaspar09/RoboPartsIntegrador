<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_acesso();

$categorias = categorias();
$nCats      = count($categorias);
$nProdutos  = count(catalogo()['produtos']);
$img        = fn(string $arquivo) => e(asset('img/sobre/' . $arquivo));

$proposta = [
    ['⚙', 'Componentes',  'Conheça componentes e recursos que podem fazer parte de projetos de robótica e automação.'],
    ['⌘', 'Aprendizado',  'Explore o universo tecnológico e amplie seus conhecimentos sobre robótica.'],
    ['◇', 'Criatividade', 'Transforme ideias em projetos, experimentando diferentes possibilidades.'],
    ['↗', 'Inovação',     'Descubra como a tecnologia pode ajudar a desenvolver soluções para desafios do dia a dia.'],
];

$identidade = [
    ['Missão',  'Aproximar pessoas da robótica por meio do acesso a componentes, informações e oportunidades de aprendizado tecnológico.'],
    ['Visão',   'Incentivar uma comunidade interessada em tecnologia, criatividade e desenvolvimento de projetos inovadores.'],
    ['Valores', 'Valorizamos a curiosidade, o conhecimento, a criatividade, a inovação e o aprendizado contínuo.'],
];

$galeria = [
    ['componentes.webp',    'Placa controladora, motores de passo e display usados em projetos de robótica', 'Componentes'],
    ['braco-robotico.webp', 'Braço robótico em movimento sobre uma mesa',                                   'Automação'],
    ['sensores.webp',       'Dois técnicos instalando sensores em um painel industrial',                    'Sensores'],
];

$titulo = 'Sobre nós';
require __DIR__ . '/includes/header.php';
?>

<main class="inst sobre-pagina">

    <!-- Apresentação: efeito Halo Horizon (só CSS) -->
    <section class="hh-stage" aria-labelledby="sobre-titulo">
        <div class="hh-glow" data-variant="top" aria-hidden="true">
            <div class="hh-arc hh-core"></div>
            <div class="hh-arc hh-gold"></div>
            <div class="hh-arc hh-rose"></div>
            <div class="hh-arc hh-mask"></div>
        </div>

        <div class="hh-content">
            <p class="hh-eyebrow">Conheça a <?= e(LOJA_NOME) ?></p>
            <h1 class="hh-title" id="sobre-titulo">Tecnologia que transforma ideias em realidade.</h1>
            <p class="hh-lead">
                Robôs para indústrias, fazendas, comércio e laboratórios, de braços articulados a humanoides
                de pesquisa, e um caminho para transformar criatividade em projetos reais.
            </p>
            <div class="inst-botoes hh-botoes">
                <a class="btn btn-cheio btn-grande" href="<?= e(pg('produtos.php')) ?>">Conheça nossos produtos &rarr;</a>
                <a class="btn btn-escuro btn-grande" href="<?= e(pg('contato.php')) ?>">Fale conosco</a>
            </div>
        </div>
    </section>

    <!-- Números -->
    <div class="inst-faixa">
        <div class="inst-faixa-in">
            <div>
                <strong><?= $nCats ?></strong>
                <span><?= $nCats === 1 ? 'categoria de robôs' : 'categorias de robôs' ?></span>
            </div>
            <?php if ($nProdutos > 0): ?>
                <div>
                    <strong><?= $nProdutos ?></strong>
                    <span><?= $nProdutos === 1 ? 'produto no catálogo' : 'produtos no catálogo' ?></span>
                </div>
            <?php endif; ?>
            <div>
                <strong>2 canais</strong>
                <span>de atendimento: WhatsApp e e-mail</span>
            </div>
        </div>
    </div>

    <!-- Quem somos -->
    <section class="inst-secao">
        <div class="inst-dividido">
            <figure class="inst-figura inst-figura-3d" id="robo3d" role="img" aria-label="Robô 3D interativo: mova o cursor para interagir">
                <span class="robo3d-loader" aria-hidden="true"></span>
                <spline-viewer class="robo3d-viewer" url="https://prod.spline.design/PyzDhpQ9E5f1E3MT/scene.splinecode" loading="lazy"></spline-viewer>
                <!-- Plano B: aparece só se o robô 3D não carregar -->
                <img class="robo3d-reserva" src="<?= $img('robo-girando.webp') ?>" alt="Robô girando e demonstrando movimentos" loading="lazy">
            </figure>
            <div>
                <p class="inst-rotulo">Quem somos</p>
                <h2>Conhecimento, inovação e criatividade</h2>
                <p class="inst-texto">
                    A <?= e(LOJA_NOME) ?> é um projeto voltado ao universo da robótica e da tecnologia. Nosso objetivo é
                    aproximar pessoas dos robôs, componentes e recursos usados na criação de projetos tecnológicos.
                </p>
                <p class="inst-texto">
                    Queremos tornar a experiência de conhecer a robótica mais acessível, incentivando a curiosidade,
                    a experimentação e o aprendizado.
                </p>
            </div>
        </div>
    </section>

    <!-- Sobre a empresa (mini texto) -->
    <section class="inst-secao inst-secao-mini">
        <div class="inst-topo-centro">
            <p class="inst-rotulo">Nossa empresa</p>
            <h2>A <?= e(LOJA_NOME) ?> em poucas palavras</h2>
            <p>
                Somos uma loja dedicada à robótica. Reunimos robôs de diferentes categorias em um só catálogo:
                industriais, humanoides, sociais, agrícolas e colaborativos. Assim, atendemos desde quem está
                começando a explorar a tecnologia até quem procura soluções para a indústria, o campo, o comércio
                e os laboratórios.
            </p>
            <p>
                Acreditamos que a tecnologia fica melhor quando é acessível. Por isso, além dos produtos, queremos
                incentivar a curiosidade, a criatividade e o aprendizado contínuo, ajudando você a transformar
                ideias em projetos reais. E, na hora de escolher, nosso atendimento próximo, por WhatsApp e e-mail,
                está pronto para ajudar você a encontrar a solução certa para o seu projeto.
            </p>
        </div>
    </section>

    <!-- Catálogo -->
    <?php if ($categorias): ?>
    <section class="inst-secao inst-secao-alt">
        <div class="inst-secao-in">
            <p class="inst-rotulo">Catálogo</p>
            <h2>O que você encontra aqui</h2>
            <ul class="inst-cats">
                <?php foreach ($categorias as $c): ?>
                    <?php $n = contar_produtos($c['id']); ?>
                    <li>
                        <a href="<?= e(pg('categoria.php')) ?>?c=<?= e($c['slug']) ?>">
                            <strong><?= e($c['nome']) ?></strong>
                            <?php if ($c['descricao'] !== ''): ?><span><?= e($c['descricao']) ?></span><?php endif; ?>
                            <small><?= $n > 0 ? $n . ($n === 1 ? ' produto' : ' produtos') : 'Ver categoria' ?> &rarr;</small>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
    <?php endif; ?>

    <!-- Galeria -->
    <section class="inst-secao">
        <div class="inst-secao-in">
            <div class="inst-topo-centro">
                <p class="inst-rotulo">Universo robótico</p>
                <h2>Explore a tecnologia</h2>
                <p>Conheça alguns exemplos de elementos presentes no mundo da robótica e da automação.</p>
            </div>
            <div class="inst-galeria">
                <?php foreach ($galeria as [$arquivo, $alt, $legenda]): ?>
                    <figure>
                        <img src="<?= $img($arquivo) ?>" alt="<?= e($alt) ?>" loading="lazy">
                        <figcaption><?= e($legenda) ?></figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    
</main>


<script>
(function () {
    var fig = document.getElementById('robo3d');
    if (!fig) return;

    var SRC = 'https://unpkg.com/@splinetool/viewer@latest/build/spline-viewer.js';
    // Remove a etiqueta "Built with Spline" (fica no shadow DOM do viewer).
    // Insiste por um minuto, porque ela só aparece depois que a cena carrega.
    function tirarMarca() {
        var v = fig.querySelector('spline-viewer');
        var n = 0;
        var t = setInterval(function () {
            var raiz = v && v.shadowRoot;
            if (raiz) {
                if (!raiz.getElementById('sem-marca')) {
                    var st = document.createElement('style');
                    st.id = 'sem-marca';
                    st.textContent = '#logo, a[href*="spline.design"] { display: none !important; }';
                    raiz.appendChild(st);
                }
                var logo = raiz.querySelector('#logo');
                if (logo) logo.remove();
            }
            if (++n > 300) clearInterval(t);
        }, 200);
    }
    function pronto() { tirarMarca(); setTimeout(function () { fig.classList.add('pronto'); }, 1400); }
    function falha()  { fig.classList.add('falhou'); }

    if (window.customElements && customElements.get('spline-viewer')) { pronto(); return; }

    var s = document.createElement('script');
    s.type = 'module';
    s.src = SRC;
    s.async = true;
    s.onload = pronto;
    s.onerror = falha;
    document.head.appendChild(s);

    // Se em 20 s nada carregar, volta para a imagem
    setTimeout(function () { if (!fig.classList.contains('pronto')) falha(); }, 20000);
})();
</script>

<?php require __DIR__ . '/includes/footer.php';
