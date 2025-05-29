// ==== Seletores e criação dinâmica dos elementos ====
const chatbotContainer = document.querySelector(".chatbot-container");
const toggleButton = document.querySelector(".chatbot-toggle");
const chatWindow = document.getElementById("chatbot-window");
const messagesContainer = document.querySelector(".chatbot-messages");
let inputField = document.getElementById("chatbot-input");
let sendButton = document.getElementById("chatbot-send");
const notificationBadge = document.getElementById("chatbot-notification-badge");
let unreadAdminMessages = 0;

// Cria input e botão de envio se não existirem
function ensureInputElements() {
  let inputDiv = chatWindow.querySelector(".chatbot-input");
  if (!inputDiv) {
    inputDiv = document.createElement("div");
    inputDiv.className = "chatbot-input";
    chatWindow.appendChild(inputDiv);
  }
  if (!inputField) {
    inputField = document.createElement("input");
    inputField.type = "text";
    inputField.id = "chatbot-input";
    inputField.placeholder = "Digite sua mensagem...";
    inputDiv.appendChild(inputField);
  }
  if (!sendButton) {
    sendButton = document.createElement("button");
    sendButton.id = "chatbot-send";
    sendButton.type = "button";
    sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
    inputDiv.appendChild(sendButton);
  }
}

// Cria botão de fechar se não existir
function ensureCloseButton() {
  let header = chatWindow.querySelector(".chatbot-header");
  if (!header) {
    header = document.createElement("div");
    header.className = "chatbot-header";
    chatWindow.insertBefore(header, messagesContainer);
  }
  let closeButton = chatWindow.querySelector(".chatbot-close");
  if (!closeButton) {
    closeButton = document.createElement("button");
    closeButton.className = "chatbot-close";
    closeButton.type = "button";
    closeButton.setAttribute("aria-label", "Fechar chat");
    closeButton.innerHTML = '<i class="fas fa-times"></i>';
    header.appendChild(closeButton);
    closeButton.addEventListener("click", () => {
      toggleButton.setAttribute("aria-expanded", "false");
      chatWindow.setAttribute("aria-hidden", "true");
      chatWindow.classList.remove("open");
    });
  }
}

ensureInputElements();
ensureCloseButton();

// ==== Variáveis globais e fluxo do bot ====
const socket = new WebSocket("ws://localhost:8081");
const chatId =
  window.chatId ||
  (window.currentUserId ? `support_user_${window.currentUserId}` : Date.now());
const currentUserId = window.currentUserId || "user";
const currentUserName = window.currentUserName || "Você";
const isSupport = window.isSupport || false;

const botFlow = {
  welcome:
    "👋 Olá! Bem-vindo(a) ao Atendimento Discursivamente. Como posso te ajudar? Escolha uma opção abaixo:",
  options: [
    { id: "talk-to-support", label: "🗨️ Falar com o Atendente" },
    { id: "common-questions", label: "❓ Dúvidas Comuns" },
  ],
  faqs: [
    {
      id: "material",
      question: "📚 O que são os Materiais Didáticos Digitais?",
      answer:
        "Nossos materiais são baseados na Análise de Discurso Crítica. Ajudam na leitura acadêmica no Ensino Médio.",
    },
    {
      id: "leitura",
      question: "📖 Como funciona a leitura acadêmica?",
      answer:
        "A leitura acadêmica desenvolve o pensamento crítico, com foco na interpretação contextualizada dos textos.",
    },
    {
      id: "suporte",
      question: "⚙️ Como falar com o suporte?",
      answer:
        'Clique em "Falar com o Atendente" e um atendente será notificado.',
    },
  ],
  default: "❗ Não entendi sua solicitação. Por favor, selecione uma opção.",
};

function displayMessage(message, sender = "bot", userName = "") {
  const messageElement = document.createElement("div");
  messageElement.classList.add("chatbot-message");
  if (sender === "user") {
    messageElement.classList.add("chatbot-message--user");
    messageElement.innerHTML = `<span>${
      userName || "Você"
    }:</span> <span>${message}</span>`;
  } else if (sender === "bot") {
    messageElement.classList.add("chatbot-message--bot");
    messageElement.innerHTML = `<span>${message}</span>`;
  } else if (sender === "system") {
    messageElement.classList.add("chatbot-message--system");
    messageElement.innerHTML = `<em>${message}</em>`;
  } else {
    messageElement.innerHTML = `<span>${
      userName || sender
    }:</span> <span>${message}</span>`;
  }
  messagesContainer.appendChild(messageElement);
  messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function displayOptions(options) {
  // Só exibe opções se usuário estiver logado
  if (window.currentUserId <= 0) return;
  const wrapper = document.createElement("div");
  wrapper.classList.add("chatbot-message", "chatbot-message--bot");
  options.forEach((opt) => {
    const btn = document.createElement("button");
    btn.className = "chatbot-option-btn";
    btn.textContent = opt.label;
    btn.onclick = () => handleOption(opt.id);
    wrapper.appendChild(btn);
  });
  messagesContainer.appendChild(wrapper);
  messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function displayFaqs() {
  botFlow.faqs.forEach((faq) => {
    displayMessage(`<strong>${faq.question}</strong><br>${faq.answer}`, "bot");
  });
  // Só mostra opção de atendimento se logado
  if (window.currentUserId > 0) {
    displayOptions([
      { id: "talk-to-support", label: "🗨️ Falar com o Atendente" },
    ]);
  }
}

function renderSupportHistory() {
  if (!window.supportMessages || !Array.isArray(window.supportMessages)) return;
  window.supportMessages.forEach((msg) => {
    if (msg.sender === "admin") {
      displayMessage(msg.message, "admin", msg.user_name || "Atendente");
    } else if (msg.sender === "user") {
      displayMessage(msg.message, "user", currentUserName);
    } else if (msg.sender === "system") {
      displayMessage(msg.message, "system");
    } else {
      displayMessage(msg.message, msg.sender, msg.user_name || msg.sender);
    }
  });
}

function startChat() {
  messagesContainer.innerHTML = "";
  renderSupportHistory();
  displayMessage(botFlow.welcome, "bot");
  displayOptions(botFlow.options);
}

function handleOption(optionId) {
  if (optionId === "talk-to-support") {
    if (window.currentUserId <= 0) {
      displayMessage(
        "⚠️ Você precisa estar logado para solicitar atendimento.",
        "system"
      );
      return;
    }
    displayMessage("🔔 Solicitando atendimento humano...", "system");
    notifySupport();
    displayMessage("Aguarde, um atendente será notificado.", "bot");
  } else if (optionId === "common-questions") {
    displayFaqs();
  } else {
    displayMessage(botFlow.default, "bot");
    displayOptions(botFlow.options);
  }
}

function processUserMessage(message) {
  const msgLower = message.toLowerCase();
  if (msgLower.includes("falar com atendente")) {
    handleOption("talk-to-support");
  } else if (msgLower.includes("duvida") || msgLower.includes("dúvida")) {
    handleOption("common-questions");
  } else {
    displayMessage(botFlow.default, "bot");
    displayOptions(botFlow.options);
  }
}

function updateNotificationBadge() {
  if (unreadAdminMessages > 0) {
    notificationBadge.textContent = unreadAdminMessages;
    notificationBadge.style.display = "inline-block";
  } else {
    notificationBadge.style.display = "none";
  }
}

// ==== WebSocket ====
socket.addEventListener("open", () => {
  socket.send(
    JSON.stringify({
      type: "join",
      chatId: chatId,
      userId: currentUserId,
      isSupport: false,
    })
  );
});

socket.addEventListener("message", (event) => {
  const data = JSON.parse(event.data);
  if (data.type === "message" && data.chatId === chatId) {
    if (data.isSupport) {
      displayMessage(data.message, "admin", data.userName || "Atendente");
      // Se o chat está fechado, contar como não lida
      if (!chatWindow.classList.contains("open")) {
        unreadAdminMessages++;
        updateNotificationBadge();
      }
    } else {
      displayMessage(data.message, "user", data.userName || "Você");
    }
  }
  if (data.type === "support-request") {
    if (isSupport) {
      displayMessage(
        `🔔 Novo atendimento solicitado por ${data.userName}`,
        "system"
      );
    }
  }
});

socket.addEventListener("close", () => {
  console.log("Conexão WebSocket encerrada");
});

socket.addEventListener("error", (error) => {
  console.error("Erro no WebSocket", error);
});

function notifySupport() {
  if (window.currentUserId <= 0) return;
  if (socket.readyState === WebSocket.OPEN) {
    socket.send(
      JSON.stringify({
        type: "support-request",
        chatId: chatId,
        userId: currentUserId,
        userName: currentUserName,
        message: "Solicitação de atendimento ao suporte.",
      })
    );
  }
}

// ==== Enviar mensagem ====
function sendMessage() {
  if (window.currentUserId <= 0) {
    displayMessage(
      "⚠️ Você precisa estar logado para enviar mensagens ao suporte.",
      "system"
    );
    inputField.value = "";
    return;
  }
  const message = inputField.value.trim();
  if (!message) return;
  displayMessage(message, "user", currentUserName);
  if (socket.readyState === WebSocket.OPEN) {
    socket.send(
      JSON.stringify({
        type: "message",
        chatId: chatId,
        userId: currentUserId,
        userName: currentUserName,
        isSupport: isSupport,
        sender: "user",
        message: message,
      })
    );
  }
  processUserMessage(message);
  inputField.value = "";
}

// Desabilita input e botão se visitante
if (window.currentUserId <= 0) {
  inputField.disabled = true;
  sendButton.disabled = true;
  inputField.placeholder = "Faça login para enviar mensagens";
  sendButton.title = "Apenas usuários logados podem enviar mensagens";
}

// Adiciona overlay para facilitar o clique fora
let overlay = document.getElementById("chatbot-overlay");
if (!overlay) {
  overlay = document.createElement("div");
  overlay.id = "chatbot-overlay";
  overlay.style.position = "fixed";
  overlay.style.top = 0;
  overlay.style.left = 0;
  overlay.style.width = "100vw";
  overlay.style.height = "100vh";
  overlay.style.background = "rgba(0,0,0,0.1)";
  overlay.style.zIndex = 999;
  overlay.style.display = "none";
  document.body.appendChild(overlay);
}

function openChat() {
  overlay.style.display = "block";
  chatWindow.classList.add("open");
  chatWindow.setAttribute("aria-hidden", "false");
  toggleButton.setAttribute("aria-expanded", "true");
  startChat();
  // Zera notificações ao abrir
  unreadAdminMessages = 0;
  updateNotificationBadge();
}

function closeChat() {
  overlay.style.display = "none";
  chatWindow.classList.remove("open");
  chatWindow.setAttribute("aria-hidden", "true");
  toggleButton.setAttribute("aria-expanded", "false");
}

toggleButton.addEventListener("click", () => {
  if (chatWindow.classList.contains("open")) {
    closeChat();
  } else {
    openChat();
    // Zera notificações ao abrir pelo balão
    unreadAdminMessages = 0;
    updateNotificationBadge();
  }
});

// Botão X (fechar)
const closeBtn = chatWindow.querySelector(".chatbot-close");
if (closeBtn) {
  closeBtn.addEventListener("click", closeChat);
}

// Fechar ao clicar no overlay
overlay.addEventListener("click", closeChat);

// Remove listeners antigos duplicados
chatWindow.onclick = null;
chatbotContainer.onclick = null;

// ==== Eventos UI ====
chatWindow.addEventListener("click", function (e) {
  // Impede propagação para não fechar ao clicar dentro
  e.stopPropagation();
});
chatbotContainer.addEventListener("click", function (e) {
  // Fecha se clicar fora do chatWindow e o chat estiver aberto
  if (
    chatWindow.classList.contains("open") &&
    !chatWindow.contains(e.target) &&
    !toggleButton.contains(e.target)
  ) {
    toggleButton.setAttribute("aria-expanded", "false");
    chatWindow.setAttribute("aria-hidden", "true");
    chatWindow.classList.remove("open");
  }
});

sendButton.addEventListener("click", sendMessage);
inputField.addEventListener("keypress", (e) => {
  if (e.key === "Enter") sendMessage();
});

// ==== Responsividade aprimorada do Chatbot ====
function adjustChatbotWindowHeight() {
  if (!chatWindow) return;
  const vh = Math.max(
    document.documentElement.clientHeight || 0,
    window.innerHeight || 0
  );
  if (window.innerWidth <= 400) {
    chatWindow.style.height = Math.round(vh * 0.92) + "px";
    chatWindow.style.width = "100vw";
    chatWindow.style.left = "0";
    chatWindow.style.right = "0";
    chatWindow.style.maxWidth = "100vw";
    chatWindow.style.borderRadius = "0";
  } else if (window.innerWidth <= 600) {
    chatWindow.style.height = Math.round(vh * 0.85) + "px";
    chatWindow.style.width = "98vw";
    chatWindow.style.left = "1vw";
    chatWindow.style.right = "1vw";
    chatWindow.style.maxWidth = "100vw";
    chatWindow.style.borderRadius = "18px";
  } else if (window.innerWidth <= 900) {
    chatWindow.style.height = Math.round(vh * 0.7) + "px";
    chatWindow.style.width = "400px";
    chatWindow.style.left = "";
    chatWindow.style.right = "0";
    chatWindow.style.maxWidth = "95vw";
    chatWindow.style.borderRadius = "18px";
  } else {
    chatWindow.style.height = "520px";
    chatWindow.style.width = "370px";
    chatWindow.style.left = "";
    chatWindow.style.right = "0";
    chatWindow.style.maxWidth = "95vw";
    chatWindow.style.borderRadius = "18px";
  }
}
window.addEventListener("resize", adjustChatbotWindowHeight);
window.addEventListener("orientationchange", adjustChatbotWindowHeight);
adjustChatbotWindowHeight();

// Acessibilidade: foco automático no input ao abrir o chat
function focusInputOnOpen() {
  if (chatWindow.classList.contains("open") && inputField) {
    setTimeout(() => inputField.focus(), 200);
  }
}
chatWindow.addEventListener("transitionend", focusInputOnOpen);
toggleButton.addEventListener("click", focusInputOnOpen);

// Acessibilidade: fechar chat com ESC
window.addEventListener("keydown", function (e) {
  if (e.key === "Escape" && chatWindow.classList.contains("open")) {
    closeChat();
    toggleButton.focus();
  }
});

// Melhoria: rolar para a última mensagem ao abrir
function scrollToLastMessage() {
  if (messagesContainer) {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }
}
chatWindow.addEventListener("transitionend", scrollToLastMessage);

// Melhoria: placeholder dinâmico para input
function updateInputPlaceholder() {
  if (window.currentUserId <= 0) {
    inputField.placeholder = "Faça login para enviar mensagens";
  } else {
    inputField.placeholder = "Digite sua mensagem...";
  }
}
updateInputPlaceholder();
