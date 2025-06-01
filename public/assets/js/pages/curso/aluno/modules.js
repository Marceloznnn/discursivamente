// JavaScript para a página de Módulos

document.addEventListener("DOMContentLoaded", function () {
  initModulesPage();
});

function initModulesPage() {
  // Melhorar a estrutura do HTML para aplicar os estilos avançados
  enhanceModuleCards();

  // Carregar dados de progresso do aluno
  loadStudentProgress();

  // Adicionar interatividade aos cards (clique fora do link leva ao primeiro material)
  addCardInteractivity();
}

function enhanceModuleCards() {
  const moduleCards = document.querySelectorAll(".module-card");

  moduleCards.forEach((card, index) => {
    const materialsList = card.querySelector(".materials-list");
    if (!materialsList) return;

    // Container para lista de materiais (caso queira estilos adicionais)
    const listContainer = document.createElement("div");
    listContainer.className = "materials-list-container";

    // Mover a lista para dentro do container
    card.appendChild(listContainer);
    listContainer.appendChild(materialsList);

    // Extra: você pode adicionar ícones ou contadores aqui, se quiser
    detectMaterialTypes(card);
  });
}

function detectMaterialTypes(moduleCard) {
  const materialItems = moduleCard.querySelectorAll(".materials-list li a");
  materialItems.forEach((item) => {
    const title = item.textContent.toLowerCase();
    if (
      title.includes("vídeo") ||
      title.includes("aula") ||
      title.includes("gravação")
    ) {
      item.classList.add("material-type-video");
    } else if (title.includes("áudio") || title.includes("podcast")) {
      item.classList.add("material-type-audio");
    } else if (
      title.includes("pdf") ||
      title.includes("documento") ||
      title.includes("apostila")
    ) {
      item.classList.add("material-type-document");
    } else if (
      title.includes("imagem") ||
      title.includes("infográfico") ||
      title.includes("foto")
    ) {
      item.classList.add("material-type-image");
    } else if (
      title.includes("quiz") ||
      title.includes("teste") ||
      title.includes("avaliação")
    ) {
      item.classList.add("material-type-quiz");
    }
  });
}

function loadStudentProgress() {
  const moduleCards = document.querySelectorAll(".module-card");

  moduleCards.forEach((card) => {
    const firstLink = card.querySelector(".materials-list li a");
    if (!firstLink) return;
    const href = firstLink.getAttribute("href");
    const urlParts = href.split("/");
    const courseId = urlParts[2];
    const moduleId = urlParts[4];
    const progressKey = `course-${courseId}-progress`;
    const progress = JSON.parse(localStorage.getItem(progressKey) || "{}");
    const moduleProgress = progress[moduleId] || {};

    const materialItems = card.querySelectorAll(".materials-list li a");
    let completedCount = 0;

    materialItems.forEach((item) => {
      const materialId = item.getAttribute("href").split("/").pop();
      if (moduleProgress[materialId] && moduleProgress[materialId].viewed) {
        completedCount++;
        item.parentElement.classList.add("completed");
        if (!item.querySelector(".material-completed-check")) {
          item.innerHTML +=
            '<span class="material-completed-check" style="margin-left: auto; color: var(--success-600);">✔</span>';
        }
      }
      // Marcar progresso ao clicar no material
      item.addEventListener("click", function () {
        markMaterialAsViewed(courseId, moduleId, materialId, card);
      });
    });

    // Atualiza barra de progresso, se existir
    updateModuleProgressBar(card, completedCount, materialItems.length);
  });
}

// Função para marcar material como visto e atualizar progresso
function markMaterialAsViewed(courseId, moduleId, materialId, card) {
  const progressKey = `course-${courseId}-progress`;
  const progress = JSON.parse(localStorage.getItem(progressKey) || "{}");
  if (!progress[moduleId]) progress[moduleId] = {};
  if (
    !progress[moduleId][materialId] ||
    !progress[moduleId][materialId].viewed
  ) {
    progress[moduleId][materialId] = {
      viewed: true,
      completedAt: new Date().toISOString(),
    };
    localStorage.setItem(progressKey, JSON.stringify(progress));
    // Atualiza visualmente
    const materialLinks = card.querySelectorAll(".materials-list li a");
    let completedCount = 0;
    materialLinks.forEach((item) => {
      const mid = item.getAttribute("href").split("/").pop();
      if (progress[moduleId][mid] && progress[moduleId][mid].viewed) {
        item.parentElement.classList.add("completed");
        if (!item.querySelector(".material-completed-check")) {
          item.innerHTML +=
            '<span class="material-completed-check" style="margin-left: auto; color: var(--success-600);">✔</span>';
        }
        completedCount++;
      }
    });
    updateModuleProgressBar(card, completedCount, materialLinks.length);
  }
}

// Atualiza barra de progresso e badge do módulo
function updateModuleProgressBar(card, completedCount, total) {
  const progressBar = card.querySelector(".progress-bar");
  const pct = total > 0 ? (completedCount / total) * 100 : 0;
  if (progressBar) {
    progressBar.style.width = `${pct}%`;
  }
  // Badge de status
  const moduleTitle = card.querySelector(".module-title");
  let statusBadge = card.querySelector(".module-status");
  if (!statusBadge) {
    statusBadge = document.createElement("span");
    statusBadge.className = "module-status";
    moduleTitle.appendChild(statusBadge);
  }
  if (pct === 100) {
    statusBadge.textContent = "Concluído";
    statusBadge.classList.add("status-completed");
    statusBadge.classList.remove("status-in-progress");
  } else if (pct > 0) {
    statusBadge.textContent = "Em andamento";
    statusBadge.classList.add("status-in-progress");
    statusBadge.classList.remove("status-completed");
  } else {
    statusBadge.textContent = "";
    statusBadge.classList.remove("status-in-progress", "status-completed");
  }
}

function addCardInteractivity() {
  const moduleCards = document.querySelectorAll(".module-card");

  moduleCards.forEach((card) => {
    card.style.cursor = "pointer";
    card.addEventListener("click", function (e) {
      // Se clicou em um <a> ou dentro dele, deixa o link agir normalmente
      const link = e.target.closest("a");
      if (link) {
        return;
      }
      // Senão, redireciona para o primeiro material disponível
      const firstLink = card.querySelector(".materials-list li a");
      if (firstLink) {
        window.location.href = firstLink.href;
      }
    });
  });
}
