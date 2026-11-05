const botao = document.querySelector('.minichat-botao');
const painel = document.querySelector('.minichat');
const fechar = document.querySelector('.minichat-fechar');
const message = painel.querySelector('.message');
const chat = painel.querySelector('.chat');
const submit = painel.querySelector('.submit_message');
const telefone = painel.dataset.telefone;



function alternarChat() {
    painel.classList.toggle('aberto');
    if (painel.classList.contains('aberto')) message.focus();
}

botao.addEventListener('click', alternarChat);
fechar.addEventListener('click', alternarChat);




function normalizar(texto) {
    return texto
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase();
}


function distancia(a, b) {
    const tabela = [];

    for (let i = 0; i <= a.length; i++) {
        tabela[i] = [];
        for (let j = 0; j <= b.length; j++) {
            if (i === 0) {
                tabela[i][j] = j;
            } else if (j === 0) {
                tabela[i][j] = i;
            } else {
                const custoTroca = a[i - 1] === b[j - 1] ? 0 : 1;
                const tirar = tabela[i - 1][j] + 1;
                const colocar = tabela[i][j - 1] + 1;
                const trocar = tabela[i - 1][j - 1] + custoTroca;
                tabela[i][j] = Math.min(tirar, colocar, trocar);
            }
        }
    }

    return tabela[a.length][b.length];
}

function tolerancia(palavra) {
    if (palavra.length <= 3) return 0;
    if (palavra.length <= 7) return 1;
    return 2;
}


function casaPalavras(texto, palavras) {
    const digitadas = texto.split(/[^a-z0-9]+/).filter(Boolean);

    for (const chave of palavras) {
        for (const digitada of digitadas) {
            if (distancia(digitada, chave) <= tolerancia(chave)) return true;
        }
    }
    return false;
}



const scripts = [
    {
        palavras: ['preco', 'precos', 'custo', 'custa', 'orcamento', 'cotacao', 'valor', 'quanto'],
        reply: 'Os valores aparecem em cada produto do catálogo. Para projetos sob medida ou compras em quantidade, fale com a nossa equipe pelo WhatsApp ou por e-mail.'
    },
    {
        palavras: ['produto', 'produtos', 'catalogo', 'robo', 'robos', 'categoria', 'categorias',
                   'industrial', 'industriais', 'humanoide', 'humanoides', 'social', 'sociais',
                   'agricola', 'agricolas', 'colaborativo', 'colaborativos'],
        reply: 'Trabalhamos com robôs industriais, humanoides, sociais, agrícolas e colaborativos. Veja a seção "Categorias" aqui na página ou o catálogo completo.'
    },
    {
        palavras: ['entrega', 'frete', 'prazo', 'envio', 'enviar'],
        reply: 'Prazo e frete variam conforme o produto e o destino. Fale com a nossa equipe para confirmar.'
    },
    {
        palavras: ['whatsapp', 'zap', 'telefone', 'ligar', 'contato', 'atendente', 'ajuda', 'falar', 'email'],
        reply: 'Você pode falar com a nossa equipe pelo WhatsApp ou por e-mail. Os botões estão no final da página, em "Precisa de ajuda para escolher?".'
    },
    {
        regex: /^(o+i+|ola+|opa|e ai|bom dia|boa tarde|boa noite)\b/,
        reply: 'Olá! Pergunte sobre produtos, categorias, preços ou entrega.'
    }
];

const fallback = 'Desculpe, não consegui ajudar com isso! O jeito mais rápido de falar com a gente é pelo telefone: ' + telefone;

function responder(texto) {
    const t = normalizar(texto);

    for (const regra of scripts) {
        const casou = regra.palavras ? casaPalavras(t, regra.palavras) : regra.regex.test(t);
        if (casou) return regra.reply;
    }
    return fallback;
}



function addBubble(role, text) {
    const bubble = document.createElement('div');
    bubble.classList.add('bubble', role);
    bubble.innerText = text;
    chat.appendChild(bubble);
    chat.scrollTop = chat.scrollHeight;
}

function showTyping() {
    const indicator = document.createElement('div');
    indicator.classList.add('bubble', 'assistant');
    indicator.innerHTML = '<span></span><span></span><span></span>';
    chat.appendChild(indicator);
    chat.scrollTop = chat.scrollHeight;
    return indicator;
}

addBubble('assistant', 'Olá! Como posso ajudar você hoje?');

let aguardando = false;

function submitMessage() {
    const text = message.value.trim();
    if (!text || aguardando) return;

    addBubble('user', text);
    message.value = '';

    aguardando = true;
    const indicator = showTyping();

    setTimeout(() => {
        indicator.remove();
        addBubble('assistant', responder(text));
        aguardando = false;
    }, 1200);
}

submit.addEventListener('click', submitMessage);

message.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
        e.preventDefault();
        submitMessage();
    }
});