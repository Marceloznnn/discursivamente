document.addEventListener('DOMContentLoaded', function () {
    // --- Toggle Menu Mobile ---
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuClose = document.getElementById('mobile-menu-close');
    const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
    const body = document.body;

    function openMobileMenu() {
        mobileMenu.classList.add('open');
        mobileMenu.setAttribute('aria-hidden', 'false');
        mobileMenuToggle.setAttribute('aria-expanded', 'true');
        mobileMenuOverlay.classList.add('open');
        body.style.overflow = 'hidden'; // Impede scroll do body
    }

    function closeMobileMenu() {
        mobileMenu.classList.remove('open');
        mobileMenu.setAttribute('aria-hidden', 'true');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
        mobileMenuOverlay.classList.remove('open');
        body.style.overflow = ''; // Restaura scroll do body
    }

    if (mobileMenuToggle && mobileMenu && mobileMenuClose && mobileMenuOverlay) {
        mobileMenuToggle.addEventListener('click', () => {
            if (mobileMenu.classList.contains('open')) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        });
        mobileMenuClose.addEventListener('click', closeMobileMenu);
        mobileMenuOverlay.addEventListener('click', closeMobileMenu);
    }

    // --- Toggle Dropdowns Desktop ---
    const dropdownToggles = document.querySelectorAll('.nav-dropdown .dropdown-toggle');

    dropdownToggles.forEach(toggle => {
        const menu = toggle.nextElementSibling; // O .dropdown-menu
        if (!menu) return;

        toggle.addEventListener('click', (event) => {
            event.stopPropagation(); // Impede que o clique feche o menu imediatamente
            const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
            
            // Fecha todos os outros dropdowns abertos
            document.querySelectorAll('.nav-dropdown .dropdown-toggle[aria-expanded="true"]').forEach(otherToggle => {
                if (otherToggle !== toggle) {
                    otherToggle.setAttribute('aria-expanded', 'false');
                    otherToggle.nextElementSibling.classList.remove('open');
                }
            });

            toggle.setAttribute('aria-expanded', !isExpanded);
            menu.classList.toggle('open');
        });

        // Adicionar funcionalidade de hover para abrir no desktop, opcional
        const parentDropdown = toggle.closest('.nav-dropdown');
        if (parentDropdown) {
            parentDropdown.addEventListener('mouseenter', () => {
                if (window.innerWidth > 992) { // Apenas em desktop
                     // Fecha outros dropdowns abertos por hover/click
                    document.querySelectorAll('.nav-dropdown .dropdown-menu.open').forEach(openMenu => {
                        if (openMenu !== menu) {
                            openMenu.classList.remove('open');
                            const correspondingToggle = openMenu.previousElementSibling;
                            if(correspondingToggle) correspondingToggle.setAttribute('aria-expanded', 'false');
                        }
                    });
                    toggle.setAttribute('aria-expanded', 'true');
                    menu.classList.add('open');
                }
            });
            parentDropdown.addEventListener('mouseleave', () => {
                 if (window.innerWidth > 992) { // Apenas em desktop
                    // Apenas fecha se não foi explicitamente clicado para abrir (mais complexo, por agora fecha)
                    // Para manter aberto após clique mesmo com mouseleave, precisaria de lógica adicional.
                    // Por simplicidade, hover abre, mouseleave fecha. Clique ainda funciona.
                    // Se um dropdown foi aberto por clique, o mouseleave não o fechará
                    // a menos que o toggle não tenha o foco.
                    // Para uma UX mais robusta que mistura hover e clique, a lógica pode ficar complexa.
                    // Este modelo prioriza o clique para manter aberto.
                    // O timeout ajuda a não fechar imediatamente se o mouse sair brevemente
                    setTimeout(() => {
                        if (!parentDropdown.matches(':hover') && !menu.matches(':hover')) {
                           if (toggle.getAttribute('aria-expanded') === 'true' && !menu.classList.contains('clicked-open')) { // 'clicked-open' seria uma classe para indicar abertura por clique
                                // toggle.setAttribute('aria-expanded', 'false');
                                // menu.classList.remove('open');
                           }
                        }
                    }, 200);
                 }
            });
        }
    });

    // Fechar dropdowns se clicar fora deles
    document.addEventListener('click', (event) => {
        dropdownToggles.forEach(toggle => {
            const menu = toggle.nextElementSibling;
            if (menu && menu.classList.contains('open')) {
                if (!toggle.contains(event.target) && !menu.contains(event.target)) {
                    toggle.setAttribute('aria-expanded', 'false');
                    menu.classList.remove('open');
                }
            }
        });
    });


    // --- Toggle Seções do Menu Mobile (Acordeão) ---
    const mobileSectionToggles = document.querySelectorAll('.mobile-menu-section-toggle');
    mobileSectionToggles.forEach(toggle => {
        const subsection = document.getElementById(toggle.getAttribute('aria-controls'));
        if (subsection) {
            toggle.addEventListener('click', () => {
                const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', !isExpanded);
                if (!isExpanded) {
                    subsection.classList.add('open');
                    subsection.style.maxHeight = subsection.scrollHeight + "px";
                } else {
                    subsection.classList.remove('open');
                    subsection.style.maxHeight = null;
                }
            });
        }
    });

    // Fechar menu mobile se um link interno for clicado (SPA behavior)
    const mobileLinks = document.querySelectorAll('.mobile-menu .mobile-link, .mobile-menu .mobile-menu-subsection a');
    mobileLinks.forEach(link => {
        link.addEventListener('click', () => {
            // Adicionar verificação se o link é para a mesma página ou âncora
            // Se for um link externo ou para outra página, pode não querer fechar.
            // Para este exemplo, sempre fecha.
            if (mobileMenu.classList.contains('open')) {
                 // Atraso para permitir a navegação antes de fechar, se necessário
                // setTimeout(closeMobileMenu, 150);
                closeMobileMenu(); 
            }
        });
    });

});

document.addEventListener('DOMContentLoaded', function () {
  // ——— existing code para toggle mobile e dropdowns ———
  // …  

  // ——— Gerenciar classe .active nos links Desktop ———
  const desktopLinks = document.querySelectorAll('.nav-links .nav-link');
  desktopLinks.forEach(link => {
    link.addEventListener('click', function () {
      // remove .active de todos
      desktopLinks.forEach(l => l.classList.remove('active'));
      // adiciona .active ao que foi clicado
      this.classList.add('active');
    });
  });

  // ——— Gerenciar classe .active nos links Mobile ———
  const mobileLinks = document.querySelectorAll('.mobile-nav-links .mobile-link');
  mobileLinks.forEach(link => {
    link.addEventListener('click', function () {
      // remove .active de todos
      mobileLinks.forEach(l => l.classList.remove('active'));
      // adiciona .active ao que foi clicado
      this.classList.add('active');
    });
  });
});
const ddToggles = document.querySelectorAll('.dropdown-toggle');
ddToggles.forEach(btn => {
  btn.addEventListener('click', function () {
    // fecha todos
    ddToggles.forEach(b => b.classList.remove('active'));
    // ativa só este
    this.classList.add('active');
  });
});
document.addEventListener('DOMContentLoaded', function () {
  // … seu código existente …

  // ===== Ativar/desativar .active só no menu mobile =====
  const mobileLinks = document.querySelectorAll('.mobile-menu .mobile-link');

  mobileLinks.forEach(link => {
    link.addEventListener('click', function () {
      // só em SMALL SCREENS: opcional, checar largura
      if (window.innerWidth <= 768) {
        // remove .active de todos
        mobileLinks.forEach(l => l.classList.remove('active'));
        // adiciona .active ao que foi clicado
        this.classList.add('active');
      }
    });
  });

});
