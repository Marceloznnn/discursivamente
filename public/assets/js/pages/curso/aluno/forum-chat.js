document.addEventListener('DOMContentLoaded', () => {
    const messagesContainer = document.getElementById('messages');
    const form = document.getElementById('messageForm');
    const input = document.getElementById('messageInput');
    const { userId, courseId, wsUrl } = window.FORUM_CONFIG;

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
        messageDiv.className = 'message';

        const now = new Date();
        const formattedDate = now.toLocaleDateString('pt-BR') + ' ' + 
                            now.toLocaleTimeString('pt-BR');

        messageDiv.innerHTML = `
            <div class="message-header">
                <img src="${data.avatar || '/assets/default-avatar.png'}" alt="Avatar" class="avatar">
                <div class="message-info">
                    <strong>${data.userName}</strong>
                    <time datetime="${now.toISOString()}">${formattedDate}</time>
                </div>
            </div>
            <div class="message-content">
                ${data.message.replace(/\\n/g, '<br>')}
            </div>
        `;

        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const message = input.value.trim();
        if (message === '') return;

        // Envia para o WebSocket
        if (ws && ws.readyState === WebSocket.OPEN) {
            ws.send(JSON.stringify({
                type: 'message',
                userId: userId,
                courseId: courseId,
                message: message
            }));
        }

        // Envia para o backend via HTTP (fallback)
        try {
            const response = await fetch(`/courses/${courseId}/forum`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `message=${encodeURIComponent(message)}`
            });

            if (!response.ok) {
                throw new Error('Falha ao enviar mensagem');
            }
        } catch (error) {
            console.error('Erro ao enviar mensagem:', error);
            alert('Não foi possível enviar a mensagem. Por favor, tente novamente.');
            return;
        }

        input.value = '';
    });

    // Auto-resize do textarea
    input.addEventListener('input', () => {
        input.style.height = 'auto';
        input.style.height = (input.scrollHeight) + 'px';
    });

    // Inicia a conexão WebSocket
    connect();
});
