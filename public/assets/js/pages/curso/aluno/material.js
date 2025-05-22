// Script para a página de material do curso
document.addEventListener('DOMContentLoaded', function() {
  // --- Animações para elementos da página ---
  const fadeInElements = [
    document.getElementById('material-title'),
    document.getElementById('material-subtitle'),
    document.getElementById('material-content')
  ];
  
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

  // --- Marcar automaticamente como concluído (dependendo do tipo de material) ---
  const contentElement = document.getElementById('material-content');
  const entryId = contentElement?.dataset.entryId;

  if (entryId) {
    const image = contentElement.querySelector('img');
    const video = contentElement.querySelector('video');
    const link = contentElement.querySelector('a[target="_blank"], a[download]');

    if (image) {
      // Marcar após 5 segundos de visualização da imagem
      setTimeout(() => {
        marcarComoConcluido(entryId);
      }, 5000);
    }

    if (video) {
      // Salvar posição do vídeo
      video.addEventListener('timeupdate', () => {
        localStorage.setItem(
          `video-position-${window.location.pathname}`, 
          video.currentTime
        );

        // Marcar como concluído após 80% assistido
        const porcentagem = video.currentTime / video.duration;
        if (porcentagem >= 0.8 && !video.dataset.progressoEnviado) {
          marcarComoConcluido(entryId);
          video.dataset.progressoEnviado = 'true';
        }
      });

      // Restaurar posição
      const saved = localStorage.getItem(`video-position-${window.location.pathname}`);
      if (saved) video.currentTime = parseFloat(saved);

      // Destacar botão próximo ao final
      video.addEventListener('ended', function() {
        const nextButton = document.getElementById('material-next');
        if (nextButton) {
          nextButton.style.animation = 'pulse 1s infinite';
          nextButton.style.backgroundColor = 'var(--primary-light)';
          nextButton.style.borderColor = 'var(--primary-color)';
        }
      });
    }

    if (link) {
      link.addEventListener('click', () => {
        marcarComoConcluido(entryId);
      });
    }

    // Se não for vídeo ou download, marcar após 30s de visualização (ex: textos)
    if (!image && !video && !link) {
      setTimeout(() => {
        marcarComoConcluido(entryId);
      }, 30000);
    }
  }

  // --- Botão de download (efeito visual) ---
  const downloadButton = document.getElementById('material-download');
  if (downloadButton) {
    downloadButton.addEventListener('click', function() {
      this.innerHTML = '⬇️ Baixando...';
      setTimeout(() => {
        this.innerHTML = '✅ Download Iniciado';
      }, 500);
      setTimeout(() => {
        this.innerHTML = '⬇️ Baixar Arquivo';
      }, 3000);
    });
  }

  // --- Marcar progresso localmente ---
  markAsCompleted();

  // --- Animação de pulse (para botão "próximo") ---
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

// Marcar progresso no localStorage
function markAsCompleted() {
  const urlParts = window.location.pathname.split('/');
  const courseId = urlParts[2];
  const moduleId = urlParts[4];
  const materialId = urlParts[6];

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
}

// (Opcional) Enviar progresso para o servidor
function marcarComoConcluido(entryId) {
  fetch(`/api/progresso/concluir/${entryId}`, { method: 'POST' })
    .catch(err => console.warn('Erro ao marcar como concluído:', err));
}
