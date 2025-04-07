/**
 * Script para adicionar interatividade à página de Perfil do Usuário
 * Desenvolvido para trabalhar com a estrutura HTML e CSS existentes
 */

// Espera o DOM carregar completamente antes de executar o script
document.addEventListener('DOMContentLoaded', function() {
    // Inicialização de todos os componentes interativos
    initPhotoUpload();
    initActiveNavigation();
    initSectionAnimations();
    initLinkAnimations();
    initHoverEffects();
});

/**
 * Gerencia o upload e alteração da foto de perfil
 */
function initPhotoUpload() {
    const changePhotoBtn = document.querySelector('.foto-perfil button');
    const profileImg = document.querySelector('.foto-perfil img');
    
    if (!changePhotoBtn) return;
    
    // Cria o modal de upload de foto
    const modal = createUploadModal();
    document.body.appendChild(modal);
    
    // Adiciona evento de clique ao botão de alteração de foto
    changePhotoBtn.addEventListener('click', function() {
        // Exibe o modal com animação
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.style.opacity = '1';
            modal.querySelector('.modal-content').style.transform = 'translateY(0)';
        }, 10);
    });
    
    // Implementa a funcionalidade de upload de imagem
    const fileInput = modal.querySelector('#photo-upload');
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && file.type.match('image.*')) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Simula um pequeno atraso para dar impressão de processamento
                showLoader(modal.querySelector('.upload-area'));
                
                setTimeout(() => {
                    // Atualiza a imagem de perfil
                    profileImg.src = e.target.result;
                    hideLoader(modal.querySelector('.upload-area'));
                    
                    // Exibe mensagem de sucesso
                    showNotification('Foto de perfil atualizada com sucesso!');
                    
                    // Fecha o modal após a atualização
                    setTimeout(() => {
                        closeModal(modal);
                    }, 1000);
                }, 1500);
            };
            
            reader.readAsDataURL(file);
        }
    });
    
    // Fecha o modal quando clica no botão de fechar ou fora do conteúdo
    modal.querySelector('.close-modal').addEventListener('click', () => closeModal(modal));
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal(modal);
        }
    });
    
    // Botão de cancelar
    modal.querySelector('.cancel-btn').addEventListener('click', () => closeModal(modal));
}

/**
 * Cria o modal para upload de foto
 * @returns {HTMLElement} O elemento do modal criado
 */
function createUploadModal() {
    const modal = document.createElement('div');
    modal.className = 'photo-modal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    
    modal.innerHTML = `
        <div class="modal-content" style="
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        ">
            <button class="close-modal" style="
                position: absolute;
                top: 10px;
                right: 10px;
                background: none;
                border: none;
                font-size: 20px;
                cursor: pointer;
                color: #666;
            ">&times;</button>
            
            <h3 style="margin-bottom: 20px; color: #333;">Alterar foto de perfil</h3>
            
            <div class="upload-area" style="
                border: 2px dashed #ccc;
                padding: 30px;
                text-align: center;
                margin-bottom: 20px;
                position: relative;
                border-radius: 5px;
            ">
                <p>Arraste uma imagem aqui ou</p>
                <input type="file" id="photo-upload" accept="image/*" style="display: none;">
                <button class="upload-btn" style="
                    background-color: #8075ff;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 20px;
                    cursor: pointer;
                    margin-top: 10px;
                    transition: background-color 0.3s;
                ">Selecionar arquivo</button>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button class="cancel-btn" style="
                    background-color: #f7d6e0;
                    color: #333;
                    border: none;
                    padding: 8px 15px;
                    border-radius: 5px;
                    cursor: pointer;
                ">Cancelar</button>
                
                <button class="save-btn" style="
                    background-color: #8075ff;
                    color: white;
                    border: none;
                    padding: 8px 15px;
                    border-radius: 5px;
                    cursor: pointer;
                    opacity: 0.5;
                    pointer-events: none;
                ">Salvar alterações</button>
            </div>
        </div>
    `;
    
    // Vincula o input de arquivo ao botão de upload
    const uploadBtn = modal.querySelector('.upload-btn');
    const fileInput = modal.querySelector('#photo-upload');
    
    uploadBtn.addEventListener('click', () => {
        fileInput.click();
    });
    
    // Implementa o drag and drop para a área de upload
    const uploadArea = modal.querySelector('.upload-area');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => {
            uploadArea.style.borderColor = '#8075ff';
            uploadArea.style.backgroundColor = 'rgba(128, 117, 255, 0.1)';
        });
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => {
            uploadArea.style.borderColor = '#ccc';
            uploadArea.style.backgroundColor = 'transparent';
        });
    });
    
    uploadArea.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        
        // Dispara o evento change manualmente
        const event = new Event('change');
        fileInput.dispatchEvent(event);
    });
    
    return modal;
}

/**
 * Fecha o modal com animação
 * @param {HTMLElement} modal - O elemento do modal a ser fechado
 */
function closeModal(modal) {
    modal.querySelector('.modal-content').style.transform = 'translateY(-20px)';
    modal.style.opacity = '0';
    setTimeout(() => {
        modal.style.display = 'none';
        // Resetar o input de arquivo
        const fileInput = modal.querySelector('#photo-upload');
        if (fileInput) fileInput.value = '';
    }, 300);
}

/**
 * Exibe um loader na área especificada
 * @param {HTMLElement} element - O elemento onde o loader será exibido
 */
function showLoader(element) {
    // Cria e adiciona o loader
    const loader = document.createElement('div');
    loader.className = 'loader';
    loader.style.cssText = `
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10;
    `;
    
    loader.innerHTML = `
        <div style="
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #8075ff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        "></div>
    `;
    
    // Adiciona o estilo da animação
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
    
    element.style.position = 'relative';
    element.appendChild(loader);
}

/**
 * Remove o loader do elemento
 * @param {HTMLElement} element - O elemento de onde o loader será removido
 */
function hideLoader(element) {
    const loader = element.querySelector('.loader');
    if (loader) {
        loader.remove();
    }
}

/**
 * Exibe uma notificação temporária
 * @param {string} message - A mensagem a ser exibida
 */
function showNotification(message) {
    // Verifica se já existe uma notificação e remove
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Cria o elemento de notificação
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #8075ff;
        color: white;
        padding: 12px 20px;
        border-radius: 5px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        z-index: 1100;
        transform: translateY(100px);
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Anima a entrada da notificação
    setTimeout(() => {
        notification.style.transform = 'translateY(0)';
        notification.style.opacity = '1';
        
        // Remove após alguns segundos
        setTimeout(() => {
            notification.style.transform = 'translateY(100px)';
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }, 10);
}

/**
 * Inicializa a navegação ativa, destacando o item correspondente à página atual
 */
function initActiveNavigation() {
    // Seleciona todos os links de navegação
    const navLinks = document.querySelectorAll('header nav ul li a');
    
    // Define o link "Perfil" como ativo por padrão
    const profileLink = Array.from(navLinks).find(link => link.textContent === 'Perfil');
    
    if (profileLink) {
        // Adiciona a classe ativa ao link de perfil
        addActiveClass(profileLink);
        
        // Adiciona evento de clique a todos os links
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Verifica se é navegação para páginas externas
                if (link.getAttribute('href') !== 'perfil.html') {
                    // Simula um carregamento de página para outros links
                    e.preventDefault();
                    
                    // Remove a classe ativa de todos os links
                    navLinks.forEach(l => removeActiveClass(l));
                    
                    // Adiciona a classe ativa ao link clicado
                    addActiveClass(link);
                    
                    // Simula um carregamento de página
                    simulatePageTransition(link.getAttribute('href'));
                }
            });
        });
    }
}

/**
 * Adiciona classe ativa a um elemento de navegação
 * @param {HTMLElement} element - O elemento de navegação
 */
function addActiveClass(element) {
    element.classList.add('active');
    element.style.backgroundColor = 'var(--cor-destaque)';
    element.style.color = 'white';
    element.style.borderLeft = '4px solid white';
    element.style.fontWeight = '600';
}

/**
 * Remove classe ativa de um elemento de navegação
 * @param {HTMLElement} element - O elemento de navegação
 */
function removeActiveClass(element) {
    element.classList.remove('active');
    element.style.backgroundColor = '';
    element.style.color = '';
    element.style.borderLeft = '4px solid transparent';
    element.style.fontWeight = '500';
}

/**
 * Simula uma transição de página
 * @param {string} url - A URL para a qual navegar
 */
function simulatePageTransition(url) {
    // Cria um overlay de carregamento
    const overlay = document.createElement('div');
    overlay.className = 'page-transition-overlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        z-index: 2000;
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;
    
    overlay.innerHTML = `
        <div style="text-align: center;">
            <div style="
                width: 50px;
                height: 50px;
                border: 4px solid #f3f3f3;
                border-top: 4px solid #8075ff;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 15px auto;
            "></div>
            <p style="font-size: 16px; color: #333;">Carregando ${url.replace('.html', '')}...</p>
        </div>
    `;
    
    document.body.appendChild(overlay);
    
    // Anima a entrada do overlay
    setTimeout(() => {
        overlay.style.opacity = '1';
        
        // Simula um atraso na navegação
        setTimeout(() => {
            overlay.style.opacity = '0';
            setTimeout(() => {
                overlay.remove();
                showNotification(`Navegação para "${url.replace('.html', '')}" simulada. Esta é apenas uma demonstração.`);
                // Na implementação real, aqui seria feita a navegação efetiva
            }, 300);
        }, 1500);
    }, 10);
}

/**
 * Inicializa as animações para as seções da página
 */
function initSectionAnimations() {
    // Seleciona todas as seções
    const sections = document.querySelectorAll('main section');
    
    // Configura o Intersection Observer para animar as seções conforme o scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            // Adiciona ou remove classes baseado na visibilidade da seção
            if (entry.isIntersecting) {
                entry.target.classList.add('section-visible');
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            } else {
                // Opcional: remover a animação quando a seção não está visível
                // entry.target.classList.remove('section-visible');
                // entry.target.style.opacity = '0.5';
                // entry.target.style.transform = 'translateY(20px)';
            }
        });
    }, {
        threshold: 0.1 // Percentual de visibilidade necessário para disparar o callback
    });
    
    // Configura cada seção com o estilo inicial e observa
    sections.forEach(section => {
        // Define o estilo inicial (invisível)
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        // Começa a observar a seção
        observer.observe(section);
    });
}

/**
 * Inicializa as animações para links específicos
 */
function initLinkAnimations() {
    // Seleciona links específicos para animação de ação
    const actionLinks = document.querySelectorAll('a[href="todas-atividades.html"], a[href="todos-livros.html"], a[href="compromissos.html"]');
    
    actionLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Adiciona um efeito de clique
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
            
            // Simula uma ação com uma notificação
            setTimeout(() => {
                showNotification(`Ação "${this.textContent}" registrada!`);
            }, 200);
        });
    });
}

/**
 * Inicializa efeitos de hover em elementos interativos
 */
function initHoverEffects() {
    // Adiciona efeitos de hover aos cards
    const interactiveElements = document.querySelectorAll('.card, .livro, .compromisso, .dados-pessoais div');
    
    interactiveElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 15px rgba(0, 0, 0, 0.1)';
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.05)';
        });
    });
    
    // Adiciona efeitos de hover aos botões
    const buttons = document.querySelectorAll('button');
    
    buttons.forEach(button => {
        if (!button.classList.contains('close-modal')) {
            button.addEventListener('mouseenter', function() {
                this.style.transition = 'transform 0.2s ease, background-color 0.2s ease';
                this.style.transform = 'translateY(-2px)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
            
            button.addEventListener('mousedown', function() {
                this.style.transform = 'translateY(1px)';
            });
            
            button.addEventListener('mouseup', function() {
                this.style.transform = 'translateY(-2px)';
            });
        }
    });
}

// Adiciona a função de inicialização ao window para garantir acesso global
window.initProfilePage = function() {
    initPhotoUpload();
    initActiveNavigation();
    initSectionAnimations();
    initLinkAnimations();
    initHoverEffects();
};