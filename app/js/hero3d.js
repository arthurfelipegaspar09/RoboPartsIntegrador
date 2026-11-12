const CENA_URL = 'https://prod.spline.design/kZDDjO5HuC9GJUM2/scene.splinecode';
const RUNTIME_URL = 'https://unpkg.com/@splinetool/runtime@1.10.39/build/runtime.js';

const hero = document.getElementById('hero3d');

if (hero) {
    const canvas = hero.querySelector('.h3d-canvas');
    const loader = hero.querySelector('.h3d-loader');
    const luz = hero.querySelector('.h3d-luz');
    const reduzido = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- Altura: a tela inteira menos o que vem antes do hero (topo e menu) ---------- */
    function medirTopo() {
        const topo = hero.getBoundingClientRect().top + window.scrollY;
        hero.style.setProperty('--h3d-topo', Math.round(topo) + 'px');
    }
    medirTopo();
    window.addEventListener('resize', medirTopo);
    window.addEventListener('load', medirTopo);

    /* ---------- Luz que segue o cursor (mola: rigidez 130, amortecimento 25, massa 0.25) ---------- */
    const RIGIDEZ = 130;
    const AMORTECIMENTO = 25;
    const MASSA = 0.25;
    const metade = luz.offsetWidth / 2 || 250;

    let x = -1000, y = -1000;
    let vx = 0, vy = 0;
    let alvoX = x, alvoY = y;
    let iniciou = false;
    let quadro = 0;
    let ultimo = 0;

    function aplicar() {
        luz.style.transform = 'translate3d(' + (x - metade) + 'px,' + (y - metade) + 'px,0)';
    }

    function passo(agora) {
        const dt = Math.min(Math.max((agora - ultimo) / 1000, 0), 0.05);
        ultimo = agora;

        // vários passos pequenos deixam a mola estável
        const n = Math.max(1, Math.ceil(dt * 240));
        const h = dt / n;
        for (let i = 0; i < n; i++) {
            vx += ((-RIGIDEZ * (x - alvoX) - AMORTECIMENTO * vx) / MASSA) * h;
            vy += ((-RIGIDEZ * (y - alvoY) - AMORTECIMENTO * vy) / MASSA) * h;
            x += vx * h;
            y += vy * h;
        }
        aplicar();

        const parou = Math.abs(x - alvoX) < 0.05 && Math.abs(y - alvoY) < 0.05 &&
                      Math.abs(vx) < 0.05 && Math.abs(vy) < 0.05;
        if (parou) {
            x = alvoX;
            y = alvoY;
            aplicar();
            quadro = 0;
            return;
        }
        quadro = requestAnimationFrame(passo);
    }

    hero.addEventListener('pointermove', function (e) {
        if (e.pointerType === 'touch') return;

        const r = hero.getBoundingClientRect();
        alvoX = e.clientX - r.left;
        alvoY = e.clientY - r.top;

        // primeiro movimento: aparece já no cursor, sem atravessar a tela
        if (!iniciou || reduzido) {
            iniciou = true;
            x = alvoX;
            y = alvoY;
            vx = 0;
            vy = 0;
            aplicar();
            return;
        }

        if (!quadro) {
            ultimo = performance.now();
            quadro = requestAnimationFrame(passo);
        }
    });

    /* ---------- Cena 3D (Spline) ---------- */
    function falhou(erro) {
        console.error('Hero 3D:', erro);
        const spinner = loader.querySelector('.h3d-spinner');
        const texto = loader.querySelector('.h3d-loader-txt');
        if (spinner) spinner.hidden = true;
        if (texto) texto.textContent = 'Cena 3D indisponível';
    }

    import(RUNTIME_URL)
        .then(function (modulo) {
            const app = new modulo.Application(canvas);
            return app.load(CENA_URL);
        })
        .then(function () {
            loader.hidden = true;
        })
        .catch(falhou);
}