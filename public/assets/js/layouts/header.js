// Script específico para o Header

document.addEventListener('DOMContentLoaded', function() {
    // Toggle do menu mobile
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNavigation = document.querySelector('.main-navigation');
    
    if (mobileMenuToggle && mainNavigation) {
        mobileMenuToggle.addEventListener('click', function() {
            mainNavigation.classList.toggle('active');
            
            // Anima os spans do ícone de hambúrguer
            const spans = this.querySelectorAll('span');
            if (mainNavigation.classList.contains('active')) {
                spans[0].style.transform = 'rotate(45deg) translate(5px, 6px)';
                spans[1].style.opacity = '0';
                spans[2].style.transform = 'rotate(-45deg) translate(5px, -6px)';
            } else {
                spans[0].style.transform = 'none';
                spans[1].style.opacity = '1';
                spans[2].style.transform = 'none';
            }
        });
    }
    
    // Destaca o item de menu ativo com base na URL atual
    const currentLocation = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        const linkPath = link.getAttribute('href');
        
        // Verifica se o caminho do link corresponde à URL atual
        if (currentLocation === linkPath || 
            (linkPath !== '/' && currentLocation.startsWith(linkPath))) {
            link.classList.add('active');
            link.style.color = '#007bff';
            link.style.fontWeight = '700';
        }
    });
    
    // Adiciona efeito de rolagem suave para os links de navegação
    document.querySelectorAll('a[href^="/"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            // Não previne o comportamento padrão para links de navegação entre páginas
            // Este código é apenas para preparar possíveis efeitos de transição no futuro
            
            // Fecha o menu mobile se estiver aberto
            if (mainNavigation && mainNavigation.classList.contains('active')) {
                mainNavigation.classList.remove('active');
                
                // Restaura o ícone de hambúrguer
                if (mobileMenuToggle) {
                    const spans = mobileMenuToggle.querySelectorAll('span');
                    spans[0].style.transform = 'none';
                    spans[1].style.opacity = '1';
                    spans[2].style.transform = 'none';
                }
            }
        });
    });
});