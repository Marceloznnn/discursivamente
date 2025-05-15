// JavaScript para melhorar a navegação e interatividade da página de material

// Destacar o item ativo na sidebar ao clicar
const sidebarLinks = document.querySelectorAll('.material-sidebar ul li a');
sidebarLinks.forEach(link => {
  link.addEventListener('click', () => {
    sidebarLinks.forEach(l => l.parentElement.classList.remove('active'));
    link.parentElement.classList.add('active');
  });
});

// Função para ajustar a altura do iframe para melhor visualização
const iframe = document.querySelector('.media-wrapper.iframe iframe');
if (iframe) {
  iframe.style.height = '600px';
}

// Melhorias adicionais podem ser adicionadas conforme necessidade
