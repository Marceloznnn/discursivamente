// JavaScript para a página de perfil
document.addEventListener('DOMContentLoaded', function() {
  // Inicializar componentes
  initializeProfilePage();
});

function initializeProfilePage() {
  setupAvatarUpload();
  setupTooltips();
  setupAnimations();
}

/**
 * Configura o upload de avatar com preview
 */
function setupAvatarUpload() {
  const avatarUpload = document.getElementById('avatar-upload');
  const profileAvatar = document.getElementById('profile-avatar');
  const avatarForm = document.getElementById('avatar-form');
  
  if (!avatarUpload || !profileAvatar) return;

  // Adicionar feedback visual ao passar o mouse sobre o botão de upload
  const avatarLabel = document.getElementById('avatar-label');
  if (avatarLabel) {
    avatarLabel.addEventListener('mouseover', function() {
      profileAvatar.style.filter = 'brightness(0.9)';
    });
    
    avatarLabel.addEventListener('mouseout', function() {
      profileAvatar.style.filter = 'brightness(1)';
    });
  }

  // Mostrar preview antes do upload
  avatarUpload.addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
      // Verificar se o arquivo é uma imagem
      const fileType = this.files[0].type;
      if (!fileType.startsWith('image/')) {
        showNotification('Por favor, selecione apenas arquivos de imagem.', 'error');
        return;
      }

      // Verificar tamanho do arquivo (max 5MB)
      const fileSize = this.files[0].size / 1024 / 1024; // em MB
      if (fileSize > 5) {
        showNotification('A imagem não pode ser maior que 5MB.', 'error');
        return;
      }
      
      // Preview da imagem
      const reader = new FileReader();
      reader.onload = function(event) {
        // Criar transição suave
        profileAvatar.style.opacity = '0.5';
        
        setTimeout(() => {
          profileAvatar.src = event.target.result;
          profileAvatar.style.opacity = '1';
        }, 300);
      };
      
      reader.readAsDataURL(this.files[0]);
      
      // Enviar o formulário com um pequeno delay para permitir ver o preview
      setTimeout(() => {
        showNotification('Enviando imagem...', 'info');
        avatarForm.submit();
      }, 1000);
    }
  });
}

/**
 * Configura tooltips para melhorar a experiência do usuário
 */
function setupTooltips() {
  const avatarLabel = document.getElementById('avatar-label');
  
  if (avatarLabel) {
    // Criar tooltip element
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.innerText = 'Alterar foto';
    tooltip.style.cssText = `
      position: absolute;
      background-color: rgba(0,0,0,0.7);
      color: white;
      padding: 5px 10px;
      border-radius: 4px;
      font-size: 12px;
      pointer-events: none;
      opacity: 0;
      transition: opacity 0.3s;
      white-space: nowrap;
      z-index: 100;
    `;
    
    document.body.appendChild(tooltip);
    
    // Mostrar/esconder tooltip
    avatarLabel.addEventListener('mouseover', (e) => {
      const rect = avatarLabel.getBoundingClientRect();
      tooltip.style.top = `${rect.top - 30}px`;
      tooltip.style.left = `${rect.left + rect.width/2 - tooltip.offsetWidth/2}px`;
      tooltip.style.opacity = '1';
    });
    
    avatarLabel.addEventListener('mouseout', () => {
      tooltip.style.opacity = '0';
    });
  }
}

/**
 * Configura animações para melhorar a experiência visual
 */
function setupAnimations() {
  // Adicionar classes de animação aos elementos
  const profileCard = document.getElementById('profile-card');
  if (profileCard) {
    profileCard.classList.add('animated-fade-in');
  }
  
  // Adicionar foco suave no campo de texto ao editar
  const bioSection = document.getElementById('profile-bio');
  if (bioSection) {
    bioSection.addEventListener('click', () => {
      const editUrl = document.getElementById('btn-edit').getAttribute('href');
      if (editUrl) {
        window.location.href = editUrl;
      }
    });
  }
}

/**
 * Exibe notificações na interface
 * @param {string} message - Mensagem a ser exibida
 * @param {string} type - Tipo de notificação (success, error, info)
 */
function showNotification(message, type = 'info') {
  // Verificar se já existe uma notificação e removê-la
  const existingNotification = document.querySelector('.profile-notification');
  if (existingNotification) {
    existingNotification.remove();
  }
  
  // Criar elemento de notificação
  const notification = document.createElement('div');
  notification.className = `profile-notification ${type}`;
  notification.innerHTML = `
    <div class="notification-content">
      <span>${message}</span>
      <button class="notification-close">×</button>
    </div>
  `;
  
  // Estilizar notificação
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 280px;
    background-color: white;
    border-radius: 4px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    overflow: hidden;
    z-index: 1000;
    transform: translateY(-100px);
    opacity: 0;
    transition: all 0.4s ease;
  `;
  
  // Estilizar baseado no tipo
  let borderColor = '#3498db';
  if (type === 'success') borderColor = '#2ecc71';
  if (type === 'error') borderColor = '#e74c3c';
  
  notification.style.borderLeft = `4px solid ${borderColor}`;
  
  // Estilizar conteúdo
  const content = notification.querySelector('.notification-content');
  content.style.cssText = `
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  `;
  
  // Estilizar botão de fechar
  const closeBtn = notification.querySelector('.notification-close');
  closeBtn.style.cssText = `
    background: none;
    border: none;
    color: #999;
    cursor: pointer;
    font-size: 20px;
    margin-left: 10px;
  `;
  
  // Adicionar notificação ao DOM
  document.body.appendChild(notification);
  
  // Animar entrada
  setTimeout(() => {
    notification.style.transform = 'translateY(0)';
    notification.style.opacity = '1';
  }, 10);
  
  // Configurar fechamento
  closeBtn.addEventListener('click', () => {
    closeNotification(notification);
  });
  
  // Auto-fechar após 5 segundos
  setTimeout(() => {
    closeNotification(notification);
  }, 5000);
}

/**
 * Fecha uma notificação com animação
 * @param {HTMLElement} notification - Elemento de notificação
 */
function closeNotification(notification) {
  notification.style.transform = 'translateY(-20px)';
  notification.style.opacity = '0';
  
  setTimeout(() => {
    notification.remove();
  }, 400);
}

// Adicionar estilos globais para animações
const style = document.createElement('style');
style.textContent = `
  .animated-fade-in {
    animation: fadeIn 0.5s ease-out forwards;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  /* Adicionar ripple effect nos botões */
  #btn-edit, #btn-logout {
    position: relative;
    overflow: hidden;
  }
  
  #btn-edit::after, #btn-logout::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    transform: scale(0);
    opacity: 0;
    transition: transform 0.5s, opacity 0.3s;
  }
  
  #btn-edit:active::after, #btn-logout:active::after {
    transform: scale(3);
    opacity: 0;
    transition: 0s;
  }
  
  /* Estilo para o bio mostrando que é clicável */
  #profile-bio {
    cursor: pointer;
    transition: background-color 0.3s;
  }
  
  #profile-bio:hover {
    background-color: rgba(240, 240, 240, 0.5);
  }
  
  #profile-bio:hover::after {
    content: 'Clique para editar';
    display: block;
    text-align: right;
    font-size: 12px;
    color: #888;
    margin-top: 10px;
  }
`;

document.head.appendChild(style);