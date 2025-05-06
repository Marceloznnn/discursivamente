/**
 * FAQ JavaScript
 * Gerencia o funcionamento do acordeão de perguntas frequentes,
 * filtragem por categoria e busca de perguntas
 */
document.addEventListener('DOMContentLoaded', function() {
    // Elementos
    const faqSection = document.querySelector('.faq-section');
    if (!faqSection) return;
    
    const faqItems = document.querySelectorAll('.faq-item');
    const categoryButtons = document.querySelectorAll('.category-btn');
    const searchInput = document.getElementById('faq-search-input');
    
    // Acordeão de perguntas
    function initAccordion() {
        faqItems.forEach((item, index) => {
            // Elementos do item
            const question = item.querySelector('.faq-question');
            const answer = item.querySelector('.faq-answer');
            
            // Configuração ARIA
            const itemId = `faq-answer-${index + 1}`;
            answer.id = itemId;
            question.setAttribute('aria-controls', itemId);
            question.setAttribute('aria-expanded', 'false');
            answer.setAttribute('aria-hidden', 'true');
            
            // Adiciona evento de clique
            question.addEventListener('click', () => {
                const isExpanded = question.getAttribute('aria-expanded') === 'true';
                
                // Fecha todas as outras perguntas
                if (!isExpanded) {
                    closeAllItems();
                }
                
                // Alterna o estado atual
                toggleItem(question, answer, !isExpanded);
            });
        });
        
        // Abre o primeiro item por padrão
        if (faqItems.length > 0) {
            const firstQuestion = faqItems[0].querySelector('.faq-question');
            const firstAnswer = faqItems[0].querySelector('.faq-answer');
            toggleItem(firstQuestion, firstAnswer, true);
        }
    }
    
    // Alternar visibilidade do item
    function toggleItem(question, answer, isOpen) {
        question.setAttribute('aria-expanded', isOpen);
        answer.setAttribute('aria-hidden', !isOpen);
    }
    
    // Fecha todos os itens
    function closeAllItems() {
        faqItems.forEach(item => {
            const q = item.querySelector('.faq-question');
            const a = item.querySelector('.faq-answer');
            toggleItem(q, a, false);
        });
    }
    
    // Filtro por categoria
    function initCategoryFilter() {
        if (!categoryButtons.length) return;
        
        categoryButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove classe ativa de todos os botões
                categoryButtons.forEach(btn => {
                    btn.classList.remove('active');
                    btn.setAttribute('aria-selected', 'false');
                });
                
                // Adiciona classe ativa ao botão clicado
                button.classList.add('active');
                button.setAttribute('aria-selected', 'true');
                
                const category = button.dataset.category;
                
                // Filtra os itens
                filterItems(category);
            });
        });
    }
    
    // Filtra os itens por categoria ou busca
    function filterItems(category = 'all', searchQuery = '') {
        faqItems.forEach(item => {
            const itemCategory = item.dataset.category;
            const questionText = item.querySelector('.faq-question h3').textContent.toLowerCase();
            const answerText = item.querySelector('.faq-answer').textContent.toLowerCase();
            
            const matchesCategory = category === 'all' || itemCategory === category;
            const matchesSearch = searchQuery === '' || 
                                  questionText.includes(searchQuery) || 
                                  answerText.includes(searchQuery);
            
            // Mostra ou esconde o item
            if (matchesCategory && matchesSearch) {
                item.style.display = '';
                item.classList.add('fade-in');
                setTimeout(() => {
                    item.classList.remove('fade-in');
                }, 500);
            } else {
                item.style.display = 'none';
            }
        });
        
        // Mostra mensagem quando não há resultados
        checkNoResults();
    }
    
    // Verifica se há resultados
    function checkNoResults() {
        // Remove mensagem existente
        const existingMessage = faqSection.querySelector('.no-results-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Verifica se todos os itens estão escondidos
        const visibleItems = Array.from(faqItems).filter(item => item.style.display !== 'none');
        
        if (visibleItems.length === 0) {
            const noResultsMessage = document.createElement('div');
            noResultsMessage.className = 'no-results-message';
            noResultsMessage.innerHTML = `
                <p>Nenhuma pergunta encontrada. Tente outro termo ou categoria.</p>
                <button class="btn btn-text reset-search">Limpar busca</button>
            `;
            
            // Adiciona mensagem após o acordeão
            const faqAccordion = faqSection.querySelector('.faq-accordion');
            faqAccordion.appendChild(noResultsMessage);
            
            // Adiciona evento para limpar busca
            const resetButton = noResultsMessage.querySelector('.reset-search');
            resetButton.addEventListener('click', () => {
                searchInput.value = '';
                
                // Resetar para "Todas" as categorias
                categoryButtons.forEach(btn => {
                    if (btn.dataset.category === 'all') {
                        btn.click();
                    }
                });
            });
        }
    }
    
    // Busca
    function initSearch() {
        if (!searchInput) return;
        
        let debounceTimeout;
        
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimeout);
            
            // Debounce para não processar a cada tecla
            debounceTimeout = setTimeout(() => {
                const searchQuery = searchInput.value.trim().toLowerCase();
                
                // Obtém a categoria atual selecionada
                const activeCategory = document.querySelector('.category-btn.active').dataset.category;
                
                // Filtra com a categoria atual e o termo de busca
                filterItems(activeCategory, searchQuery);
            }, 300);
        });
        
        // Limpa a busca quando pressionar ESC
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                searchInput.value = '';
                
                // Aciona o evento input manualmente
                const inputEvent = new Event('input');
                searchInput.dispatchEvent(inputEvent);
            }
        });
    }
    
    // Tracking de eventos (para analytics)
    function trackFaqInteractions() {
        // Rastreia abertura de perguntas
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question h3').textContent;
            const questionBtn = item.querySelector('.faq-question');
            
            questionBtn.addEventListener('click', () => {
                if (questionBtn.getAttribute('aria-expanded') === 'false') {
                    console.log(`FAQ aberta: ${question}`);
                    
                    // Integração com Google Analytics (se disponível)
                    if (typeof gtag === 'function') {
                        gtag('event', 'faq_open', {
                            'event_category': 'engagement',
                            'event_label': question
                        });
                    }
                }
            });
        });
        
        // Rastreia mudanças de categoria
        categoryButtons.forEach(button => {
            button.addEventListener('click', () => {
                const category = button.dataset.category;
                console.log(`Categoria FAQ selecionada: ${category}`);
                
                // Integração com Google Analytics (se disponível)
                if (typeof gtag === 'function') {
                    gtag('event', 'faq_category', {
                        'event_category': 'content_filter',
                        'event_label': category
                    });
                }
            });
        });
        
        // Rastreia buscas (quando o usuário para de digitar)
        if (searchInput) {
            let searchTimeout;
            
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                
                searchTimeout = setTimeout(() => {
                    const searchTerm = searchInput.value.trim();
                    if (searchTerm.length > 2) {
                        console.log(`Busca FAQ: ${searchTerm}`);
                        
                        // Integração com Google Analytics (se disponível)
                        if (typeof gtag === 'function') {
                            gtag('event', 'faq_search', {
                                'event_category': 'content_search',
                                'event_label': searchTerm
                            });
                        }
                    }
                }, 1000);
            });
        }
    }
    
    // Inicializa todas as funcionalidades
    initAccordion();
    initCategoryFilter();
    initSearch();
    trackFaqInteractions();
});