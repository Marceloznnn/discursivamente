document.addEventListener('DOMContentLoaded', function() {
    // Inicializar os componentes principais
    carregarComunidadesDestaque();
    carregarComunidadesPopulares();
    carregarDiscussoes();
    carregarEventos();
    carregarComunidadeMes();
    inicializarCategoriasTabs();
    inicializarSlider();
    inicializarFiltrosDiscussoes();
});

// =================== COMUNIDADES EM DESTAQUE ===================
function carregarComunidadesDestaque() {
    // Simulação de dados para comunidades em destaque
    const comunidadesDestaque = [
        {
            id: 1,
            nome: "Literatura Brasileira Contemporânea",
            membros: 3452,
            imagem: "/assets/images/comunidades/literatura-brasileira.jpg",
            categoria: "foruns"
        },
        {
            id: 2,
            nome: "Clube de Leitura - Ficção Científica",
            membros: 2718,
            imagem: "/assets/images/comunidades/ficcao-cientifica.jpg",
            categoria: "clubes-livro"
        },
        {
            id: 3,
            nome: "Grupo de Estudos Literários",
            membros: 1982,
            imagem: "/assets/images/comunidades/estudos-literarios.jpg",
            categoria: "grupos-estudo"
        },
        {
            id: 4,
            nome: "Poesia & Prosa - Análises Coletivas",
            membros: 2341,
            imagem: "/assets/images/comunidades/poesia-prosa.jpg",
            categoria: "foruns"
        }
    ];

    const container = document.getElementById('comunidades-destaque');
    if (!container) return;

    container.innerHTML = '';
    
    comunidadesDestaque.forEach(comunidade => {
        const card = criarCardComunidade(comunidade);
        container.appendChild(card);
    });
}

function criarCardComunidade(comunidade) {
    const card = document.createElement('div');
    card.className = 'comunidade-card';
    card.setAttribute('data-categoria', comunidade.categoria);
    card.setAttribute('data-id', comunidade.id);
    
    card.innerHTML = `
        <div class="comunidade-image" style="background-image: url('${comunidade.imagem}')">
            <div class="categoria-badge">${formatarCategoria(comunidade.categoria)}</div>
        </div>
        <div class="comunidade-info">
            <h3 class="comunidade-nome">${comunidade.nome}</h3>
            <div class="comunidade-meta">
                <span class="membros-count"><i class="icon-user"></i> ${formatarNumero(comunidade.membros)} membros</span>
            </div>
            <a href="/comunidade/${comunidade.id}" class="explorar-button">Explorar</a>
        </div>
    `;
    
    return card;
}

// =================== TABS DE CATEGORIAS ===================
function inicializarCategoriasTabs() {
    const tabsContainer = document.getElementById('categorias-tabs');
    const contentContainer = document.getElementById('categorias-content');
    
    if (!tabsContainer || !contentContainer) return;
    
    // Dados simulados para as diferentes categorias
    const categoriasDados = {
        'todos': [],  // Será preenchido com todas as comunidades
        'foruns': [
            {
                id: 5,
                nome: "Debate Filosófico na Literatura",
                membros: 1852,
                imagem: "/assets/images/comunidades/debate-filosofico.jpg",
                categoria: "foruns"
            },
            {
                id: 6,
                nome: "Literatura e Sociedade",
                membros: 2105,
                imagem: "/assets/images/comunidades/literatura-sociedade.jpg",
                categoria: "foruns"
            }
        ],
        'clubes-livro': [
            {
                id: 7,
                nome: "Clube do Livro - Clássicos Mundiais",
                membros: 3210,
                imagem: "/assets/images/comunidades/classicos-mundiais.jpg",
                categoria: "clubes-livro"
            },
            {
                id: 8,
                nome: "Clube de Leitura - Romances Históricos",
                membros: 1795,
                imagem: "/assets/images/comunidades/romances-historicos.jpg",
                categoria: "clubes-livro"
            }
        ],
        'grupos-estudo': [
            {
                id: 9,
                nome: "Estudos da Narrativa",
                membros: 1520,
                imagem: "/assets/images/comunidades/estudos-narrativa.jpg",
                categoria: "grupos-estudo"
            },
            {
                id: 10,
                nome: "Grupo de Pesquisa Literária",
                membros: 980,
                imagem: "/assets/images/comunidades/pesquisa-literaria.jpg",
                categoria: "grupos-estudo"
            }
        ],
        'eventos': [
            {
                id: 11,
                nome: "Festival Literário Virtual",
                membros: 2450,
                imagem: "/assets/images/comunidades/festival-literario.jpg",
                categoria: "eventos"
            },
            {
                id: 12,
                nome: "Encontros de Autores",
                membros: 1650,
                imagem: "/assets/images/comunidades/encontros-autores.jpg",
                categoria: "eventos"
            }
        ]
    };
    
    // Preencher a categoria "todos" com todas as comunidades
    categoriasDados.todos = [
        ...categoriasDados.foruns,
        ...categoriasDados['clubes-livro'],
        ...categoriasDados['grupos-estudo'],
        ...categoriasDados.eventos
    ];
    
    // Adicionar os event listeners aos botões de tab
    tabsContainer.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            // Remover a classe 'active' de todos os botões
            tabsContainer.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Adicionar a classe 'active' ao botão clicado
            this.classList.add('active');
            
            // Carregar o conteúdo da categoria
            const categoria = this.getAttribute('data-categoria');
            carregarConteudoCategoria(categoria, categoriasDados[categoria]);
        });
    });
    
    // Carregar a categoria "todos" por padrão
    carregarConteudoCategoria('todos', categoriasDados.todos);
}

function carregarConteudoCategoria(categoria, dados) {
    const contentContainer = document.getElementById('categorias-content');
    if (!contentContainer) return;
    
    contentContainer.innerHTML = '';
    
    if (dados.length === 0) {
        contentContainer.innerHTML = '<p class="no-content">Nenhuma comunidade encontrada nesta categoria.</p>';
        return;
    }
    
    const grid = document.createElement('div');
    grid.className = 'comunidades-grid';
    
    dados.forEach(comunidade => {
        const card = criarCardComunidade(comunidade);
        grid.appendChild(card);
    });
    
    contentContainer.appendChild(grid);
}

// =================== SLIDER DE COMUNIDADES POPULARES ===================
function carregarComunidadesPopulares() {
    // Simulação de dados para comunidades populares
    const comunidadesPopulares = [
        {
            id: 13,
            nome: "Livros que Viraram Filmes",
            membros: 4782,
            imagem: "/assets/images/comunidades/livros-filmes.jpg",
            categoria: "foruns"
        },
        {
            id: 14,
            nome: "Escritores Independentes",
            membros: 3921,
            imagem: "/assets/images/comunidades/escritores-independentes.jpg",
            categoria: "foruns"
        },
        {
            id: 15,
            nome: "Literatura Fantástica",
            membros: 5437,
            imagem: "/assets/images/comunidades/literatura-fantastica.jpg",
            categoria: "clubes-livro"
        },
        {
            id: 16,
            nome: "Crítica Literária",
            membros: 2876,
            imagem: "/assets/images/comunidades/critica-literaria.jpg",
            categoria: "grupos-estudo"
        },
        {
            id: 17,
            nome: "Narrativas Interativas",
            membros: 1953,
            imagem: "/assets/images/comunidades/narrativas-interativas.jpg",
            categoria: "foruns"
        },
        {
            id: 18,
            nome: "Feira do Livro Virtual",
            membros: 3269,
            imagem: "/assets/images/comunidades/feira-livro.jpg",
            categoria: "eventos"
        }
    ];

    const container = document.getElementById('comunidades-container');
    if (!container) return;

    container.innerHTML = '';
    
    comunidadesPopulares.forEach(comunidade => {
        const card = criarCardComunidade(comunidade);
        container.appendChild(card);
    });
    
    // Criar os dots de paginação
    criarPaginationDots(comunidadesPopulares.length);
}

function inicializarSlider() {
    const wrapper = document.getElementById('comunidades-wrapper');
    const container = document.getElementById('comunidades-container');
    const prevButton = document.getElementById('comunidades-prev-button');
    const nextButton = document.getElementById('comunidades-next-button');
    const paginationDots = document.getElementById('comunidades-pagination-dots');
    
    if (!wrapper || !container || !prevButton || !nextButton || !paginationDots) return;
    
    let currentPosition = 0;
    const itemWidth = 280; // Largura do card + margem
    const visibleItems = Math.floor(wrapper.offsetWidth / itemWidth);
    const totalItems = container.children.length;
    const maxPosition = Math.max(0, totalItems - visibleItems);
    
    // Atualizar dots iniciais
    atualizarPaginationDots(0);
    
    prevButton.addEventListener('click', () => {
        if (currentPosition > 0) {
            currentPosition--;
            moverSlider(currentPosition);
            atualizarPaginationDots(currentPosition);
        }
    });
    
    nextButton.addEventListener('click', () => {
        if (currentPosition < maxPosition) {
            currentPosition++;
            moverSlider(currentPosition);
            atualizarPaginationDots(currentPosition);
        }
    });
    
    // Função para mover o slider
    function moverSlider(position) {
        const translateX = -position * itemWidth;
        container.style.transform = `translateX(${translateX}px)`;
    }
    
    // Inicializar os dots de paginação com click events
    paginationDots.querySelectorAll('.pagination-dot').forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentPosition = Math.min(index, maxPosition);
            moverSlider(currentPosition);
            atualizarPaginationDots(currentPosition);
        });
    });
    
    // Ajustar o slider em caso de redimensionamento da janela
    window.addEventListener('resize', () => {
        const newVisibleItems = Math.floor(wrapper.offsetWidth / itemWidth);
        const newMaxPosition = Math.max(0, totalItems - newVisibleItems);
        
        if (currentPosition > newMaxPosition) {
            currentPosition = newMaxPosition;
            moverSlider(currentPosition);
        }
    });
}

function criarPaginationDots(total) {
    const dotsContainer = document.getElementById('comunidades-pagination-dots');
    if (!dotsContainer) return;
    
    dotsContainer.innerHTML = '';
    
    const visibleItems = Math.floor(document.getElementById('comunidades-wrapper').offsetWidth / 280);
    const pages = Math.ceil(total / visibleItems);
    
    for (let i = 0; i < pages; i++) {
        const dot = document.createElement('span');
        dot.className = 'pagination-dot';
        if (i === 0) dot.classList.add('active');
        dotsContainer.appendChild(dot);
    }
}

function atualizarPaginationDots(position) {
    const dotsContainer = document.getElementById('comunidades-pagination-dots');
    if (!dotsContainer) return;
    
    const dots = dotsContainer.querySelectorAll('.pagination-dot');
    
    dots.forEach((dot, index) => {
        if (index === position) {
            dot.classList.add('active');
        } else {
            dot.classList.remove('active');
        }
    });
}

// =================== DISCUSSÕES EM ANDAMENTO ===================
function carregarDiscussoes() {
    // Simulação de dados para discussões
    const discussoes = [
        {
            id: 1,
            titulo: "A influência da literatura fantástica no cinema contemporâneo",
            autor: "Maria Silva",
            autorImagem: "/assets/images/usuarios/maria-silva.jpg",
            comunidade: "Literatura e Cinema",
            dataPublicacao: "2023-04-02T10:30:00",
            comentarios: 42,
            curtidas: 87
        },
        {
            id: 2,
            titulo: "Análise da obra 'Grande Sertão: Veredas' de Guimarães Rosa",
            autor: "João Santos",
            autorImagem: "/assets/images/usuarios/joao-santos.jpg",
            comunidade: "Literatura Brasileira",
            dataPublicacao: "2023-04-01T15:45:00",
            comentarios: 38,
            curtidas: 65
        },
        {
            id: 3,
            titulo: "O uso de metáforas na literatura pós-moderna",
            autor: "Ana Costa",
            autorImagem: "/assets/images/usuarios/ana-costa.jpg",
            comunidade: "Estudos Literários",
            dataPublicacao: "2023-03-30T09:15:00",
            comentarios: 27,
            curtidas: 51
        },
        {
            id: 4,
            titulo: "Debate: Os clássicos ainda são relevantes?",
            autor: "Carlos Mendes",
            autorImagem: "/assets/images/usuarios/carlos-mendes.jpg",
            comunidade: "Debate Literário",
            dataPublicacao: "2023-03-29T14:20:00",
            comentarios: 76,
            curtidas: 103
        }
    ];
    
    const container = document.getElementById('discussoes-grid');
    if (!container) return;
    
    container.innerHTML = '';
    
    discussoes.forEach(discussao => {
        const card = criarCardDiscussao(discussao);
        container.appendChild(card);
    });
    
    // Event listener para o botão "Carregar mais"
    const carregarMaisBtn = document.getElementById('carregar-mais-discussoes');
    if (carregarMaisBtn) {
        carregarMaisBtn.addEventListener('click', () => {
            // Aqui você pode carregar mais discussões ou mostrar um conjunto adicional
            // Por enquanto, apenas vamos adicionar as mesmas discussões novamente para demonstração
            discussoes.forEach(discussao => {
                const card = criarCardDiscussao({ ...discussao, id: discussao.id + 100 }); // Modificando ID para evitar duplicidade
                container.appendChild(card);
            });
        });
    }
}

function criarCardDiscussao(discussao) {
    const card = document.createElement('div');
    card.className = 'discussao-card';
    card.setAttribute('data-id', discussao.id);
    
    // Formatar a data de publicação
    const dataPublicacao = new Date(discussao.dataPublicacao);
    const dataFormatada = formatarData(dataPublicacao);
    
    card.innerHTML = `
        <div class="discussao-header">
            <div class="autor-info">
                <img src="${discussao.autorImagem}" alt="${discussao.autor}" class="autor-imagem">
                <div class="autor-detalhes">
                    <span class="autor-nome">${discussao.autor}</span>
                    <span class="comunidade-nome">em ${discussao.comunidade}</span>
                </div>
            </div>
            <div class="discussao-data">
                ${dataFormatada}
            </div>
        </div>
        <h3 class="discussao-titulo">
            <a href="/discussao/${discussao.id}">${discussao.titulo}</a>
        </h3>
        <div class="discussao-footer">
            <div class="discussao-stats">
                <span class="stat-item"><i class="icon-comment"></i> ${discussao.comentarios} comentários</span>
                <span class="stat-item"><i class="icon-heart"></i> ${discussao.curtidas} curtidas</span>
            </div>
            <a href="/discussao/${discussao.id}" class="ver-discussao-btn">Participar</a>
        </div>
    `;
    
    return card;
}

function inicializarFiltrosDiscussoes() {
    const filtroSelect = document.getElementById('filtro-discussoes');
    if (!filtroSelect) return;
    
    filtroSelect.addEventListener('change', function() {
        const filtro = this.value;
        // Aqui você implementaria a lógica para recarregar as discussões
        // baseado no filtro selecionado (recentes/populares/comentadas)
        console.log(`Filtro de discussões alterado para: ${filtro}`);
        
        // Recarregar discussões (simulação)
        carregarDiscussoes();
    });
}

// =================== PRÓXIMOS EVENTOS ===================
function carregarEventos() {
    // Simulação de dados para eventos
    const eventos = [
        {
            id: 1,
            titulo: "Clube do Livro - Discussão mensal",
            data: "2023-04-15T19:00:00",
            local: "Online - Google Meet",
            comunidade: "Clube de Leitura Literária",
            descricao: "Discussão sobre o livro 'O Conto da Aia' de Margaret Atwood.",
            participantes: 32
        },
        {
            id: 2,
            titulo: "Workshop de Escrita Criativa",
            data: "2023-04-20T14:00:00",
            local: "Online - Zoom",
            comunidade: "Escritores Independentes",
            descricao: "Aprenda técnicas para desenvolver personagens complexos e envolventes.",
            participantes: 45
        },
        {
            id: 3,
            titulo: "Bate-papo com Autor",
            data: "2023-04-25T20:00:00",
            local: "Online - YouTube Live",
            comunidade: "Literatura Brasileira",
            descricao: "Conversa com o escritor João Paulo Cuenca sobre seu processo criativo.",
            participantes: 120
        }
    ];
    
    const container = document.getElementById('eventos-timeline');
    if (!container) return;
    
    container.innerHTML = '';
    
    eventos.forEach(evento => {
        const card = criarCardEvento(evento);
        container.appendChild(card);
    });
}

function criarCardEvento(evento) {
    const card = document.createElement('div');
    card.className = 'evento-card';
    card.setAttribute('data-id', evento.id);
    
    // Formatar a data do evento
    const dataEvento = new Date(evento.data);
    const dataFormatada = formatarDataEvento(dataEvento);
    const horaFormatada = formatarHoraEvento(dataEvento);
    
    card.innerHTML = `
        <div class="evento-data">
            <div class="evento-dia">${dataEvento.getDate()}</div>
            <div class="evento-mes">${obterNomeMes(dataEvento).substr(0, 3)}</div>
        </div>
        <div class="evento-detalhes">
            <h3 class="evento-titulo">${evento.titulo}</h3>
            <div class="evento-meta">
                <span class="evento-horario"><i class="icon-clock"></i> ${horaFormatada}</span>
                <span class="evento-local"><i class="icon-location"></i> ${evento.local}</span>
                <span class="evento-comunidade"><i class="icon-group"></i> ${evento.comunidade}</span>
            </div>
            <p class="evento-descricao">${evento.descricao}</p>
            <div class="evento-footer">
                <span class="evento-participantes">${evento.participantes} participantes confirmados</span>
                <a href="/evento/${evento.id}" class="participar-btn">Participar</a>
            </div>
        </div>
    `;
    
    return card;
}

// =================== COMUNIDADE DO MÊS ===================
function carregarComunidadeMes() {
    // Dados simulados para a comunidade do mês
    const comunidadeMes = {
        id: 100,
        nome: "Literatura Latino-Americana",
        descricao: "Uma comunidade dedicada à exploração e discussão das ricas tradições literárias da América Latina, desde o realismo mágico até narrativas contemporâneas urbanas. Compartilhamos análises, recomendações e organizamos eventos com autores regionais.",
        membros: 4827,
        posts: 3452,
        atividade: "Alta",
        imagem: "/assets/images/comunidades/literatura-latino-americana.jpg"
    };
    
    // Atualizar os elementos do DOM
    document.getElementById('comunidade-mes-title').textContent = comunidadeMes.nome;
    document.getElementById('comunidade-mes-desc').textContent = comunidadeMes.descricao;
    document.getElementById('comunidade-membros').textContent = formatarNumero(comunidadeMes.membros);
    document.getElementById('comunidade-posts').textContent = formatarNumero(comunidadeMes.posts);
    document.getElementById('comunidade-atividade').textContent = comunidadeMes.atividade;
    document.getElementById('comunidade-mes-link').href = `/comunidade/${comunidadeMes.id}`;
    
    // Adicionar a imagem
    const imagemContainer = document.getElementById('comunidade-mes-image');
    if (imagemContainer) {
        imagemContainer.style.backgroundImage = `url('${comunidadeMes.imagem}')`;
    }
}

// =================== FUNÇÕES UTILITÁRIAS ===================
function formatarCategoria(categoria) {
    const categorias = {
        'foruns': 'Fórum',
        'clubes-livro': 'Clube do Livro',
        'grupos-estudo': 'Grupo de Estudo',
        'eventos': 'Evento Literário'
    };
    
    return categorias[categoria] || categoria;
}

function formatarNumero(numero) {
    if (numero >= 1000) {
        return (numero / 1000).toFixed(1) + 'k';
    }
    return numero;
}

function formatarData(data) {
    // Verifica se a data é hoje
    const hoje = new Date();
    const ontem = new Date(hoje);
    ontem.setDate(hoje.getDate() - 1);
    
    if (data.toDateString() === hoje.toDateString()) {
        return `Hoje, ${formatarHora(data)}`;
    } else if (data.toDateString() === ontem.toDateString()) {
        return `Ontem, ${formatarHora(data)}`;
    } else {
        // Formatar para DD/MM/AAAA
        return `${String(data.getDate()).padStart(2, '0')}/${String(data.getMonth() + 1).padStart(2, '0')}/${data.getFullYear()}, ${formatarHora(data)}`;
    }
}

function formatarHora(data) {
    return `${String(data.getHours()).padStart(2, '0')}:${String(data.getMinutes()).padStart(2, '0')}`;
}

function formatarDataEvento(data) {
    const dias = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
    const meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    
    return `${dias[data.getDay()]}, ${data.getDate()} ${meses[data.getMonth()]} ${data.getFullYear()}`;
}

function formatarHoraEvento(data) {
    return `${String(data.getHours()).padStart(2, '0')}:${String(data.getMinutes()).padStart(2, '0')}`;
}

function obterNomeMes(data) {
    const meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
    return meses[data.getMonth()];
}