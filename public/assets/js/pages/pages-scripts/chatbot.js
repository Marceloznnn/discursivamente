// ==== Seletores e criação dinâmica dos elementos ====
const chatbotContainer = document.querySelector(".chatbot-container");
const toggleButton = document.querySelector(".chatbot-toggle");
const chatWindow = document.getElementById("chatbot-window");
const messagesContainer = document.querySelector(".chatbot-messages");
let inputField = document.getElementById("chatbot-input");
let sendButton = document.getElementById("chatbot-send");

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

function startChat() {
  messagesContainer.innerHTML = "";
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
