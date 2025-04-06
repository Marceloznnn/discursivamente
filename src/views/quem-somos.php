<?php require_once BASE_PATH . '/src/views/partials/header.php'; ?>

<!-- Banner Inicial -->
<div class="banner" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('/assets/images/banner-projeto.jpg') no-repeat center center; background-size: cover; padding: 80px 20px; text-align: center; color: #fff;">
    <div class="container">
        <h1 style="font-size: 2.5rem; margin-bottom: 20px;">Sobre o Projeto</h1>
        <p style="font-size: 1.2rem; max-width: 800px; margin: 0 auto;">Conheça nossa missão, visão e os valores que impulsionam o Discursivamente, promovendo o diálogo e a comunicação significativa.</p>
    </div>
</div>

<!-- Seção Sobre o Projeto -->
<section class="sobre-projeto" style="padding: 60px 20px; background-color: #fff;">
    <div class="container" style="max-width: 1200px; margin: 0 auto;">
        <h2 style="text-align: center; margin-bottom: 40px; color: #333; font-size: 2rem;">O Projeto Discursivamente</h2>
        
        <div style="display: flex; flex-wrap: wrap; gap: 30px; align-items: center;">
            <!-- Imagem ilustrativa do projeto -->
            <div style="flex: 1 1 300px;">
                <img src="/assets/images/projeto-ilustracao.jpg" alt="Ilustração do Projeto" style="width: 100%; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            </div>
            
            <!-- Descrição do projeto -->
            <div style="flex: 1 1 500px;">
                <h3 style="color: #444; margin-bottom: 20px;">Nossa Missão</h3>
                <p style="margin-bottom: 20px; line-height: 1.6; color: #555;">O projeto Discursivamente nasceu da necessidade de criar um espaço onde o diálogo e a comunicação possam florescer de forma construtiva. Buscamos fomentar o debate respeitoso e o intercâmbio de ideias em um ambiente digital seguro e inclusivo.</p>
                
                <h3 style="color: #444; margin-bottom: 20px;">Visão</h3>
                <p style="margin-bottom: 20px; line-height: 1.6; color: #555;">Aspiramos ser uma referência na promoção do pensamento crítico e da comunicação efetiva, contribuindo para a formação de uma sociedade mais reflexiva e capaz de dialogar sobre temas complexos com respeito e empatia.</p>
                
                <h3 style="color: #444; margin-bottom: 20px;">Objetivos</h3>
                <ul style="margin-bottom: 20px; line-height: 1.6; color: #555; padding-left: 20px;">
                    <li style="margin-bottom: 10px;">Desenvolver ferramentas e metodologias que promovam a qualidade do discurso público</li>
                    <li style="margin-bottom: 10px;">Criar espaços seguros para o debate de ideias e o compartilhamento de perspectivas diversas</li>
                    <li style="margin-bottom: 10px;">Fomentar a pesquisa e o desenvolvimento de tecnologias que auxiliem na comunicação construtiva</li>
                    <li>Educar e capacitar pessoas para o diálogo efetivo e respeitoso em ambientes digitais</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Seção dos Membros -->
<section class="membros" style="padding: 60px 20px; background-color: #f5f5f5;">
    <div class="container" style="max-width: 1200px; margin: 0 auto;">
        <h2 style="text-align: center; margin-bottom: 50px; color: #333; font-size: 2rem;">Nossa Equipe</h2>
        
        <!-- Grid de membros -->
        <div class="membros-grid" style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center;">
            
            <!-- Card de Membro 1 -->
            <div class="membro-card" style="flex: 1 1 300px; max-width: 350px; background-color: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                <div style="padding: 30px 20px; text-align: center;">
                    <!-- Imagem Redonda -->
                    <div style="width: 150px; height: 150px; margin: 0 auto 20px; border-radius: 50%; overflow: hidden; border: 3px solid #3498db;">
                        <img src="/assets/images/membros/membro1.jpg" alt="Ana Silva" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    
                    <!-- Nome do Membro -->
                    <h3 style="margin: 0 0 10px; color: #333; font-size: 1.5rem;">Ana Silva</h3>
                    
                    <!-- Cargo -->
                    <p style="margin: 0 0 15px; color: #3498db; font-weight: 500;">Coordenadora do Projeto</p>
                    
                    <!-- Biografia -->
                    <p style="margin: 0 0 20px; line-height: 1.6; color: #666;">Doutora em Comunicação Social, com experiência em análise do discurso e metodologias de diálogo. Fundadora do Discursivamente e pesquisadora na área de comunicação digital.</p>
                    
                    <!-- Ícones de Redes Sociais -->
                    <div class="redes-sociais" style="display: flex; justify-content: center; gap: 15px;">
                        <a href="https://instagram.com/anasilva" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #E1306C; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://linkedin.com/in/anasilva" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #0077B5; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://facebook.com/anasilva" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #3B5998; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/anasilva" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #1DA1F2; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Card de Membro 2 -->
            <div class="membro-card" style="flex: 1 1 300px; max-width: 350px; background-color: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                <div style="padding: 30px 20px; text-align: center;">
                    <!-- Imagem Redonda -->
                    <div style="width: 150px; height: 150px; margin: 0 auto 20px; border-radius: 50%; overflow: hidden; border: 3px solid #3498db;">
                        <img src="/assets/images/membros/membro2.jpg" alt="Carlos Mendes" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    
                    <!-- Nome do Membro -->
                    <h3 style="margin: 0 0 10px; color: #333; font-size: 1.5rem;">Carlos Mendes</h3>
                    
                    <!-- Cargo -->
                    <p style="margin: 0 0 15px; color: #3498db; font-weight: 500;">Desenvolvedor Principal</p>
                    
                    <!-- Biografia -->
                    <p style="margin: 0 0 20px; line-height: 1.6; color: #666;">Especialista em desenvolvimento web com foco em aplicações interativas e acessíveis. Responsável pela arquitetura técnica e implementação da plataforma Discursivamente.</p>
                    
                    <!-- Ícones de Redes Sociais -->
                    <div class="redes-sociais" style="display: flex; justify-content: center; gap: 15px;">
                        <a href="https://instagram.com/carlosmendes" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #E1306C; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://linkedin.com/in/carlosmendes" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #0077B5; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://facebook.com/carlosmendes" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #3B5998; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/carlosmendes" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #1DA1F2; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Card de Membro 3 -->
            <div class="membro-card" style="flex: 1 1 300px; max-width: 350px; background-color: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                <div style="padding: 30px 20px; text-align: center;">
                    <!-- Imagem Redonda -->
                    <div style="width: 150px; height: 150px; margin: 0 auto 20px; border-radius: 50%; overflow: hidden; border: 3px solid #3498db;">
                        <img src="/assets/images/membros/membro3.jpg" alt="Marina Costa" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    
                    <!-- Nome do Membro -->
                    <h3 style="margin: 0 0 10px; color: #333; font-size: 1.5rem;">Marina Costa</h3>
                    
                    <!-- Cargo -->
                    <p style="margin: 0 0 15px; color: #3498db; font-weight: 500;">Especialista em UX/UI</p>
                    
                    <!-- Biografia -->
                    <p style="margin: 0 0 20px; line-height: 1.6; color: #666;">Designer com foco em experiência do usuário e interfaces intuitivas. Responsável pelo design visual e pela usabilidade da plataforma, garantindo uma experiência acessível a todos.</p>
                    
                    <!-- Ícones de Redes Sociais -->
                    <div class="redes-sociais" style="display: flex; justify-content: center; gap: 15px;">
                        <a href="https://instagram.com/marinacosta" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #E1306C; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://linkedin.com/in/marinacosta" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #0077B5; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://facebook.com/marinacosta" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #3B5998; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/marinacosta" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #1DA1F2; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Card de Membro 4 -->
            <div class="membro-card" style="flex: 1 1 300px; max-width: 350px; background-color: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                <div style="padding: 30px 20px; text-align: center;">
                    <!-- Imagem Redonda -->
                    <div style="width: 150px; height: 150px; margin: 0 auto 20px; border-radius: 50%; overflow: hidden; border: 3px solid #3498db;">
                        <img src="/assets/images/membros/membro4.jpg" alt="Lucas Oliveira" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    
                    <!-- Nome do Membro -->
                    <h3 style="margin: 0 0 10px; color: #333; font-size: 1.5rem;">Lucas Oliveira</h3>
                    
                    <!-- Cargo -->
                    <p style="margin: 0 0 15px; color: #3498db; font-weight: 500;">Analista de Conteúdo</p>
                    
                    <!-- Biografia -->
                    <p style="margin: 0 0 20px; line-height: 1.6; color: #666;">Formado em Letras e especialista em produção de conteúdo digital. Responsável pela curadoria de materiais e pela estratégia de comunicação do projeto Discursivamente.</p>
                    
                    <!-- Ícones de Redes Sociais -->
                    <div class="redes-sociais" style="display: flex; justify-content: center; gap: 15px;">
                        <a href="https://instagram.com/lucasoliveira" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #E1306C; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://linkedin.com/in/lucasoliveira" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #0077B5; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://facebook.com/lucasoliveira" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #3B5998; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/lucasoliveira" target="_blank" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #1DA1F2; border-radius: 50%; color: #fff; text-decoration: none; transition: transform 0.2s ease;">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- Script para animações e efeitos hover -->
<script>
    // Adiciona efeito de hover nos cards de membros
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.membro-card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Adiciona efeito de hover nos ícones de redes sociais
        const socialIcons = document.querySelectorAll('.redes-sociais a');
        
        socialIcons.forEach(icon => {
            icon.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.2)';
            });
            
            icon.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    });
</script>

<?php require_once BASE_PATH . '/src/views/partials/footer.php'; ?>