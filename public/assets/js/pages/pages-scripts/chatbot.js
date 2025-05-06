document.addEventListener('DOMContentLoaded', function() {
    // Elementos do chatbot
    const chatbotToggle = document.querySelector('.chatbot-toggle');
    const chatbotWindow = document.getElementById('chatbot-window');
    const chatbotClose = document.querySelector('.chatbot-close');
    const messagesContainer = document.querySelector('.chatbot-messages');
    const inputField = document.querySelector('.chatbot-input input');
    const sendButton = document.querySelector('.chatbot-input button');

    // Mensagens automáticas do chatbot
    const botResponses = {
        welcome: "Olá! Bem-vindo(a) ao atendimento Discursivamente. Como posso ajudar você hoje?",
        default: "Obrigado pelo seu contato. Um membro da nossa equipe responderá em breve. Enquanto isso, você pode explorar nossos materiais sobre leitura acadêmica.",
        keywords: {
            "material": "Nossos materiais didáticos digitais (MDD) são desenvolvidos com base na Análise de Discurso Crítica. Você gostaria de saber mais sobre isso?",
            "leitura": "A leitura acadêmica é fundamental para o desenvolvimento do pensamento crítico. Nossa abordagem enfatiza a interpretação contextualizada dos textos acadêmicos.",
            "professor": "Somos parceiros de professores de Língua Portuguesa! Oferecemos recursos para o ensino de leitura acadêmica no Ensino Médio.",
            "aluno": "Para estudantes, disponibilizamos materiais interativos que facilitam a compreensão de gêneros acadêmicos.",
            "ensino médio": "Nosso foco inclui a preparação de alunos do Ensino Médio para a leitura acadêmica, fundamental para o sucesso no ensino superior.",
            "análise de discurso": "Trabalhamos com a metodologia de Análise de Discurso Crítica (Fairclough, 2003), que considera fatores sociais, culturais e políticos na interpretação textual.",
            "pesquisa": "Nossa pesquisa mais recente 'Educação e Cyberespaço: o discurso sobre (a #) leitura no tiktok' (2023-2024) fundamenta nossos materiais didáticos.",
            "contato": "Para mais informações, deixe seu e-mail que entraremos em contato. Você também pode agendar uma demonstração do nosso material didático digital."
        }
    };

    // Estado do chatbot
    let isChatbotOpen = false;

    // Função para exibir mensagem
    function displayMessage(message, sender) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message');
        messageElement.classList.add(sender === 'user' ? 'user-message' : 'bot-message');
        messageElement.textContent = message;
        messagesContainer.appendChild(messageElement);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Resposta do bot com base no conteúdo da mensagem
    function getBotResponse(message) {
        // Converte a mensagem para minúsculas para facilitar a comparação
        const lowerCaseMessage = message.toLowerCase();
        
        // Verifica palavras-chave na mensagem do usuário
        for (const keyword in botResponses.keywords) {
            if (lowerCaseMessage.includes(keyword.toLowerCase())) {
                return botResponses.keywords[keyword];
            }
        }
        
        // Se nenhuma palavra-chave for encontrada, retorna a resposta padrão
        return botResponses.default;
    }

    // Envia mensagem
    function sendMessage() {
        const message = inputField.value.trim();
        
        if (message) {
            // Exibe mensagem do usuário
            displayMessage(message, 'user');
            
            // Limpa o campo de input
            inputField.value = '';
            
            // Simula um pequeno atraso antes da resposta do bot
            setTimeout(() => {
                // Gera e exibe resposta do bot
                const botMessage = getBotResponse(message);
                displayMessage(botMessage, 'bot');
            }, 600);
        }
    }

    // Abre o chatbot
    function openChatbot() {
        chatbotWindow.setAttribute('aria-hidden', 'false');
        chatbotToggle.setAttribute('aria-expanded', 'true');
        isChatbotOpen = true;
        
        // Se for a primeira abertura, exibe mensagem de boas-vindas
        if (messagesContainer.children.length === 0) {
            displayMessage(botResponses.welcome, 'bot');
        }
    }

    // Fecha o chatbot
    function closeChatbot() {
        chatbotWindow.setAttribute('aria-hidden', 'true');
        chatbotToggle.setAttribute('aria-expanded', 'false');
        isChatbotOpen = false;
    }

    // Event listeners
    chatbotToggle.addEventListener('click', () => {
        if (isChatbotOpen) {
            closeChatbot();
        } else {
            openChatbot();
        }
    });

    chatbotClose.addEventListener('click', closeChatbot);

    sendButton.addEventListener('click', sendMessage);

    inputField.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Inicializa o chatbot fechado
    closeChatbot();
});