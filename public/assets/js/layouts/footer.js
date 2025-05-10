// Atualizar o ano do copyright automaticamente
document.getElementById('current-year').textContent = new Date().getFullYear();

// Funcionalidade para mostrar/ocultar a seção de parceiros
const partnersToggle = document.getElementById('partners-toggle');
const partnersSection = document.getElementById('partners-section');

partnersToggle.addEventListener('click', function() {
    partnersSection.classList.toggle('active');
    
    if (partnersSection.classList.contains('active')) {
        partnersToggle.textContent = 'Ocultar parceiros';
        // Rolagem suave até a seção de parceiros
        partnersSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
        partnersToggle.textContent = 'Ver parceiros';
    }
});

// Efeito de deslizamento suave para os links do footer
document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('mouseover', function() {
        this.style.transition = 'color 0.3s ease, transform 0.3s ease';
    });
});

// Efeito para os ícones de contato
document.querySelectorAll('.contact-list li').forEach(item => {
    item.addEventListener('mouseover', function() {
        const icon = this.querySelector('svg');
        if (icon) {
            icon.style.transition = 'transform 0.3s ease';
            icon.style.transform = 'scale(1.2)';
        }
    });
    
    item.addEventListener('mouseout', function() {
        const icon = this.querySelector('svg');
        if (icon) {
            icon.style.transform = 'scale(1)';
        }
    });
});

// Adiciona interatividade aos links sociais
document.querySelectorAll('.social-link').forEach(link => {
    link.addEventListener('mouseover', function() {
        const icon = this.querySelector('svg');
        if (icon) {
            icon.style.transition = 'transform 0.3s ease';
            icon.style.transform = 'rotate(15deg)';
        }
    });
    
    link.addEventListener('mouseout', function() {
        const icon = this.querySelector('svg');
        if (icon) {
            icon.style.transform = 'rotate(0deg)';
        }
    });
});

// Validação simples do formulário de newsletter
const newsletterForm = document.querySelector('.footer-newsletter form');
if (newsletterForm) {
    newsletterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const emailInput = this.querySelector('input[type="email"]');
        
        if (emailInput && emailInput.value) {
            // Aqui você adicionaria o código para enviar o email para seu backend
            // Por enquanto, apenas mostramos uma mensagem simples
            const originalHTML = this.innerHTML;
            this.innerHTML = '<p style="color: var(--cream); padding: 10px;">Obrigado por se inscrever!</p>';
            
            // Restaurar o formulário após 3 segundos
            setTimeout(() => {
                this.innerHTML = originalHTML;
                // Reaplica os event listeners após restaurar o HTML
                attachFormListeners();
            }, 3000);
        }
    });
}

function attachFormListeners() {
    const form = document.querySelector('.footer-newsletter form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Reimplementa a mesma lógica de submissão
            e.preventDefault();
            // ...resto do código...
        });
    }
}