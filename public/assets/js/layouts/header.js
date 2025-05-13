// Melhorias na responsividade do header

document.addEventListener('DOMContentLoaded', function() {
    const header = document.getElementById('site-header');
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuClose = document.getElementById('mobile-menu-close');
    const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
    const mobileSectionToggles = document.querySelectorAll('.mobile-menu-section-toggle');
    
    // Função para manipular scroll da página
    function handleScroll() {
        if (window.scrollY > 10) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }
    
    // Scroll inicial
    handleScroll();
    
    // Listener de scroll
    window.addEventListener('scroll', handleScroll);
    
    // Função para abrir menu mobile
    function openMobileMenu() {
        mobileMenu.classList.add('open');
        mobileMenuOverlay.classList.add('open');
        document.body.style.overflow = 'hidden'; // Impede scroll na página
        mobileMenuToggle.setAttribute('aria-expanded', 'true');
    }
    
    // Função para fechar menu mobile
    function closeMobileMenu() {
        mobileMenu.classList.remove('open');
        mobileMenuOverlay.classList.remove('open');
        document.body.style.overflow = ''; // Restaura scroll
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
        
        // Reset do menu - fecha todos os submenus quando o menu principal é fechado
        document.querySelectorAll('.mobile-menu-subsection').forEach(subsection => {
            subsection.style.maxHeight = null;
            const toggle = subsection.previousElementSibling;
            if (toggle && toggle.classList.contains('mobile-menu-section-toggle')) {
                toggle.setAttribute('aria-expanded', 'false');
            }
        });
    }
    
    // Adiciona listeners para menu mobile
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', openMobileMenu);
    }
    
    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', closeMobileMenu);
    }
    
    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', closeMobileMenu);
    }
    
    // Manipulação dos dropdowns do menu mobile
    mobileSectionToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const expanded = this.getAttribute('aria-expanded') === 'true';
            const targetId = this.getAttribute('aria-controls');
            const target = document.getElementById(targetId);
            
            if (!expanded) {
                // Abrindo este submenu
                this.setAttribute('aria-expanded', 'true');
                if (target) {
                    target.style.maxHeight = target.scrollHeight + 'px';
                }
            } else {
                // Fechando este submenu
                this.setAttribute('aria-expanded', 'false');
                if (target) {
                    target.style.maxHeight = null;
                }
            }
        });
    });
    
    // Melhoria para navegação por teclado
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const expanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !expanded);
            }
        });
    });
    
    // Fecha menu mobile em redimensionamento para desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992 && mobileMenu.classList.contains('open')) {
            closeMobileMenu();
        }
    });
    
    // Lidar com navegação nos submenus por teclado
    document.querySelectorAll('.dropdown-menu a').forEach(item => {
        item.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const dropdown = this.closest('.nav-dropdown');
                if (dropdown) {
                    const toggle = dropdown.querySelector('.dropdown-toggle');
                    toggle.setAttribute('aria-expanded', 'false');
                    toggle.focus();
                }
            }
        });
    });
});