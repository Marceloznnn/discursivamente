const socket = new WebSocket('ws://localhost:8081');

const chatbotContainer = document.querySelector('.chatbot-container');
const toggleButton = document.querySelector('.chatbot-toggle');
const closeButton = document.querySelector('.chatbot-close');
const chatWindow = document.getElementById('chatbot-window');
const messagesContainer = document.querySelector('.chatbot-messages');
const inputField = document.getElementById('chatbot-input');
const sendButton = document.getElementById('chatbot-send');

const chatId = Date.now(); // ID único temporário da sessão
const currentUserId = window.currentUserId || 'user'; // Configure conforme necessário
const currentUserName = window.currentUserName || 'Você'; // Configure conforme necessário
const isSupport = window.isSupport || false;

const botFlow = {
    welcome: "👋 Olá! Bem-vindo(a) ao Atendimento Discursivamente. Como posso te ajudar? Escolha uma opção abaixo:",
    options: [
        { id: 'talk-to-support', label: '🗨️ Falar com o Atendente' },
        { id: 'common-questions', label: '❓ Dúvidas Comuns' }
    ],
    faqs: [
        {
            id: 'material',
            question: '📚 O que são os Materiais Didáticos Digitais?',
            answer: 'Nossos materiais são baseados na Análise de Discurso Crítica. Ajudam na leitura acadêmica no Ensino Médio.'
        },
        {
            id: 'leitura',
            question: '📖 Como funciona a leitura acadêmica?',
            answer: 'A leitura acadêmica desenvolve o pensamento crítico, com foco na interpretação contextualizada dos textos.'
        },
        {
            id: 'suporte',
            question: '⚙️ Como falar com o suporte?',
            answer: 'Clique em "Falar com o Atendente" e um atendente será notificado.'
        }
    ],
    default: "❗ Não entendi sua solicitação. Por favor, selecione uma opção."
};

// ==== UI ====

function displayMessage(message, sender = 'bot', userName = '') {
    const messageElement = document.createElement('div');
    messageElement.classList.add('chatbot-message');

    if (sender === 'user') {
        messageElement.classList.add('chatbot-message--self');
        messageElement.innerHTML = `<strong>${userName}:</strong> ${message}`;
    } else if (sender === 'bot') {
        messageElement.classList.add('chatbot-message--bot');
        messageElement.innerHTML = `<strong>Atendente:</strong> ${message}`;
    } else if (sender === 'system') {
        messageElement.classList.add('chatbot-message--system');
        messageElement.innerHTML = `<em>${message}</em>`;
    }

    messagesContainer.appendChild(messageElement);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function displayOptions(options) {
    const wrapper = document.createElement('div');
    wrapper.classList.add('chatbot-message', 'chatbot-message--bot');

    options.forEach(opt => {
        const btn = document.createElement('button');
        btn.classList.add('chatbot-option');
        btn.textContent = opt.label;
        btn.dataset.id = opt.id;
        btn.addEventListener('click', () => handleOption(opt.id));
        wrapper.appendChild(btn);
    });

    messagesContainer.appendChild(wrapper);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// ==== Fluxo ====

function startChat() {
    displayMessage(botFlow.welcome, 'bot');
    displayOptions(botFlow.options);
}

function handleOption(optionId) {
    if (optionId === 'talk-to-support') {
        displayMessage("🔔 Você solicitou falar com o atendente. Aguarde enquanto localizamos alguém...", 'bot');
        notifySupport();
    } else if (optionId === 'common-questions') {
        displayMessage("📑 Aqui estão algumas dúvidas comuns. Clique em uma delas:", 'bot');
        const faqOptions = botFlow.faqs.map(faq => ({ id: faq.id, label: faq.question }));
        displayOptions(faqOptions);
    } else {
        const faq = botFlow.faqs.find(f => f.id === optionId);
        if (faq) {
            displayMessage(faq.answer, 'bot');
        } else {
            displayMessage(botFlow.default, 'bot');
        }
    }
}

function processUserMessage(message) {
    const msgLower = message.toLowerCase();
    if (msgLower.includes('falar com atendente')) {
        handleOption('talk-to-support');
    } else if (msgLower.includes('duvida') || msgLower.includes('dúvida')) {
        handleOption('common-questions');
    } else {
        const matchedFaq = botFlow.faqs.find(faq => msgLower.includes(faq.id));
        if (matchedFaq) {
            handleOption(matchedFaq.id);
        } else {
            displayMessage(botFlow.default, 'bot');
        }
    }
}

// ==== WebSocket ====

socket.addEventListener('open', () => {
    console.log('Conectado ao servidor WebSocket');
});

socket.addEventListener('message', event => {
    const data = JSON.parse(event.data);

    if (data.type === 'message') {
        displayMessage(data.message, data.sender, data.userName);
    }

    if (data.type === 'support-request') {
        if (isSupport) {
            displayMessage(`🔔 Novo atendimento solicitado por ${data.userName}`, 'system');
        }
    }
});

socket.addEventListener('close', () => {
    console.log('Conexão WebSocket encerrada');
});

socket.addEventListener('error', error => {
    console.error('Erro no WebSocket', error);
});

function notifySupport() {
    if (socket.readyState === WebSocket.OPEN) {
        socket.send(JSON.stringify({
            type: 'support-request',
            chatId: chatId,
            userId: currentUserId,
            userName: currentUserName,
            message: 'Solicitação de atendimento ao suporte.'
        }));
    }
}

// ==== Enviar mensagem ====

function sendMessage() {
    const message = inputField.value.trim();
    if (!message) return;

    displayMessage(message, 'user', currentUserName);

    if (socket.readyState === WebSocket.OPEN) {
        socket.send(JSON.stringify({
            type: 'message',
            chatId: chatId,
            userId: currentUserId,
            userName: currentUserName,
            isSupport: isSupport,
            sender: 'user',
            message: message
        }));
    }

    processUserMessage(message);

    inputField.value = '';
}

// ==== Eventos UI ====

toggleButton.addEventListener('click', () => {
    const expanded = toggleButton.getAttribute('aria-expanded') === 'true';
    toggleButton.setAttribute('aria-expanded', String(!expanded));
    chatWindow.setAttribute('aria-hidden', String(expanded));
    chatWindow.classList.toggle('open');

    if (!expanded) {
        startChat();
    }
});

closeButton.addEventListener('click', () => {
    toggleButton.setAttribute('aria-expanded', 'false');
    chatWindow.setAttribute('aria-hidden', 'true');
    chatWindow.classList.remove('open');
});

sendButton.addEventListener('click', sendMessage);
inputField.addEventListener('keypress', e => {
    if (e.key === 'Enter') sendMessage();
});
