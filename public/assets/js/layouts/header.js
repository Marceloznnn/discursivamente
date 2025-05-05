/**
 * Discursivamente - Script de navegação para o header
 * Gerencia os dropdowns do menu de navegação e o menu mobile
 */

document.addEventListener('DOMContentLoaded', function() {
    // ======= MENU DESKTOP - DROPDOWNS =======
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    // Função para fechar todos os dropdowns
    function closeAllDropdowns() {
        document.querySelectorAll('.dropdown-menu').forEach(dropdown => {
            dropdown.classList.remove('active');
        });
        
        // Resetar estado dos toggles
        dropdownToggles.forEach(toggle => {
            toggle.classList.remove('active');
        });
    }
    
    // Adicionar evento de clique para cada botão dropdown
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation(); // Evitar que o clique propague para o documento
            
            const dropdownMenu = this.nextElementSibling;
            const isActive = dropdownMenu.classList.contains('active');
            
            // Fechar todos os outros dropdowns primeiro
            closeAllDropdowns();
            
            // Se não estava ativo, ative-o (toggle)
            if (!isActive) {
                dropdownMenu.classList.add('active');
                this.classList.add('active');
            }
        });
    });
    
    // Fechar dropdowns quando clicar fora deles
    document.addEventListener('click', function(e) {
        // Verifica se o clique foi fora de qualquer dropdown ou toggle
        if (!e.target.closest('.dropdown-menu') && !e.target.closest('.dropdown-toggle')) {
            closeAllDropdowns();
        }
    });
    
    // ======= MENU MOBILE =======
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    // Toggle do menu mobile
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenuButton.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });
    }
    
    // Gerenciar submenus mobile
    const mobileSubmenuHeaders = document.querySelectorAll('.mobile-dropdown-header');
    
    mobileSubmenuHeaders.forEach(header => {
        header.addEventListener('click', function() {
            // Toggle do conteúdo do submenu
            const content = this.nextElementSibling;
            content.classList.toggle('active');
            this.classList.toggle('active');
        });
    });
    
    // Fechar menu mobile ao clicar em links de navegação
    const mobileLinks = document.querySelectorAll('#mobile-menu a:not(.mobile-dropdown-header)');
    
    mobileLinks.forEach(link => {
        link.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            mobileMenuButton.classList.remove('active');
        });
    });
    
    // Fechar menu mobile quando clicar fora dele
    document.addEventListener('click', function(e) {
        if (mobileMenu && mobileMenu.classList.contains('active')) {
            // Se o menu mobile está aberto e o clique não foi no menu ou no botão
            if (!e.target.closest('#mobile-menu') && !e.target.closest('#mobile-menu-button')) {
                mobileMenu.classList.remove('active');
                mobileMenuButton.classList.remove('active');
            }
        }
    });
    
    // ======= RESPONSIVIDADE =======
    // Função para verificar o tamanho da tela e ajustar comportamentos
    function checkScreenSize() {
        if (window.innerWidth > 768) {
            // Se o dispositivo voltar para desktop, garantir que o menu mobile esteja fechado
            if (mobileMenu && mobileMenu.classList.contains('active')) {
                mobileMenu.classList.remove('active');
                mobileMenuButton.classList.remove('active');
            }
        }
    }
    
    // Verificar quando a janela for redimensionada
    window.addEventListener('resize', checkScreenSize);
    
    // Verificar no carregamento inicial
    checkScreenSize();
});