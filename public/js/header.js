// Script para gerenciar o menu mobile e dropdown
document.addEventListener('DOMContentLoaded', function() {
    // Elementos do DOM
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mainNav = document.getElementById('mainNav');
    const dropdowns = document.querySelectorAll('.dropdown');
    
    // Toggle do menu mobile
    mobileMenuBtn.addEventListener('click', function() {
        mainNav.classList.toggle('active');
        
        // Alterna o ícone do botão entre barras e X
        const icon = this.querySelector('i');
        if (icon.classList.contains('fa-bars')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
    
    // Gestão dos dropdowns em dispositivos móveis
    if (window.innerWidth <= 768) {
        dropdowns.forEach(dropdown => {
            const dropdownToggle = dropdown.querySelector('.dropdown-toggle');
            
            dropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                dropdown.classList.toggle('open');
                
                // Alterna o ícone de seta
                const icon = this.querySelector('.fa-chevron-down');
                if (icon) {
                    icon.style.transform = dropdown.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0)';
                }
            });
        });
    }
    
    // Fecha o menu ao clicar fora
    document.addEventListener('click', function(event) {
        const isClickInsideNav = mainNav.contains(event.target);
        const isClickOnMenuBtn = mobileMenuBtn.contains(event.target);
        
        if (!isClickInsideNav && !isClickOnMenuBtn && mainNav.classList.contains('active')) {
            mainNav.classList.remove('active');
            mobileMenuBtn.querySelector('i').classList.remove('fa-times');
            mobileMenuBtn.querySelector('i').classList.add('fa-bars');
        }
    });
    
    // Ajusta o menu ao redimensionar a tela
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && mainNav.classList.contains('active')) {
            mainNav.classList.remove('active');
            mobileMenuBtn.querySelector('i').classList.remove('fa-times');
            mobileMenuBtn.querySelector('i').classList.add('fa-bars');
        }
    });
});