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


function normalizar(texto) {
    return texto
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[?!.,;:]/g, " ")
        .replace(/\s+/g, " ")
        .trim();
}



const scripts = [
    // 01. PREÇOS E ORÇAMENTOS
    {
        id: "preco",
        palavras: ["preco", "precos", "valor", "valores", "quanto custa", "quanto custa um robo"],
        reply: "Os preços estão disponíveis nas páginas dos produtos. Para projetos personalizados ou compras em quantidade, solicite um orçamento à nossa equipe."
    },
    {
        id: "orcamento",
        palavras: ["orcamento", "orcamentos", "cotacao", "fazer cotacao", "solicitar orcamento"],
        reply: "Para solicitar um orçamento, informe quais produtos ou componentes deseja adquirir e entre em contato com nossa equipe pelo WhatsApp ou e-mail."
    },
    {
        id: "desconto",
        palavras: ["desconto", "descontos", "promocao", "promocoes", "cupom", "cupom desconto"],
        reply: "Para consultar descontos, promoções e condições especiais, entre em contato com nossa equipe."
    },
    {
        id: "atacado",
        palavras: ["atacado", "atacado", "grande quantidade", "comprar em quantidade", "compra em volume"],
        reply: "Temos atendimento para solicitações de compras em quantidade. Entre em contato para consultar condições comerciais."
    },
    {
        id: "preco_personalizado",
        palavras: ["preco personalizado", "projeto sob medida", "preco sob consulta"],
        reply: "Projetos personalizados podem exigir uma análise individual. Envie os detalhes da sua necessidade para nossa equipe."
    },

    // 02. CATÁLOGO E NAVEGAÇÃO
    {
        id: "catalogo",
        palavras: ["catalogo", "catalogo completo", "lista de produtos", "ver produtos", "todos os produtos"],
        reply: "Você pode consultar nossos produtos na seção Catálogo. Lá estão reunidos os robôs e componentes disponíveis na RoboParts."
    },
    {
        id: "categorias",
        palavras: ["categorias", "categoria", "tipos de robo", "tipos de robos", "classificacao"],
        reply: "Trabalhamos com diferentes categorias, incluindo robôs industriais, humanoides, sociais, agrícolas e colaborativos. Consulte a seção Categorias para conhecer cada uma."
    },
    {
        id: "pagina_inicial",
        palavras: ["inicio", "home", "pagina inicial", "voltar ao inicio"],
        reply: "Para voltar à página inicial, utilize o logotipo da RoboParts ou a opção Início no menu de navegação."
    },
    {
        id: "pagina_produto",
        palavras: ["pagina do produto", "detalhes do produto", "informacoes do produto", "descricao do produto"],
        reply: "Na página de cada produto você pode consultar suas informações, características e disponibilidade, quando esses dados estiverem cadastrados."
    },
    {
        id: "pesquisa",
        palavras: ["pesquisar", "barra de pesquisa", "buscar produto", "nao encontro produto", "procurar produto"],
        reply: "Utilize a barra de pesquisa para procurar pelo nome, modelo ou tipo de produto. Se não encontrar, nossa equipe poderá ajudar."
    },
    {
        id: "navegacao",
        palavras: ["como navegar", "como usar o site", "onde encontro", "como funciona o site"],
        reply: "Você pode navegar pelo menu principal, acessar as categorias, consultar o catálogo e abrir as páginas individuais dos produtos."
    },
    {
        id: "sobre_empresa",
        palavras: ["sobre a roboparts", "quem somos", "sobre nos", "empresa", "historia da empresa"],
        reply: "A RoboParts é uma plataforma voltada à comercialização de robôs e componentes robóticos de diferentes categorias. Consulte nossa página institucional para conhecer mais."
    },
    {
        id: "contato_pagina",
        palavras: ["pagina de contato", "onde fica o contato", "como entrar em contato"],
        reply: "Os canais de atendimento estão disponíveis no final da página, na seção Precisa de ajuda para escolher?."
    },
    {
        id: "produtos_novos",
        palavras: ["novidades", "lancamentos", "novos produtos", "produtos novos"],
        reply: "Para conhecer novidades e lançamentos, acompanhe nosso catálogo e consulte nossa equipe sobre os produtos mais recentes."
    },

    // 03. TIPOS DE ROBÔS
    {
        id: "robo_industrial",
        palavras: ["robo industrial", "robos industriais", "automacao industrial"],
        reply: "Robôs industriais são utilizados em processos de fabricação, montagem, movimentação e automação. Consulte nossa categoria Industrial para conhecer os modelos."
    },
    {
        id: "robo_humanoide",
        palavras: ["humanoide", "humanoides", "robo humanoide", "robo com forma humana"],
        reply: "Robôs humanoides possuem estrutura inspirada no corpo humano e podem ser utilizados em pesquisa, interação e diferentes aplicações tecnológicas."
    },
    {
        id: "robo_social",
        palavras: ["robo social", "robos sociais", "robo de interacao", "robo para atendimento"],
        reply: "Robôs sociais são desenvolvidos para interagir com pessoas e podem atuar em atendimento, educação, recepção e outros ambientes."
    },
    {
        id: "robo_agricola",
        palavras: ["robo agricola", "robos agricolas", "agricultura robotizada"],
        reply: "Robôs agrícolas auxiliam atividades do campo, como monitoramento, inspeção e automação de processos agrícolas."
    },
    {
        id: "robo_colaborativo",
        palavras: ["robo colaborativo", "robos colaborativos", "cobot", "cobots"],
        reply: "Robôs colaborativos, também conhecidos como cobots, são projetados para trabalhar em ambientes compartilhados com pessoas, conforme os requisitos de segurança de cada aplicação."
    },
    {
        id: "robo_autonomo",
        palavras: ["robo autonomo", "robos autonomos", "autonomia robotica"],
        reply: "Robôs autônomos executam tarefas com diferentes níveis de autonomia, utilizando sensores, processamento e sistemas de controle."
    },
    {
        id: "robo_movel",
        palavras: ["robo movel", "robos moveis", "robo sobre rodas", "plataforma movel"],
        reply: "Robôs móveis são plataformas capazes de se deslocar e podem ser utilizados em logística, pesquisa, inspeção e automação."
    },
    {
        id: "robo_logistico",
        palavras: ["robo logistico", "robo de transporte", "robo de armazem", "amr", "agv"],
        reply: "Robôs logísticos auxiliam no transporte e na movimentação de materiais. Modelos AGV e AMR são utilizados em diferentes aplicações de logística."
    },
    {
        id: "robo_educacional",
        palavras: ["robo educacional", "robotica educacional", "robo para escola", "robo para aprender"],
        reply: "Robôs educacionais são utilizados no ensino de programação, eletrônica, automação e conceitos de robótica."
    },
    {
        id: "robo_domestico",
        palavras: ["robo domestico", "robo para casa", "robo residencial", "robo de limpeza"],
        reply: "Robôs domésticos podem auxiliar em tarefas residenciais, como limpeza, monitoramento e assistência."
    },
    {
        id: "robo_medico",
        palavras: ["robo medico", "robo hospitalar", "robotica medica", "robo cirurgico"],
        reply: "A robótica médica possui aplicações em assistência hospitalar, pesquisa e procedimentos especializados. Consulte nossa equipe para verificar produtos dessa categoria."
    },
    {
        id: "robo_militar",
        palavras: ["robo militar", "robo de defesa", "robotica militar"],
        reply: "Existem aplicações robóticas voltadas à pesquisa e a diferentes atividades de defesa. Consulte nossa equipe para verificar se temos produtos relacionados."
    },
    {
        id: "robo_aereo",
        palavras: ["drone", "drones", "robo aereo", "veiculo aereo nao tripulado", "vants"],
        reply: "Drones são sistemas robóticos aéreos utilizados em inspeção, monitoramento, agricultura e outras aplicações."
    },
    {
        id: "robo_submarino",
        palavras: ["robo submarino", "robo aquatico", "rov", "robotica submarina"],
        reply: "Robôs aquáticos e submarinos podem ser utilizados em inspeções, pesquisas e operações subaquáticas. Consulte nosso catálogo para verificar os modelos disponíveis."
    },
    {
        id: "robo_personalizado",
        palavras: ["robo personalizado", "robo customizado", "criar um robo", "desenvolver um robo"],
        reply: "Para uma solução personalizada, entre em contato com nossa equipe e explique a aplicação desejada, as funções e os requisitos do projeto."
    },

    // 04. PEÇAS E COMPONENTES ROBÓTICOS
    {
        id: "pecas",
        palavras: ["pecas", "peca", "pecas para robo", "componentes", "componentes roboticos"],
        reply: "A RoboParts também trabalha com componentes robóticos. Consulte a categoria de peças e componentes para encontrar os itens disponíveis."
    },
    {
        id: "motores",
        palavras: ["motor", "motores", "motor eletrico", "motor para robo"],
        reply: "Motores são componentes responsáveis por gerar movimento em sistemas robóticos. Consulte as especificações de cada modelo para verificar sua aplicação."
    },
    {
        id: "servomotores",
        palavras: ["servo motor", "servomotor", "servomotores", "servo"],
        reply: "Servomotores permitem controlar posição e movimento com precisão. Verifique torque, tensão, dimensões e protocolo antes de escolher um modelo."
    },
    {
        id: "motores_passo",
        palavras: ["motor de passo", "motores de passo", "stepper"],
        reply: "Motores de passo realizam movimentos em incrementos controlados e são utilizados em impressoras 3D, máquinas CNC e projetos robóticos."
    },
    {
        id: "sensores",
        palavras: ["sensor", "sensores", "sensor para robo"],
        reply: "Sensores permitem que robôs detectem informações do ambiente. Temos diferentes tipos de tecnologias e aplicações, conforme os produtos disponíveis no catálogo."
    },
    {
        id: "sensor_distancia",
        palavras: ["sensor de distancia", "ultrassonico", "sensor ultrassonico", "sensor de proximidade"],
        reply: "Sensores de distância e proximidade ajudam a identificar objetos e obstáculos. Confira o alcance e a tecnologia de cada modelo."
    },
    {
        id: "sensor_lidar",
        palavras: ["lidar", "sensor lidar", "scanner laser"],
        reply: "Sensores LiDAR utilizam luz laser para medir distâncias e mapear ambientes. São comuns em robôs móveis e sistemas de navegação."
    },
    {
        id: "camera",
        palavras: ["camera", "cameras", "visao computacional", "camera robotica"],
        reply: "Câmeras podem ser utilizadas em sistemas de visão computacional, reconhecimento de objetos e monitoramento. Verifique as especificações de cada produto."
    },
    {
        id: "controladores",
        palavras: ["controlador", "controladores", "placa controladora", "controle de robo"],
        reply: "Controladores são responsáveis pelo processamento e gerenciamento das funções do robô. Confira a compatibilidade com os demais componentes."
    },
    {
        id: "arduino",
        palavras: ["arduino", "placa arduino", "uno", "mega"],
        reply: "Arduino é uma plataforma muito utilizada em projetos de eletrônica e robótica. Consulte nosso catálogo para verificar placas e componentes compatíveis."
    },
    {
        id: "raspberry",
        palavras: ["raspberry pi", "raspberry", "computador de placa unica"],
        reply: "Raspberry Pi é um computador compacto utilizado em projetos de automação, visão computacional e robótica. Consulte os modelos disponíveis."
    },
    {
        id: "baterias",
        palavras: ["bateria", "baterias", "bateria para robo", "alimentacao"],
        reply: "A escolha da bateria depende da tensão, capacidade, corrente e consumo do sistema. Confira os requisitos do seu equipamento antes de comprar."
    },
    {
        id: "fonte",
        palavras: ["fonte", "fontes", "fonte de alimentacao", "alimentador"],
        reply: "Fontes de alimentação fornecem energia aos componentes eletrônicos. Verifique tensão, corrente e tipo de conexão exigidos pelo equipamento."
    },
    {
        id: "cabos",
        palavras: ["cabo", "cabos", "fios", "fiação", "fiação eletrica"],
        reply: "Cabos e fios fazem as conexões elétricas e de comunicação entre componentes. Confira o tipo de conexão e as especificações necessárias."
    },
    {
        id: "conectores",
        palavras: ["conector", "conectores", "plug", "terminal", "adaptador"],
        reply: "Conectores e adaptadores permitem interligar componentes. Verifique o padrão, as dimensões e a compatibilidade antes da compra."
    },
    {
        id: "engrenagens",
        palavras: ["engrenagem", "engrenagens", "reducao mecanica", "redutor"],
        reply: "Engrenagens e redutores são utilizados para transmitir movimento e ajustar velocidade e torque em sistemas mecânicos."
    },
    {
        id: "rodas",
        palavras: ["roda", "rodas", "rodizio", "esteira", "rodas para robo"],
        reply: "Rodas e sistemas de locomoção variam conforme o peso, terreno e tipo de movimentação do robô."
    },
    {
        id: "garra",
        palavras: ["garra", "garras", "garra robotica", "garra mecanica"],
        reply: "Garras robóticas são utilizadas para segurar e manipular objetos. A escolha depende do peso, formato e tipo de material manipulado."
    },
    {
        id: "braco",
        palavras: ["braco robotico", "bracos roboticos", "braco mecanico", "manipulador robotico"],
        reply: "Braços robóticos são utilizados para manipulação, montagem, movimentação e outras tarefas. Consulte os modelos e suas capacidades."
    },
    {
        id: "estrutura",
        palavras: ["estrutura", "chassi", "carcaca", "suporte", "base robotica"],
        reply: "Estruturas, chassis e suportes formam a base física de sistemas robóticos. Verifique dimensões e pontos de fixação."
    },
    {
        id: "rolamentos",
        palavras: ["rolamento", "rolamentos", "bucha", "eixo"],
        reply: "Rolamentos, buchas e eixos auxiliam na sustentação e movimentação de componentes mecânicos."
    },
    {
        id: "display",
        palavras: ["display", "tela", "lcd", "oled", "visor"],
        reply: "Displays podem ser utilizados para apresentar informações e permitir interação com sistemas eletrônicos."
    },
    {
        id: "led",
        palavras: ["led", "leds", "iluminacao", "luz"],
        reply: "LEDs são componentes utilizados para sinalização, iluminação e indicação de estados em projetos eletrônicos."
    },
    {
        id: "modulos",
        palavras: ["modulo", "modulos", "modulo eletronico", "modulo de comunicacao"],
        reply: "Módulos eletrônicos adicionam funções específicas aos projetos, como comunicação, controle e leitura de sensores."
    },
    {
        id: "placas",
        palavras: ["placa eletronica", "placas eletronicas", "circuito", "pcb"],
        reply: "Placas eletrônicas integram componentes e circuitos. Consulte as especificações para verificar a aplicação de cada modelo."
    },
    {
        id: "atuadores",
        palavras: ["atuador", "atuadores", "atuador linear", "atuador eletrico"],
        reply: "Atuadores transformam energia em movimento e podem realizar deslocamentos lineares ou rotacionais."
    },
    {
        id: "pneumatica",
        palavras: ["pneumatica", "pneumatico", "cilindro pneumatico", "valvula"],
        reply: "Componentes pneumáticos utilizam ar comprimido para executar movimentos e controlar mecanismos em sistemas automatizados."
    },
    {
        id: "hidraulica",
        palavras: ["hidraulica", "hidraulico", "cilindro hidraulico"],
        reply: "Sistemas hidráulicos utilizam fluidos sob pressão para gerar força e movimento em aplicações mecânicas."
    },
    {
        id: "pecas_reposicao",
        palavras: ["reposicao", "peca de reposicao", "pecas de reposicao", "substituir componente"],
        reply: "Para encontrar uma peça de reposição, informe o modelo do equipamento e, se possível, o código ou referência do componente."
    },

    // 05. COMPATIBILIDADE E ESPECIFICAÇÕES
    {
        id: "compatibilidade",
        palavras: ["compatibilidade", "compativel", "funciona com", "serve para", "serve no meu robo"],
        reply: "A compatibilidade depende do modelo e das especificações técnicas. Envie os modelos dos componentes para nossa equipe verificar."
    },
    {
        id: "especificacoes",
        palavras: ["especificacoes", "ficha tecnica", "dados tecnicos", "datasheet"],
        reply: "As especificações técnicas podem ser consultadas na página do produto, quando disponibilizadas. Caso precise de mais informações, fale com nossa equipe."
    },
    {
        id: "dimensoes",
        palavras: ["dimensoes", "medidas", "tamanho", "comprimento", "largura", "altura"],
        reply: "As dimensões variam conforme o modelo. Consulte a ficha técnica do produto ou solicite essa informação à nossa equipe."
    },
    {
        id: "peso",
        palavras: ["peso", "massa", "quanto pesa", "capacidade de carga"],
        reply: "O peso e a capacidade de carga dependem do produto. Consulte suas especificações técnicas para obter os valores corretos."
    },
    {
        id: "voltagem",
        palavras: ["voltagem", "tensao", "volts", "12v", "24v", "5v"],
        reply: "Verifique a tensão exigida pelo componente antes de conectá-lo. A tensão incorreta pode causar danos ao equipamento."
    },
    {
        id: "corrente",
        palavras: ["corrente eletrica", "amperagem", "amperes", "ampere"],
        reply: "A corrente elétrica necessária varia conforme o componente e sua aplicação. Consulte a ficha técnica para verificar os requisitos."
    },
    {
        id: "torque",
        palavras: ["torque", "forca do motor", "capacidade de forca"],
        reply: "O torque indica a capacidade de um motor de produzir força rotacional. Compare esse valor com a necessidade do seu projeto."
    },
    {
        id: "precisao",
        palavras: ["precisao", "resolucao", "margem de erro"],
        reply: "A precisão depende da tecnologia e das características de cada componente. Consulte as especificações do modelo desejado."
    },
    {
        id: "alcance",
        palavras: ["alcance", "distancia maxima", "area de trabalho"],
        reply: "O alcance varia de acordo com o modelo e a aplicação. Consulte a ficha técnica do equipamento."
    },
    {
        id: "velocidade",
        palavras: ["velocidade", "rpm", "rapido", "rotacao"],
        reply: "A velocidade de operação depende das características do motor ou equipamento. Consulte os dados técnicos do produto."
    },
    {
        id: "programacao",
        palavras: ["programacao", "programar robo", "codigo", "software"],
        reply: "A programação depende do controlador e do sistema utilizado. Consulte a documentação do fabricante ou fale com nossa equipe sobre o modelo desejado."
    },
    {
        id: "linguagens",
        palavras: ["python", "c++", "cpp", "linguagem de programacao", "ros", "ros2"],
        reply: "Diferentes sistemas robóticos podem utilizar Python, C++, ROS e outras tecnologias. A compatibilidade depende do equipamento."
    },
    {
        id: "conectividade",
        palavras: ["bluetooth", "wifi", "wi-fi", "wireless", "conexao sem fio"],
        reply: "As opções de conectividade dependem do modelo. Confira se o produto oferece Bluetooth, Wi-Fi ou outro protocolo de comunicação."
    },
    {
        id: "protocolo",
        palavras: ["usb", "uart", "i2c", "spi", "can", "ethernet", "protocolo"],
        reply: "A comunicação entre componentes depende dos protocolos suportados por cada equipamento. Confira a ficha técnica antes de escolher."
    },

    // 06. ENTREGA E DISPONIBILIDADE
    {
        id: "entrega",
        palavras: ["entrega", "entregar", "receber produto", "quando chega"],
        reply: "O prazo de entrega depende do produto, da disponibilidade e do endereço de destino. Entre em contato para consultar uma estimativa."
    },
    {
        id: "frete",
        palavras: ["frete", "valor do frete", "quanto custa o envio"],
        reply: "O valor do frete varia conforme o produto e o destino. Nossa equipe pode ajudar a consultar as condições de envio."
    },
    {
        id: "prazo",
        palavras: ["prazo", "prazo de entrega", "tempo de entrega", "demora para chegar"],
        reply: "O prazo pode variar conforme a disponibilidade do produto e a região de entrega. Consulte nossa equipe para confirmar."
    },
    {
        id: "rastreamento",
        palavras: ["rastreamento", "rastrear pedido", "codigo de rastreio", "acompanhar pedido"],
        reply: "Para consultar o rastreamento, entre em contato com nossa equipe informando o número do pedido."
    },
    {
        id: "pedido",
        palavras: ["pedido", "meu pedido", "status do pedido", "acompanhar compra"],
        reply: "Para consultar o status de um pedido, entre em contato com nossa equipe e informe o número da compra."
    },
    {
        id: "disponibilidade",
        palavras: ["disponibilidade", "disponivel", "tem em estoque", "estoque", "tem esse produto"],
        reply: "A disponibilidade pode variar. Consulte a página do produto ou fale com nossa equipe para confirmar o estoque."
    },
    {
        id: "produto_esgotado",
        palavras: ["esgotado", "sem estoque", "indisponivel", "acabou o estoque"],
        reply: "Se o produto estiver indisponível, entre em contato para consultar uma possível previsão de reposição ou alternativas."
    },
    {
        id: "envio",
        palavras: ["envio", "enviar", "transportadora", "transportadoras"],
        reply: "As condições de envio dependem do produto e do destino. Entre em contato com nossa equipe para consultar as opções disponíveis."
    },

    // 07. PAGAMENTO E COMPRA
    {
        id: "pagamento",
        palavras: ["pagamento", "pagar", "formas de pagamento", "como pagar"],
        reply: "Para consultar as formas de pagamento aceitas, verifique as opções apresentadas na finalização da compra ou entre em contato com nossa equipe."
    },
    {
        id: "parcelamento",
        palavras: ["parcelamento", "parcelar", "parcelas", "vezes sem juros"],
        reply: "As condições de parcelamento dependem do produto e da forma de pagamento. Consulte as opções disponíveis na compra."
    },
    {
        id: "pix",
        palavras: ["pix", "pagar no pix", "chave pix"],
        reply: "Para verificar a disponibilidade de pagamento por Pix, consulte as opções apresentadas na finalização da compra."
    },
    {
        id: "boleto",
        palavras: ["boleto", "boleto bancario"],
        reply: "A disponibilidade de pagamento por boleto deve ser confirmada nas opções de pagamento da loja."
    },
    {
        id: "cartao",
        palavras: ["cartao", "cartao de credito", "cartao de debito"],
        reply: "Consulte as opções de cartão disponíveis na finalização da compra."
    },
    {
        id: "nota_fiscal",
        palavras: ["nota fiscal", "nf", "nota fiscal eletronica", "danfe"],
        reply: "Para dúvidas sobre nota fiscal, entre em contato com nossa equipe informando os dados da compra."
    },
    {
        id: "comprar",
        palavras: ["como comprar", "quero comprar", "fazer compra", "realizar compra"],
        reply: "Escolha o produto no catálogo, consulte suas informações e siga as instruções de compra disponíveis na página."
    },
    {
        id: "pedido_empresa",
        palavras: ["compra empresarial", "comprar para empresa", "cnpj", "pessoa juridica"],
        reply: "Para compras empresariais, entre em contato com nossa equipe para consultar as condições comerciais e os procedimentos necessários."
    },

    // 08. GARANTIA E SUPORTE
    {
        id: "garantia",
        palavras: ["garantia", "tempo de garantia", "garantia do produto"],
        reply: "As condições e o prazo de garantia dependem do produto e de seu fabricante. Consulte as informações específicas antes da compra."
    },
    {
        id: "devolucao",
        palavras: ["devolucao", "devolver produto", "devolver compra"],
        reply: "Para solicitar uma devolução, entre em contato com nossa equipe informando o número do pedido e o motivo da solicitação."
    },
    {
        id: "troca",
        palavras: ["troca", "trocar produto", "substituicao"],
        reply: "Para consultar as condições de troca, entre em contato com nossa equipe e informe os detalhes da compra."
    },
    {
        id: "defeito",
        palavras: ["defeito", "produto com defeito", "nao funciona", "veio quebrado"],
        reply: "Sentimos pelo problema. Entre em contato com nossa equipe, informe o modelo do produto e descreva o ocorrido para receber orientação."
    },
    {
        id: "assistencia",
        palavras: ["assistencia tecnica", "reparo", "conserto", "manutencao"],
        reply: "Para consultar possibilidades de assistência técnica, reparo ou manutenção, informe o modelo do equipamento à nossa equipe."
    },
    {
        id: "manual",
        palavras: ["manual", "manual de usuario", "manual tecnico", "instrucoes"],
        reply: "Consulte a página do produto para verificar se o manual está disponível. Caso não esteja, solicite a documentação à nossa equipe."
    },
    {
        id: "instalacao",
        palavras: ["instalacao", "montagem", "como instalar", "como montar"],
        reply: "A instalação e montagem dependem do equipamento. Consulte o manual do fabricante e as orientações específicas do produto."
    },
    {
        id: "configuracao",
        palavras: ["configuracao", "configurar", "ajustar robo", "calibracao"],
        reply: "A configuração depende do modelo e do sistema de controle. Consulte o manual técnico ou fale com nossa equipe."
    },
    {
        id: "problema_conexao",
        palavras: ["nao conecta", "problema de conexao", "falha de comunicacao"],
        reply: "Verifique as conexões, a alimentação e os protocolos suportados pelo equipamento. Se o problema continuar, entre em contato com nossa equipe."
    },
    {
        id: "peca_danificada",
        palavras: ["peca danificada", "componente danificado", "componente queimado"],
        reply: "Para identificar a peça adequada para substituição, informe o modelo do equipamento e a referência do componente danificado."
    },

    // 09. SOBRE A ROBÓTICA
    {
        id: "o_que_e_robotica",
        palavras: ["o que e robotica", "definicao de robotica", "robotica"],
        reply: "Robótica é a área que reúne conhecimentos de mecânica, eletrônica, programação e controle para desenvolver sistemas capazes de executar tarefas."
    },
    {
        id: "como_funciona_robo",
        palavras: ["como funciona um robo", "funcionamento de um robo"],
        reply: "De forma geral, um robô combina estrutura mecânica, sensores, controladores e atuadores para perceber o ambiente e executar ações."
    },
    {
        id: "inteligencia_artificial",
        palavras: ["inteligencia artificial", "ia", "machine learning", "aprendizado de maquina"],
        reply: "A inteligência artificial pode permitir que sistemas robóticos reconheçam padrões, interpretem informações e tomem decisões conforme sua programação."
    },
    {
        id: "automacao",
        palavras: ["automacao", "automacao robotica", "automatizar processo"],
        reply: "A automação utiliza tecnologias para executar processos com menor necessidade de intervenção humana. Robôs são uma das ferramentas utilizadas nesse campo."
    },
    {
        id: "diferenca_robo",
        palavras: ["diferenca entre robo e maquina", "robo ou maquina"],
        reply: "Uma máquina pode executar uma função específica, enquanto um robô geralmente combina sensores, controle e atuadores para realizar tarefas de forma programada ou autônoma."
    },
    {
        id: "iniciante",
        palavras: ["sou iniciante", "comecar na robotica", "aprender robotica", "primeiro robo"],
        reply: "Se você está começando na robótica, pode explorar kits educacionais, placas controladoras, sensores e motores. Nossa equipe pode ajudar a identificar componentes adequados ao seu projeto."
    },
    {
        id: "projeto_escolar",
        palavras: ["projeto escolar", "trabalho escolar", "feira de ciencias", "projeto faculdade"],
        reply: "Para projetos escolares e acadêmicos, é interessante definir primeiro o objetivo, o tamanho do robô e as funções desejadas. Depois, escolha os componentes compatíveis."
    },
    {
        id: "projeto_tcc",
        palavras: ["tcc", "projeto de pesquisa", "projeto academico", "universidade"],
        reply: "Para projetos acadêmicos, informe os requisitos técnicos e os componentes necessários. Nossa equipe pode ajudar a verificar os produtos disponíveis."
    },

    // 10. ATENDIMENTO E OUTRAS DÚVIDAS
    {
        id: "whatsapp",
        palavras: ["whatsapp", "zap", "telefone", "ligar", "contato", "atendente"],
        reply: "Você pode falar com nossa equipe pelo WhatsApp ou por e-mail. Os botões estão no final da página, na seção Precisa de ajuda para escolher?."
    },
    {
        id: "email",
        palavras: ["email", "e-mail", "correio eletronico"],
        reply: "Nosso canal de e-mail está disponível na seção de contato, no final da página."
    },
    {
        id: "atendimento",
        palavras: ["atendimento", "falar com vendedor", "falar com humano", "atendimento humano"],
        reply: "Claro! Nossa equipe pode ajudar com dúvidas comerciais e técnicas. Utilize os canais de WhatsApp ou e-mail no final da página."
    },
    {
        id: "horario",
        palavras: ["horario", "horario de atendimento", "funcionamento", "aberto agora"],
        reply: "Para consultar os horários de atendimento, verifique as informações na seção de contato ou fale com nossa equipe."
    },
    {
        id: "localizacao",
        palavras: ["endereco", "localizacao", "onde fica", "onde voces ficam"],
        reply: "Para consultar nossa localização e informações institucionais, acesse a seção de contato da RoboParts."
    },
    {
        id: "redes_sociais",
        palavras: ["instagram", "facebook", "linkedin", "redes sociais", "youtube"],
        reply: "Você pode encontrar nossos canais digitais pelos links disponibilizados no site, caso estejam disponíveis."
    },
    {
        id: "parceria",
        palavras: ["parceria", "parcerias", "ser parceiro", "representante"],
        reply: "Para apresentar uma proposta de parceria ou representação comercial, entre em contato com nossa equipe."
    },
    {
        id: "fornecedor",
        palavras: ["fornecedor", "fornecedores", "vender para roboparts", "fornecer produtos"],
        reply: "Se deseja apresentar produtos ou serviços como fornecedor, entre em contato com nossa equipe comercial."
    },
    {
        id: "trabalhar",
        palavras: ["trabalhar na roboparts", "vaga", "emprego", "curriculo", "oportunidade"],
        reply: "Para consultar oportunidades profissionais, entre em contato pelos canais institucionais disponíveis no site."
    },
    {
        id: "seguranca",
        palavras: ["seguranca", "site seguro", "compra segura", "dados pessoais"],
        reply: "Para dúvidas sobre segurança, privacidade e tratamento de dados, consulte as políticas e informações institucionais disponíveis no site."
    },
    {
        id: "politica_privacidade",
        palavras: ["politica de privacidade", "privacidade", "cookies", "dados pessoais"],
        reply: "As informações sobre privacidade e tratamento de dados devem estar disponíveis na Política de Privacidade do site."
    },
    {
        id: "termos",
        palavras: ["termos de uso", "termos e condicoes", "regras do site", "termos"],
        reply: "Consulte a página de Termos de Uso para conhecer as condições aplicáveis à utilização do site."
    },
    {
        id: "saudacao",
        palavras: ["oi", "ola", "opa", "bom dia", "boa tarde", "boa noite", "e ai", "tudo bem"],
        reply: "Olá! Sou o assistente virtual da RoboParts. Posso ajudar você a encontrar robôs, peças, componentes, informações sobre produtos, preços e entregas. O que procura?"
    },
    {
        id: "agradecimento",
        palavras: ["obrigado", "obrigada", "valeu", "agradeco", "muito obrigado"],
        reply: "Por nada! Se precisar de mais alguma informação sobre robôs ou componentes, estou à disposição."
    },
    {
        id: "despedida",
        palavras: ["tchau", "ate logo", "ate mais", "falou", "encerrar atendimento"],
        reply: "Até mais! A RoboParts agradece sua visita. Se precisar, estaremos à disposição para ajudar."
    },
    {
        id: "elogio",
        palavras: ["muito bom", "excelente", "gostei", "site bonito", "parabens", "boa"],
        reply: "Agradecemos pelo feedback! Ficamos felizes em saber que sua experiência com a RoboParts foi positiva."
    },
    {
        id: "reclamacao",
        palavras: ["reclamacao", "insatisfeito", "insatisfacao", "experiencia ruim", "problema com atendimento"],
        reply: "Lamentamos pela experiência. Entre em contato com nossa equipe e explique o ocorrido para que possamos encaminhar sua solicitação."
    },
    {
        id: "bot",
        palavras: ["voce e um robo", "voce e humano", "quem esta falando", "assistente virtual"],
        reply: "Sou o assistente virtual da RoboParts! Estou aqui para ajudar com informações sobre produtos, robôs, componentes e atendimento."
    },
    {
        id: "ajuda",
        palavras: ["ajuda", "preciso de ajuda", "pode me ajudar", "nao sei o que escolher"],
        reply: "Claro! Posso ajudar a encontrar o produto certo. Conte um pouco sobre o que você procura ou qual aplicação deseja realizar."
    },
    {
        id: "agradecimento_e_duvida",
        palavras: ["tenho uma duvida", "duvida", "pergunta", "queria saber"],
        reply: "Pode perguntar! Estou preparado para ajudar com informações sobre robôs, peças, componentes e produtos da RoboParts."
    },
    {
        id: "nao_entendi",
        palavras: ["nao entendi", "pode explicar", "explica melhor", "pode repetir"],
        reply: "Claro! Pode reformular sua pergunta ou me dizer qual produto ou assunto deseja entender melhor."
    },
    {
        id: "fora_escopo",
        palavras: ["receita", "filme", "musica", "futebol", "clima"],
        reply: "Posso ajudar principalmente com assuntos relacionados à robótica, produtos, componentes e serviços da RoboParts. O que gostaria de saber sobre esses assuntos?"
    },
    {
        id: "67",
        palavras: ["67", "farmar aura", "tchola", "bora bill"],
        reply: "o nível de carisma tá trancado no 67. Enquanto alguns só tentam entender a matemática, eu sigo farmando aura sem gastar UM %. 🤫🔥 Se você sabe o que é fazer o malabarismo imaginário do six-seven, você entendeu o código. Quem fica de fora perde o timing e toma -5000 de aura de graça. Entrei na sala e o ambiente simplesmente gesticulou. 👑🚀 #FarmarAura #SixSeven #67 #Brainrot #Estilo #BORABILL"
    }
];


const fallback = 'Desculpe, não consegui ajudar com isso! Pode reformular a pergunta? Se não, o jeito mais rápido de falar com a gente é pelo telefone: ' + telefone;

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