const adminSocket = new WebSocket("ws://localhost:8081");
const chatId = window.chatId;
const adminId = window.currentUserId;
const adminName = window.currentUserName;

const messagesContainer = document.querySelector(".chat-messages");
const inputField = document.querySelector(".chat-reply-form textarea");
const sendButton = document.querySelector(
  '.chat-reply-form button[type="submit"]'
);

adminSocket.addEventListener("open", () => {
  adminSocket.send(
    JSON.stringify({
      type: "join",
      chatId: chatId,
      userId: adminId,
      isSupport: true,
    })
  );
});

adminSocket.addEventListener("message", (event) => {
  const data = JSON.parse(event.data);
  if (data.type === "message" && data.chatId === chatId) {
    displayAdminMessage(data);
  }
  if (data.type === "support-request") {
    // Notificação de novo atendimento (opcional)
    alert("Novo usuário solicitou atendimento: " + data.userName);
  }
});

function displayAdminMessage(data) {
  const div = document.createElement("div");
  div.className = "chat-message " + (data.isSupport ? "admin" : "user");
  div.innerHTML = `<strong>${
    data.isSupport ? "Admin" : data.userName
  }:</strong> <span>${data.message}</span> <small>${new Date(
    data.timestamp
  ).toLocaleString()}</small>`;
  messagesContainer.appendChild(div);
  messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

sendButton.addEventListener("click", function (e) {
  e.preventDefault();
  const message = inputField.value.trim();
  if (!message) return;
  adminSocket.send(
    JSON.stringify({
      type: "message",
      chatId: chatId,
      userId: adminId,
      userName: adminName,
      isSupport: true,
      message: message,
    })
  );
  inputField.value = "";
});
