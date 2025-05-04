// Script específico para o Footer

document.addEventListener('DOMContentLoaded', function() {
    // Animação para os links do footer
    const footerLinks = document.querySelectorAll('.footer-section a');
    
    footerLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.paddingLeft = '5px';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.paddingLeft = '0';
        });
    });
    
    // Animação suave para os ícones sociais
    const socialIcons = document.querySelectorAll('.social-icon');
    
    socialIcons.forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.2)';
        });
        
        icon.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Função para atualizar o ano de copyright caso necessário
    function updateCopyrightYear() {
        const copyrightElement = document.querySelector('.footer-bottom p');
        if (copyrightElement) {
            const currentYear = new Date().getFullYear();
            const copyrightText = copyrightElement.textContent;
            
            // Verifica se o ano atual já está no texto
            if (!copyrightText.includes(currentYear.toString())) {
                const updatedText = copyrightText.replace(/\d{4}(?!.*\d{4})/, currentYear);
                copyrightElement.textContent = updatedText;
            }
        }
    }
    
    // Não precisamos chamar updateCopyrightYear() aqui porque o Twig
    // já faz essa atualização no servidor com {{ 'now'|date('Y') }}
});