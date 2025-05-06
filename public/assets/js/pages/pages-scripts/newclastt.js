/**
 * Newsletter JavaScript
 * Gerencia a inscrição na newsletter e validações do formulário
 */
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.getElementById('newsletterForm');
    const emailInput = document.getElementById('newsletter-email');
    
    if (!newsletterForm) return;
    
    // Validação de Email
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Cria elemento de feedback
    function createFeedbackElement() {
        const feedback = document.createElement('div');
        feedback.className = 'form-feedback';
        feedback.style.padding = '10px';
        feedback.style.marginTop = '10px';
        feedback.style.borderRadius = 'var(--radius-sm)';
        feedback.style.fontWeight = '500';
        feedback.style.textAlign = 'center';
        
        return feedback;
    }
    
    // Mostra mensagem de sucesso
    function showSuccess(message) {
        const feedback = createFeedbackElement();
        feedback.textContent = message;
        feedback.style.backgroundColor = 'rgba(0, 128, 0, 0.1)';
        feedback.style.color = '#006400';
        
        // Remove qualquer feedback anterior
        const existingFeedback = newsletterForm.querySelector('.form-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
        
        newsletterForm.appendChild(feedback);
        
        // Limpa o formulário
        newsletterForm.reset();
        
        // Remove o feedback após 5 segundos
        setTimeout(() => {
            feedback.style.opacity = '0';
            feedback.style.transition = 'opacity 0.5s ease';
            
            setTimeout(() => {
                feedback.remove();
            }, 500);
        }, 5000);
    }
    
    // Mostra mensagem de erro
    function showError(message) {
        const feedback = createFeedbackElement();
        feedback.textContent = message;
        feedback.style.backgroundColor = 'rgba(220, 53, 69, 0.1)';
        feedback.style.color = '#dc3545';
        
        // Remove qualquer feedback anterior
        const existingFeedback = newsletterForm.querySelector('.form-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
        
        newsletterForm.appendChild(feedback);
        
        // Destaca o campo com erro
        emailInput.style.borderColor = '#dc3545';
        emailInput.focus();
        
        // Remove o destaque após o usuário começar a digitar
        emailInput.addEventListener('input', function() {
            emailInput.style.borderColor = '';
        }, { once: true });
    }
    
    // Animação de envio
    function animateSubmitButton(isLoading) {
        const submitButton = newsletterForm.querySelector('button[type="submit"]');
        
        if (isLoading) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        } else {
            submitButton.disabled = false;
            submitButton.innerHTML = 'Inscrever';
        }
    }
    
    // Manipula o envio do formulário
    newsletterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = emailInput.value.trim();
        
        // Verifica se o email é válido
        if (!isValidEmail(email)) {
            showError('Por favor, insira um endereço de email válido.');
            return;
        }
        
        // Coleta dados de preferências
        const prefCursos = newsletterForm.querySelector('input[name="pref_cursos"]').checked;
        const prefEventos = newsletterForm.querySelector('input[name="pref_eventos"]').checked;
        const prefConteudos = newsletterForm.querySelector('input[name="pref_conteudos"]').checked;
        
        // Dados para enviar ao servidor
        const formData = {
            email: email,
            preferences: {
                cursos: prefCursos,
                eventos: prefEventos,
                conteudos: prefConteudos
            }
        };
        
        // Simulação de envio de dados para o servidor
        animateSubmitButton(true);
        
        // Simula uma requisição AJAX (substitua por uma requisição real)
        setTimeout(() => {
            console.log('Dados enviados:', formData);
            
            // Simula resposta do servidor
            animateSubmitButton(false);
            showSuccess('Inscrição realizada com sucesso! Verifique seu email para confirmar.');
            
            // Evento de analytics (substitua pelo seu código de analytics)
            if (typeof gtag === 'function') {
                gtag('event', 'newsletter_signup', {
                    'event_category': 'engagement',
                    'event_label': 'homepage_newsletter'
                });
            }
        }, 1500);
    });
    
    // Adiciona interatividade aos checkboxes
    const preferenceCheckboxes = newsletterForm.querySelectorAll('.preference-checkboxes input');
    preferenceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            this.parentElement.style.fontWeight = this.checked ? '600' : '400';
        });
    });
});