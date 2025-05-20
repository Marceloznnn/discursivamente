// Script para a página de material do curso
document.addEventListener('DOMContentLoaded', function() {
  // Animações para elementos da página
  const fadeInElements = [
    document.getElementById('material-title'),
    document.getElementById('material-subtitle'),
    document.getElementById('material-content')
  ];
  
  // Aplicar fade in com delay para cada elemento
  fadeInElements.forEach((element, index) => {
    if (element) {
      element.style.opacity = '0';
      element.style.transform = 'translateY(20px)';
      element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
      
      setTimeout(() => {
        element.style.opacity = '1';
        element.style.transform = 'translateY(0)';
      }, 100 * index);
    }
  });
  
  // Tratamento especial para vídeos
  const videoElement = document.getElementById('material-video');
  if (videoElement) {
    // Salvar posição do vídeo no localStorage
    videoElement.addEventListener('timeupdate', function() {
      localStorage.setItem(
        `video-position-${window.location.pathname}`, 
        videoElement.currentTime
      );
    });
    
    // Restaurar posição do vídeo
    const savedPosition = localStorage.getItem(`video-position-${window.location.pathname}`);
    if (savedPosition) {
      videoElement.currentTime = parseFloat(savedPosition);
    }
    
    // Detectar quando o vídeo termina
    videoElement.addEventListener('ended', function() {
      const nextButton = document.getElementById('material-next');
      if (nextButton) {
        // Destacar o botão próximo quando o vídeo terminar
        nextButton.style.animation = 'pulse 1s infinite';
        nextButton.style.backgroundColor = 'var(--primary-light)';
        nextButton.style.borderColor = 'var(--primary-color)';
      }
    });
  }
  
  // Melhorar experiência de download
  const downloadButton = document.getElementById('material-download');
  if (downloadButton) {
    downloadButton.addEventListener('click', function() {
      // Feedback visual ao clicar no botão
      this.innerHTML = '⬇️ Baixando...';
      setTimeout(() => {
        this.innerHTML = '✅ Download Iniciado';
      }, 500);
      setTimeout(() => {
        this.innerHTML = '⬇️ Baixar Arquivo';
      }, 3000);
    });
  }
  
  // Verificar progresso no módulo
  markAsCompleted();
});

// Registrar que o usuário visualizou este material
function markAsCompleted() {
  // Extrair IDs do URL
  const urlParts = window.location.pathname.split('/');
  const courseId = urlParts[2];
  const moduleId = urlParts[4];
  const materialId = urlParts[6];
  
  // Armazenar no localStorage o progresso do aluno
  const progressKey = `course-${courseId}-progress`;
  let progress = JSON.parse(localStorage.getItem(progressKey) || '{}');
  
  if (!progress[moduleId]) {
    progress[moduleId] = {};
  }
  
  progress[moduleId][materialId] = {
    viewed: true,
    timestamp: new Date().toISOString()
  };
  
  localStorage.setItem(progressKey, JSON.stringify(progress));
  
  // Opcionalmente, enviar para o servidor em uma implementação real
  // sendProgressToServer(courseId, moduleId, materialId);
}

// Animação de pulse para botões
document.addEventListener('DOMContentLoaded', function() {
  const style = document.createElement('style');
  style.textContent = `
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
  `;
  document.head.appendChild(style);
});