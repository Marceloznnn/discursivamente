<footer id="siteFooter" role="contentinfo">
    <div class="footer-container">
        <!-- Seção principal do footer com informações, links e contatos -->
        <div class="footer-main">
            <!-- Sobre o projeto -->
            <div class="footer-section footer-about">
                <h3 class="footer-heading">Biblioteca Discursivamente</h3>
                <p class="footer-description">Uma plataforma inovadora dedicada à promoção do conhecimento, debate
                    construtivo e desenvolvimento acadêmico, criando pontes entre ideias e pessoas.</p>
                <div class="footer-social">
                    <a href="#" class="social-link" aria-label="Facebook">
                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Twitter">
                        <i class="fab fa-twitter" aria-hidden="true"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="Instagram">
                        <i class="fab fa-instagram" aria-hidden="true"></i>
                    </a>
                    <a href="#" class="social-link" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <!-- Links úteis -->
            <div class="footer-section footer-links">
                <h3 class="footer-heading">Links Úteis</h3>
                <ul class="footer-nav">
                    <li class="footer-nav-item">
                        <a href="/home" class="footer-nav-link">
                            <i class="fas fa-chevron-right" aria-hidden="true"></i> Home
                        </a>
                    </li>
                    <li class="footer-nav-item">
                        <a href="/biblioteca" class="footer-nav-link">
                            <i class="fas fa-chevron-right" aria-hidden="true"></i> Biblioteca
                        </a>
                    </li>
                    <li class="footer-nav-item">
                        <a href="/comunidade/comunicacao" class="footer-nav-link">
                            <i class="fas fa-chevron-right" aria-hidden="true"></i> Comunidade
                        </a>
                    </li>
                    <li class="footer-nav-item">
                        <a href="/compromissos" class="footer-nav-link">
                            <i class="fas fa-chevron-right" aria-hidden="true"></i> Compromissos
                        </a>
                    </li>
                    <li class="footer-nav-item">
                        <a href="/quem-somos" class="footer-nav-link">
                            <i class="fas fa-chevron-right" aria-hidden="true"></i> Quem Somos
                        </a>
                    </li>
                    <li class="footer-nav-item">
                        <a href="/politica-privacidade" class="footer-nav-link">
                            <i class="fas fa-chevron-right" aria-hidden="true"></i> Política de Privacidade
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contato -->
            <div class="footer-section footer-contact">
                <h3 class="footer-heading">Contato</h3>
                <ul class="footer-contact-list">
                    <li class="footer-contact-item">
                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                        <span>Av. Maranhão, 1000, Codó - MA</span>
                    </li>
                    <li class="footer-contact-item">
                        <i class="fas fa-envelope" aria-hidden="true"></i>
                        <a href="mailto:projeto.discursivamente@gmail.com">projeto.discursivamente@gmail.com</a>
                    </li>
                    <li class="footer-contact-item">
                        <i class="fas fa-phone-alt" aria-hidden="true"></i>
                        <a href="tel:+551123456789">(98) 9212-0055</a>
                    </li>
                    <li class="footer-contact-item">
                        <i class="fas fa-clock" aria-hidden="true"></i>
                        <span>Segunda a Sexta: 9h às 18h</span>
                    </li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="footer-section footer-newsletter">
                <h3 class="footer-heading">Newsletter</h3>
                <p class="footer-newsletter-text">Inscreva-se para receber novidades e atualizações da nossa biblioteca.
                </p>
                <form class="footer-form" action="/newsletter/subscribe" method="post">
                    <div class="form-group">
                        <input type="email" name="email" id="footer-email" placeholder="Seu e-mail" required
                            class="footer-input">
                        <button type="submit" class="footer-button">
                            <i class="fas fa-paper-plane" aria-hidden="true"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Seção de colaboradores -->
        <div class="footer-collaborators">
            <h3 class="collaborators-heading">Nossos Colaboradores</h3>
            <div class="collaborators-carousel">
                <div class="collaborator-item">
                    <a href="https://lptacademico.me/" target="_blank" aria-label="LPT Acadêmico">
                        <img src="/assets/images/colaborators/lptacademico.jpg" alt="LPT Acadêmico"
                            class="collaborator-logo">
                        <span class="collaborator-name">LPT Acadêmico</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Divisor -->
        <div class="footer-divider"></div>

        <!-- Rodapé do footer com copyright -->
        <div class="footer-bottom">
            <p class="copyright">&copy;
                <?php echo date('Y'); ?>
                Discursivamente. Todos os direitos reservados.</p>
            <p class="footer-credits">Desenvolvido com <i class="fas fa-heart" aria-hidden="true"></i> para a comunidade
                acadêmica</p>
        </div>
    </div>
</footer>