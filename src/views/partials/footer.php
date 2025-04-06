<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer-content">
            <!-- Seção Sobre -->
            <div class="footer-section about">
                <h3>Sobre a Biblioteca</h3>
                <p>A Biblioteca Discursivamente é um espaço dedicado à promoção da leitura e do conhecimento, oferecendo
                    um rico acervo em diversos formatos e temáticas para todos os públicos.</p>
                <div class="social-links">
                    <a href="#" class="social-link" aria-label="Siga-nos no Facebook">
                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Siga-nos no Instagram">
                        <i class="fab fa-instagram" aria-hidden="true"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Siga-nos no Twitter">
                        <i class="fab fa-twitter" aria-hidden="true"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Inscreva-se no nosso canal do YouTube">
                        <i class="fab fa-youtube" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <!-- Seção Links Rápidos -->
            <div class="footer-section links">
                <h3>Links Rápidos</h3>
                <ul class="footer-links">
                    <li><a href="/home">Home</a></li>
                    <li><a href="/comunidade/comunicacao">Comunidade</a></li>
                    <li><a href="/biblioteca">Biblioteca</a></li>
                    <li><a href="/compromissos">Compromissos</a></li>
                    <li><a href="/quem-somos">Quem Somos</a></li>
                    <li><a href="/contato">Contato</a></li>
                    <li><a href="/perguntas-frequentes">Perguntas Frequentes</a></li>
                </ul>
            </div>

            <!-- Seção Newsletter -->
            <div class="footer-section newsletter">
                <h3>Newsletter</h3>
                <p>Assine nossa newsletter para receber as últimas novidades sobre eventos, livros e atividades da biblioteca.</p>
                <form class="newsletter-form" aria-label="Formulário de inscrição na newsletter">
                    <div class="form-group">
                        <input type="email" class="newsletter-input" placeholder="Seu email" required aria-label="Digite seu email">
                        <button class="newsletter-btn" type="submit" aria-label="Inscrever-se na newsletter">Inscrever</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Seção Copyright -->
        <div class="copyright">
            &copy; <?php echo date('Y'); ?> Biblioteca Discursivamente. Todos os direitos reservados.
        </div>
    </div>
</footer>

<!-- Script para funcionalidade do newsletter -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Captura o formulário de newsletter
        const newsletterForm = document.querySelector('.newsletter-form');
        
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const emailInput = this.querySelector('.newsletter-input');
                const email = emailInput.value.trim();
                
                if (email) {
                    // Aqui você implementaria a lógica para enviar o email para seu sistema
                    // Por enquanto, vamos apenas mostrar um feedback visual
                    emailInput.value = '';
                    
                    // Cria um elemento de feedback
                    const feedback = document.createElement('div');
                    feedback.classList.add('newsletter-feedback');
                    feedback.textContent = 'Obrigado por se inscrever!';
                    feedback.style.color = '#10b981';
                    feedback.style.marginTop = '10px';
                    feedback.style.fontWeight = '500';
                    
                    // Adiciona o feedback após o formulário
                    this.appendChild(feedback);
                    
                    // Remove o feedback após 3 segundos
                    setTimeout(() => {
                        feedback.remove();
                    }, 3000);
                }
            });
        }
    });
</script>