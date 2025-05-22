// JavaScript para a página de Módulos

document.addEventListener('DOMContentLoaded', function() {
  initModulesPage();
});

function initModulesPage() {
  // Melhorar a estrutura do HTML para aplicar os estilos avançados
  enhanceModuleCards();
  
  // Carregar dados de progresso do aluno
  loadStudentProgress();
  
  // Adicionar interatividade aos cards (clique fora do link leva ao primeiro material)
  addCardInteractivity();
  
  // Adicionar filtragem e ordenação
  setupFiltersAndSorting();
}

function enhanceModuleCards() {
  const moduleCards = document.querySelectorAll('.module-card');
  
  moduleCards.forEach((card, index) => {
    const materialsList = card.querySelector('.materials-list');
    if (!materialsList) return;
    
    // Container para lista de materiais (caso queira estilos adicionais)
    const listContainer = document.createElement('div');
    listContainer.className = 'materials-list-container';
    
    // Mover a lista para dentro do container
    card.appendChild(listContainer);
    listContainer.appendChild(materialsList);
    
    // Extra: você pode adicionar ícones ou contadores aqui, se quiser
    detectMaterialTypes(card);
  });
}

function detectMaterialTypes(moduleCard) {
  const materialItems = moduleCard.querySelectorAll('.materials-list li a');
  materialItems.forEach(item => {
    const title = item.textContent.toLowerCase();
    if (title.includes('vídeo') || title.includes('aula') || title.includes('gravação')) {
      item.classList.add('material-type-video');
    } else if (title.includes('áudio') || title.includes('podcast')) {
      item.classList.add('material-type-audio');
    } else if (title.includes('pdf') || title.includes('documento') || title.includes('apostila')) {
      item.classList.add('material-type-document');
    } else if (title.includes('imagem') || title.includes('infográfico') || title.includes('foto')) {
      item.classList.add('material-type-image');
    } else if (title.includes('quiz') || title.includes('teste') || title.includes('avaliação')) {
      item.classList.add('material-type-quiz');
    }
  });
}

function loadStudentProgress() {
  const moduleCards = document.querySelectorAll('.module-card');
  
  moduleCards.forEach(card => {
    const firstLink = card.querySelector('.materials-list li a');
    if (!firstLink) return;
    const href = firstLink.getAttribute('href');
    const urlParts = href.split('/');
    const courseId = urlParts[2];
    const moduleId = urlParts[4];
    const progressKey = `course-${courseId}-progress`;
    const progress = JSON.parse(localStorage.getItem(progressKey) || '{}');
    const moduleProgress = progress[moduleId] || {};
    
    const materialItems = card.querySelectorAll('.materials-list li a');
    let completedCount = 0;
    
    materialItems.forEach(item => {
      const materialId = item.getAttribute('href').split('/').pop();
      if (moduleProgress[materialId] && moduleProgress[materialId].viewed) {
        completedCount++;
        item.parentElement.classList.add('completed');
        item.innerHTML += '<span style="margin-left: auto; color: var(--success-600);">✔</span>';
      }
    });
    
    // Atualiza barra de progresso, se existir
    const progressBar = card.querySelector('.progress-bar');
    if (progressBar && materialItems.length > 0) {
      const pct = (completedCount / materialItems.length) * 100;
      progressBar.style.width = `${pct}%`;
      
      // Badge de status (opcional)
      const moduleTitle = card.querySelector('.module-title');
      const statusBadge = document.createElement('span');
      statusBadge.className = 'module-status';
      if (pct === 100) {
        statusBadge.textContent = 'Concluído';
        statusBadge.classList.add('status-completed');
      } else if (pct > 0) {
        statusBadge.textContent = 'Em andamento';
        statusBadge.classList.add('status-in-progress');
      }
      if (pct > 0) {
        moduleTitle.appendChild(statusBadge);
      }
    }
  });
}

function addCardInteractivity() {
  const moduleCards = document.querySelectorAll('.module-card');
  
  moduleCards.forEach(card => {
    card.style.cursor = 'pointer';
    card.addEventListener('click', function(e) {
      // Se clicou em um <a> ou dentro dele, deixa o link agir normalmente
      const link = e.target.closest('a');
      if (link) {
        return;
      }
      // Senão, redireciona para o primeiro material disponível
      const firstLink = card.querySelector('.materials-list li a');
      if (firstLink) {
        window.location.href = firstLink.href;
      }
    });
  });
}

function setupFiltersAndSorting() {
  const modules = document.querySelectorAll('.module-card');
  if (modules.length <= 1) return;
  
  const pageHeading = document.querySelector('.page-heading');
  if (!pageHeading) return;
  
  const controls = document.createElement('div');
  controls.className = 'modules-controls';
  controls.innerHTML = `
    <div style="display:flex; justify-content:space-between; margin-bottom:1.5rem; gap:1rem; flex-wrap:wrap;">
      <div>
        <label for="modules-filter" style="font-weight:500; margin-right:0.5rem;">Filtrar:</label>
        <select id="modules-filter" style="padding:0.5rem; border-radius:var(--radius-sm);">
          <option value="all">Todos</option>
          <option value="in-progress">Em andamento</option>
          <option value="completed">Concluídos</option>
          <option value="not-started">Não iniciados</option>
        </select>
      </div>
      <div>
        <label for="modules-sort" style="font-weight:500; margin-right:0.5rem;">Ordenar:</label>
        <select id="modules-sort" style="padding:0.5rem; border-radius:var(--radius-sm);">
          <option value="default">Padrão</option>
          <option value="title-asc">Título A–Z</option>
          <option value="title-desc">Título Z–A</option>
          <option value="progress">Progresso</option>
        </select>
      </div>
    </div>
  `;
  pageHeading.insertAdjacentElement('afterend', controls);
  
  document.getElementById('modules-filter').addEventListener('change', applyFiltersAndSort);
  document.getElementById('modules-sort').addEventListener('change', applyFiltersAndSort);
}

function applyFiltersAndSort() {
  const modules = Array.from(document.querySelectorAll('.module-card')).map(el => {
    const title = el.querySelector('.module-title').textContent.trim();
    const progressBar = el.querySelector('.progress-bar');
    const progress = progressBar ? parseFloat(progressBar.style.width) : 0;
    return { el, title, progress };
  });
  
  const filter = document.getElementById('modules-filter').value;
  const sort = document.getElementById('modules-sort').value;
  
  let filtered = modules;
  if (filter === 'in-progress') {
    filtered = modules.filter(m => m.progress > 0 && m.progress < 100);
  } else if (filter === 'completed') {
    filtered = modules.filter(m => m.progress === 100);
  } else if (filter === 'not-started') {
    filtered = modules.filter(m => m.progress === 0);
  }
  
  if (sort === 'title-asc') {
    filtered.sort((a, b) => a.title.localeCompare(b.title));
  } else if (sort === 'title-desc') {
    filtered.sort((a, b) => b.title.localeCompare(a.title));
  } else if (sort === 'progress') {
    filtered.sort((a, b) => b.progress - a.progress);
  }
  
  const grid = document.querySelector('.modules-grid');
  grid.innerHTML = '';
  
  if (filtered.length === 0) {
    const p = document.createElement('p');
    p.className = 'empty-message';
    p.textContent = 'Nenhum módulo encontrado com os filtros aplicados.';
    grid.appendChild(p);
  } else {
    filtered.forEach(item => grid.appendChild(item.el));
  }
}
