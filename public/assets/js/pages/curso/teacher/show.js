/**
 * File: assets/js/pages/curso/teacher/show.js
 * JavaScript para funcionalidade da página de detalhes do curso
 */

document.addEventListener('DOMContentLoaded', function() {
  // Gerenciamento do menu dropdown
  const dropdownButton = document.getElementById('dropdownMenu');
  const dropdownOptions = document.getElementById('dropdownMenuOptions');
  
  if (dropdownButton && dropdownOptions) {
    // Abrir/fechar dropdown
    dropdownButton.addEventListener('click', function(e) {
      e.stopPropagation();
      dropdownOptions.classList.toggle('active');
    });
    
    // Fechar dropdown ao clicar fora
    document.addEventListener('click', function(e) {
      if (!dropdownButton.contains(e.target) && !dropdownOptions.contains(e.target)) {
        dropdownOptions.classList.remove('active');
      }
    });
  }
  
  // Inicializar animações de entrada
  animateContent();
});

// Função para animar a entrada dos elementos
function animateContent() {
  const header = document.querySelector('.course-detail-header');
  const tags = document.querySelector('.course-detail-tags');
  const sections = document.querySelectorAll('.course-detail-section');
  const actions = document.querySelector('.course-detail-actions');
  
  // Aplica classes com animações usando CSS
  if (header) header.classList.add('animate-fade-in');
  if (tags) {
    tags.classList.add('animate-slide-in');
    // Animar tags individualmente
    const tagElements = tags.querySelectorAll('.course-detail-tag');
    tagElements.forEach((tag, index) => {
      setTimeout(() => {
        tag.classList.add('animate-pop');
      }, 100 * index);
    });
  }
  
  // Animar seções com intervalo
  if (sections.length) {
    sections.forEach((section, index) => {
      setTimeout(() => {
        section.classList.add('animate-fade-up');
      }, 200 * index);
    });
  }
  
  // Animar botões de ação
  if (actions) {
    actions.classList.add('animate-fade-in');
    const buttons = actions.querySelectorAll('.course-detail-action-button');
    buttons.forEach((button, index) => {
      setTimeout(() => {
        button.classList.add('animate-slide-in');
      }, 100 * index);
    });
  }
}