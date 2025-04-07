// header.js

document.addEventListener('DOMContentLoaded', function() {
    // Elementos
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenuIcon = document.getElementById('mobileMenuIcon');
    const mainNavigation = document.getElementById('mainNavigation');
    const dropdownToggle = document.getElementById('dropdownToggle');
    const dropdownIcon = document.getElementById('dropdownIcon');
    const dropdownMenu = document.getElementById('dropdownMenu');
    
    // Função para alternar o menu mobile
    function toggleMobileMenu() {
        const isExpanded = mobileMenuBtn.getAttribute('aria-expanded') === 'true';
        
        mainNavigation.classList.toggle('active');
        mobileMenuBtn.setAttribute('aria-expanded', !isExpanded);
        
        // Alterna o ícone do menu
        if (mainNavigation.classList.contains('active')) {
            mobileMenuIcon.classList.remove('fa-bars');
            mobileMenuIcon.classList.add('fa-times');
        } else {
            mobileMenuIcon.classList.remove('fa-times');
            mobileMenuIcon.classList.add('fa-bars');
        }
    }
    
    // Função para alternar o dropdown do perfil
    function toggleDropdown(event) {
        event.preventDefault();
        
        const isExpanded = dropdownToggle.getAttribute('aria-expanded') === 'true';
        
        dropdownMenu.classList.toggle('active');
        dropdownIcon.classList.toggle('active');
        dropdownToggle.setAttribute('aria-expanded', !isExpanded);
    }
    
    // Fechar dropdown quando clicar fora dele
    function closeDropdownOnClickOutside(event) {
        if (dropdownToggle && dropdownMenu && dropdownMenu.classList.contains('active')) {
            const isClickInside = dropdownToggle.contains(event.target) || dropdownMenu.contains(event.target);
            
            if (!isClickInside) {
                dropdownMenu.classList.remove('active');
                dropdownIcon.classList.remove('active');
                dropdownToggle.setAttribute('aria-expanded', 'false');
            }
        }
    }
    
    // Fechar menu mobile quando clicar em um link
    function closeMenuOnLinkClick(event) {
        const windowWidth = window.innerWidth;
        
        // Somente em telas mobile
        if (windowWidth <= 992) {
            // Verifica se o clique foi em um link de navegação
            const isNavLink = event.target.closest('#navList a:not(#dropdownToggle)');
            
            if (isNavLink) {
                mainNavigation.classList.remove('active');
                mobileMenuBtn.setAttribute('aria-expanded', 'false');
                mobileMenuIcon.classList.remove('fa-times');
                mobileMenuIcon.classList.add('fa-bars');
            }
        }
    }
    
    // Adicionar event listeners
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
    }
    
    if (dropdownToggle) {
        dropdownToggle.addEventListener('click', toggleDropdown);
    }
    
    // Event listeners globais
    document.addEventListener('click', closeDropdownOnClickOutside);
    document.addEventListener('click', closeMenuOnLinkClick);
    
    // Ajustar o menu quando a janela for redimensionada
    window.addEventListener('resize', function() {
        const windowWidth = window.innerWidth;
        
        if (windowWidth > 992 && mainNavigation.classList.contains('active')) {
            mainNavigation.classList.remove('active');
            mobileMenuBtn.setAttribute('aria-expanded', 'false');
            mobileMenuIcon.classList.remove('fa-times');
            mobileMenuIcon.classList.add('fa-bars');
        }
    });
});