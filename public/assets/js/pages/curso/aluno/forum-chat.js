document.addEventListener('DOMContentLoaded', () => {
  const messagesContainer = document.getElementById('messages');
  const form = document.getElementById('messageForm');
  const input = document.getElementById('messageInput');
  const { userId, courseId, wsUrl } = window.FORUM_CONFIG;

  console.log('Meu userId:', userId, typeof userId);

  let ws = null;
  let reconnectAttempts = 0;
  const maxReconnectAttempts = 5;

  function connect() {
    ws = new WebSocket(wsUrl);

    ws.onopen = () => {
      console.log('Conectado ao WebSocket');
      reconnectAttempts = 0;
      ws.send(JSON.stringify({
        type: 'join',
        courseId: courseId
      }));
    };

    ws.onmessage = (event) => {
      const data = JSON.parse(event.data);
      console.log('Mensagem recebida de:', data.userId);
      appendMessage(data);
    };

    ws.onclose = () => {
      console.log('Conexão WebSocket fechada');
      if (reconnectAttempts < maxReconnectAttempts) {
        reconnectAttempts++;
        setTimeout(connect, 3000 * reconnectAttempts);
      }
    };

    ws.onerror = (error) => {
      console.error('Erro WebSocket:', error);
    };
  }

  function appendMessage(data) {
    const messageDiv = document.createElement('div');
    // comparação flexível para alinhar corretamente
    const isSentByCurrentUser = data.userId == userId;
    console.log('É mensagem minha?', isSentByCurrentUser);

    const isTeacher = data.isTeacher || false;

    // aplica classes de alinhamento e estilo
    messageDiv.className =
      'message ' +
      (isSentByCurrentUser ? 'sent' : 'received') +
      (isTeacher ? ' teacher-message' : '');

    const now = new Date();
    const formattedDate = `${now.toLocaleDateString('pt-BR')} ${now.toLocaleTimeString('pt-BR')}`;

    messageDiv.innerHTML = `
      <div class="message-bubble">
        <div class="message-header">
          <img src="${data.avatar || '/assets/default-avatar.png'}" alt="Avatar" class="avatar">
          <div class="message-info">
            <strong>${data.userName}</strong>
            <time datetime="${now.toISOString()}">${formattedDate}</time>
          </div>
        </div>
        <div class="message-content">
          ${data.message.replace(/\n/g, '<br>')}
        </div>
      </div>
    `;

    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    // Clear float
    const clearDiv = document.createElement('div');
    clearDiv.style.clear = 'both';
    messagesContainer.appendChild(clearDiv);
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const messageText = input.value.trim();
    if (!messageText) return;

    // Otimistic update local
    appendMessage({
      userId: userId,
      userName: 'Você',
      avatar: '/assets/default-avatar.png',
      message: messageText,
      isTeacher: false
    });

    // Envia via WebSocket
    if (ws && ws.readyState === WebSocket.OPEN) {
      ws.send(JSON.stringify({
        type: 'message',
        userId: userId,
        courseId: courseId,
        message: messageText
      }));
    }

    // Envia para o backend HTTP (persistência)
    try {
      const response = await fetch(`/courses/${courseId}/forum`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ message: messageText })
      });
      if (!response.ok) {
        console.error('Erro ao salvar mensagem no backend:', response.statusText);
      }
    } catch (error) {
      console.error('Erro ao enviar mensagem:', error);
    }

    // Recarrega toda a página para refletir estado completo
    location.reload();
  });

  // Ajusta altura do textarea ao digitar
  function autoResize() {
    input.style.height = 'auto';
    input.style.height = `${input.scrollHeight}px`;
  }
  input.addEventListener('input', autoResize);

  // Inicia conexão WebSocket
  connect();

  // Garante wrapper em mensagens estáticas
  function fixExistingMessages() {
    document.querySelectorAll('#messages .message').forEach(msg => {
      if (!msg.querySelector('.message-bubble')) {
        const content = msg.innerHTML;
        msg.innerHTML = `<div class="message-bubble">${content}</div>`;
      }
    });
  }
  fixExistingMessages();
});
