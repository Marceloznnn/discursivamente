// Dados de exemplo para categorias
const categories = [
  { id: 1, name: 'Desenvolvimento', icon: 'fa-code' },
  { id: 2, name: 'Design', icon: 'fa-paint-brush' },
  { id: 3, name: 'Marketing', icon: 'fa-bullhorn' },
  { id: 4, name: 'Negócios', icon: 'fa-briefcase' },
  { id: 5, name: 'Fotografia', icon: 'fa-camera' },
  { id: 6, name: 'Música', icon: 'fa-music' },
  { id: 7, name: 'Saúde e Bem-estar', icon: 'fa-heartbeat' },
  { id: 8, name: 'Idiomas', icon: 'fa-language' },
  { id: 9, name: 'Ciência de Dados', icon: 'fa-chart-bar' },
  { id: 10, name: 'Finanças', icon: 'fa-money-bill' }
];

// Dados de exemplo para cursos
const courses = [
  { id: 1, title: 'HTML & CSS para Iniciantes', status: 'ativo', price: 0, members: 1250, categoryId: 1 },
  { id: 2, title: 'JavaScript Avançado', status: 'ativo', price: 79.90, members: 850, categoryId: 1 },
  { id: 3, title: 'Design de Interface UX/UI', status: 'ativo', price: 129.90, members: 720, categoryId: 2 },
  { id: 4, title: 'Marketing Digital Completo', status: 'ativo', price: 149.90, members: 980, categoryId: 3 },
  { id: 5, title: 'Photoshop do Zero ao Avançado', status: 'ativo', price: 89.90, members: 540, categoryId: 2 },
  { id: 6, title: 'Criação de Logo e Identidade Visual', status: 'pendente', price: 99.90, members: 320, categoryId: 2 },
  { id: 7, title: 'React JS para Iniciantes', status: 'ativo', price: 0, members: 2100, categoryId: 1 },
  { id: 8, title: 'SEO e Marketing de Conteúdo', status: 'ativo', price: 119.90, members: 450, categoryId: 3 },
  { id: 9, title: 'Gestão de Negócios', status: 'ativo', price: 189.90, members: 380, categoryId: 4 },
  { id: 10, title: 'Fotografia para Iniciantes', status: 'ativo', price: 69.90, members: 290, categoryId: 5 },
  { id: 11, title: 'Produção Musical', status: 'ativo', price: 149.90, members: 210, categoryId: 6 },
  { id: 12, title: 'Yoga para Iniciantes', status: 'ativo', price: 59.90, members: 670, categoryId: 7 },
  { id: 13, title: 'Inglês para Negócios', status: 'ativo', price: 129.90, members: 890, categoryId: 8 },
  { id: 14, title: 'Python para Análise de Dados', status: 'ativo', price: 139.90, members: 760, categoryId: 9 },
  { id: 15, title: 'Investimentos para Iniciantes', status: 'ativo', price: 99.90, members: 520, categoryId: 10 },
  { id: 16, title: 'Node.js Completo', status: 'ativo', price: 159.90, members: 430, categoryId: 1 },
  { id: 17, title: 'Adobe Illustrator Master', status: 'ativo', price: 119.90, members: 350, categoryId: 2 },
  { id: 18, title: 'E-mail Marketing', status: 'pendente', price: 79.90, members: 280, categoryId: 3 },
  { id: 19, title: 'Empreendedorismo Digital', status: 'ativo', price: 0, members: 1100, categoryId: 4 },
  { id: 20, title: 'Fotografia de Produto', status: 'ativo', price: 89.90, members: 190, categoryId: 5 },
  { id: 21, title: 'Teoria Musical', status: 'ativo', price: 69.90, members: 230, categoryId: 6 },
  { id: 22, title: 'Meditação e Mindfulness', status: 'ativo', price: 49.90, members: 850, categoryId: 7 },
  { id: 23, title: 'Espanhol para Viagens', status: 'ativo', price: 79.90, members: 410, categoryId: 8 },
  { id: 24, title: 'Machine Learning Básico', status: 'ativo', price: 189.90, members: 320, categoryId: 9 },
  { id: 25, title: 'Educação Financeira', status: 'ativo', price: 0, members: 1450, categoryId: 10 },
  { id: 26, title: 'Vue.js do Zero ao Avançado', status: 'ativo', price: 149.90, members: 380, categoryId: 1 },
  { id: 27, title: 'Design Thinking', status: 'ativo', price: 109.90, members: 420, categoryId: 2 },
  { id: 28, title: 'Google Ads Avançado', status: 'ativo', price: 159.90, members: 290, categoryId: 3 },
  { id: 29, title: 'Gestão de Projetos', status: 'pendente', price: 179.90, members: 240, categoryId: 4 },
  { id: 30, title: 'Fotografia de Paisagem', status: 'ativo', price: 79.90, members: 310, categoryId: 5 }
];

// Configuração da paginação
const coursesPerPage = 12; // 4 colunas x 3 linhas
let currentPage = 1;
let currentCategory = 'all'; // 'all' para mostrar todos os cursos

// Formatar preço
function formatPrice(price) {
  if (price === 0) {
    return 'Gratuito';
  }
  return `R$ ${price.toFixed(2).replace('.', ',')}`;
}

// Inicializar a página
document.addEventListener('DOMContentLoaded', function() {
  renderCategories();
  filterAndRenderCourses();
});

// Renderizar categorias
function renderCategories() {
  const categoriesContainer = document.getElementById('categories-scroll');
  
  // Adicionar categoria "Todos"
  const allCategory = document.createElement('div');
  allCategory.className = 'category-item active';
  allCategory.setAttribute('data-category', 'all');
  allCategory.innerHTML = `
    <i class="fas fa-th"></i>
    <span>Todos</span>
  `;
  allCategory.addEventListener('click', function() {
    currentCategory = 'all';
    setActiveCategory(this);
    filterAndRenderCourses();
  });
  categoriesContainer.appendChild(allCategory);
  
  // Adicionar as demais categorias
  categories.forEach(category => {
    const categoryElement = document.createElement('div');
    categoryElement.className = 'category-item';
    categoryElement.setAttribute('data-category', category.id);
    categoryElement.innerHTML = `
      <i class="fas ${category.icon}"></i>
      <span>${category.name}</span>
    `;
    categoryElement.addEventListener('click', function() {
      currentCategory = category.id.toString();
      setActiveCategory(this);
      filterAndRenderCourses();
    });
    categoriesContainer.appendChild(categoryElement);
  });
}

// Marcar categoria ativa
function setActiveCategory(element) {
  document.querySelectorAll('.category-item').forEach(item => {
    item.classList.remove('active');
  });
  element.classList.add('active');
}

// Filtrar e renderizar cursos com base na categoria e paginação
function filterAndRenderCourses() {
  let filteredCourses = courses;
  
  // Filtrar por categoria se não for "Todos"
  if (currentCategory !== 'all') {
    const categoryId = parseInt(currentCategory);
    filteredCourses = courses.filter(course => course.categoryId === categoryId);
  }
  
  // Calcular total de páginas
  const totalPages = Math.ceil(filteredCourses.length / coursesPerPage);
  
  // Ajustar página atual se necessário
  if (currentPage > totalPages) {
    currentPage = totalPages > 0 ? totalPages : 1;
  }
  
  // Calcular índices de início e fim para a página atual
  const startIndex = (currentPage - 1) * coursesPerPage;
  const endIndex = Math.min(startIndex + coursesPerPage, filteredCourses.length);
  
  // Obter cursos da página atual
  const currentCourses = filteredCourses.slice(startIndex, endIndex);
  
  // Renderizar cursos
  renderCourses(currentCourses);
  
  // Renderizar paginação
  renderPagination(totalPages);
}

// Renderizar lista de cursos
function renderCourses(currentCourses) {
  const coursesGrid = document.getElementById('courses-grid');
  coursesGrid.innerHTML = '';
  
  if (currentCourses.length === 0) {
    const noCourses = document.createElement('p');
    noCourses.className = 'no-courses';
    noCourses.textContent = 'Não há cursos disponíveis nesta categoria.';
    coursesGrid.appendChild(noCourses);
    return;
  }
  
  currentCourses.forEach(course => {
    const courseElement = document.createElement('div');
    courseElement.className = 'card';
    courseElement.innerHTML = `
      <div class="card-header">
        <h3>${course.title}</h3>
        <span class="status ${course.status}">${course.status.charAt(0).toUpperCase() + course.status.slice(1)}</span>
      </div>
      <p class="price">${formatPrice(course.price)}</p>
      <p class="members">Membros: ${course.members}</p>
      <a href="/curso/${course.id}" class="btn-detail">Ver Detalhes</a>
    `;
    coursesGrid.appendChild(courseElement);
  });
}

// Renderizar botões de paginação
function renderPagination(totalPages) {
  const paginationContainer = document.getElementById('pagination');
  paginationContainer.innerHTML = '';
  
  if (totalPages <= 1) {
    return; // Não mostrar paginação se houver apenas uma página
  }
  
  // Botão "Anterior"
  if (currentPage > 1) {
    const prevButton = createPaginationButton('Anterior', currentPage - 1);
    paginationContainer.appendChild(prevButton);
  }
  
  // Botões numéricos
  const maxVisibleButtons = 5; // Número máximo de botões numéricos visíveis
  let startPage = Math.max(1, currentPage - Math.floor(maxVisibleButtons / 2));
  let endPage = Math.min(totalPages, startPage + maxVisibleButtons - 1);
  
  // Ajustar startPage se necessário
  if (endPage - startPage + 1 < maxVisibleButtons) {
    startPage = Math.max(1, endPage - maxVisibleButtons + 1);
  }
  
  // Adicionar botão para a página 1 se necessário
  if (startPage > 1) {
    const firstPageButton = createPaginationButton('1', 1);
    paginationContainer.appendChild(firstPageButton);
    
    if (startPage > 2) {
      const ellipsis = document.createElement('span');
      ellipsis.className = 'pagination-ellipsis';
      ellipsis.textContent = '...';
      paginationContainer.appendChild(ellipsis);
    }
  }
  
  // Adicionar botões numéricos
  for (let i = startPage; i <= endPage; i++) {
    const pageButton = createPaginationButton(i.toString(), i);
    if (i === currentPage) {
      pageButton.classList.add('active');
    }
    paginationContainer.appendChild(pageButton);
  }
  
  // Adicionar botão para a última página se necessário
  if (endPage < totalPages) {
    if (endPage < totalPages - 1) {
      const ellipsis = document.createElement('span');
      ellipsis.className = 'pagination-ellipsis';
      ellipsis.textContent = '...';
      paginationContainer.appendChild(ellipsis);
    }
    
    const lastPageButton = createPaginationButton(totalPages.toString(), totalPages);
    paginationContainer.appendChild(lastPageButton);
  }
  
  // Botão "Próximo"
  if (currentPage < totalPages) {
    const nextButton = createPaginationButton('Próximo', currentPage + 1);
    paginationContainer.appendChild(nextButton);
  }
}

// Criar botão de paginação
function createPaginationButton(text, page) {
  const button = document.createElement('button');
  button.className = 'pagination-btn';
  button.textContent = text;
  button.addEventListener('click', function() {
    currentPage = page;
    filterAndRenderCourses();
    // Rolar para o topo da seção de cursos
    document.querySelector('.courses-section').scrollIntoView({ behavior: 'smooth' });
  });
  return button;
}
