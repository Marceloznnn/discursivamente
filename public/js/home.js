// Dados de exemplo para livros
const livrosData = [
    {
      id: 1,
      titulo: "O Último Desejo",
      autor: "Andrzej Sapkowski",
      preco: "R$ 49,90",
      imagem: "https://via.placeholder.com/300x400?text=Livro+1"
    },
    {
      id: 2,
      titulo: "Duna",
      autor: "Frank Herbert",
      preco: "R$ 65,00",
      imagem: "https://via.placeholder.com/300x400?text=Livro+2"
    },
    {
      id: 3,
      titulo: "O Nome do Vento",
      autor: "Patrick Rothfuss",
      preco: "R$ 59,90",
      imagem: "https://via.placeholder.com/300x400?text=Livro+3"
    },
    {
      id: 4,
      titulo: "1984",
      autor: "George Orwell",
      preco: "R$ 45,00",
      imagem: "https://via.placeholder.com/300x400?text=Livro+4"
    },
    {
      id: 5,
      titulo: "O Hobbit",
      autor: "J.R.R. Tolkien",
      preco: "R$ 55,90",
      imagem: "https://via.placeholder.com/300x400?text=Livro+5"
    },
    {
      id: 6,
      titulo: "Cem Anos de Solidão",
      autor: "Gabriel García Márquez",
      preco: "R$ 62,90",
      imagem: "https://via.placeholder.com/300x400?text=Livro+6"
    },
    {
      id: 7,
      titulo: "A Revolução dos Bichos",
      autor: "George Orwell",
      preco: "R$ 39,90",
      imagem: "https://via.placeholder.com/300x400?text=Livro+7"
    },
    {
      id: 8,
      titulo: "Dom Casmurro",
      autor: "Machado de Assis",
      preco: "R$ 42,00",
      imagem: "https://via.placeholder.com/300x400?text=Livro+8"
    },
    {
      id: 9,
      titulo: "O Senhor dos Anéis",
      autor: "J.R.R. Tolkien",
      preco: "R$ 89,90",
      imagem: "https://via.placeholder.com/300x400?text=Livro+9"
    },
    {
      id: 10,
      titulo: "Crime e Castigo",
      autor: "Fiódor Dostoiévski",
      preco: "R$ 54,90",
      imagem: "https://via.placeholder.com/300x400?text=Livro+10"
    },
    {
      id: 11,
      titulo: "O Pequeno Príncipe",
      autor: "Antoine de Saint-Exupéry",
      preco: "R$ 35,90",
      imagem: "https://via.placeholder.com/300x400?text=Livro+11"
    },
    {
      id: 12,
      titulo: "Orgulho e Preconceito",
      autor: "Jane Austen",
      preco: "R$ 47,90",
      imagem: "https://via.placeholder.com/300x400?text=Livro+12"
    }
  ];
  
  // Dados de exemplo para feedback de usuários
  const feedbackData = [
    {
      id: 1,
      texto: "Os livros chegaram em perfeito estado e muito mais rápido do que eu esperava. A seleção de títulos é incrível!",
      nome: "Maria Silva",
      cargo: "Professora",
      avatar: "https://via.placeholder.com/100x100?text=Avatar+1"
    },
    {
      id: 2,
      texto: "Atendimento excelente! Sempre encontro obras raras que procuro há anos. Compro sempre nesta livraria.",
      nome: "João Pereira",
      cargo: "Advogado",
      avatar: "https://via.placeholder.com/100x100?text=Avatar+2"
    },
    {
      id: 3,
      texto: "As recomendações personalizadas acertaram em cheio meu gosto literário. A embalagem dos livros também é ótima, muito cuidadosa.",
      nome: "Ana Rodrigues",
      cargo: "Designer",
      avatar: "https://via.placeholder.com/100x100?text=Avatar+3"
    }
  ];
  
  // Variáveis de controle
  let currentPage = 0;
  const booksPerPage = 5;
  const totalPages = Math.ceil(livrosData.length / booksPerPage);
  
  // Função para renderizar os livros
  function renderLivros() {
    const livrosContainer = document.getElementById('livros-container');
    livrosContainer.innerHTML = '';
    
    const startIndex = currentPage * booksPerPage;
    const endIndex = startIndex + booksPerPage;
    const livrosAtuais = livrosData.slice(startIndex, endIndex);
    
    livrosAtuais.forEach(livro => {
      const livroCard = document.createElement('div');
      livroCard.className = 'livro-card';
      
      livroCard.innerHTML = `
        <img src="${livro.imagem}" alt="${livro.titulo}" class="livro-img">
        <div class="livro-info">
          <h3 class="livro-titulo">${livro.titulo}</h3>
          <p class="livro-autor">${livro.autor}</p>
          <p class="livro-preco">${livro.preco}</p>
        </div>
      `;
      
      livrosContainer.appendChild(livroCard);
    });
    
    atualizarPaginacao();
  }
  
  // Função para renderizar os feedbacks
  function renderFeedbacks() {
    const feedbackContainer = document.getElementById('feedback-container');
    
    feedbackData.forEach(feedback => {
      const feedbackCard = document.createElement('div');
      feedbackCard.className = 'feedback-card';
      
      feedbackCard.innerHTML = `
        <p class="feedback-texto">${feedback.texto}</p>
        <div class="feedback-usuario">
          <img src="${feedback.avatar}" alt="${feedback.nome}" class="feedback-avatar">
          <div class="feedback-info">
            <h4>${feedback.nome}</h4>
            <p>${feedback.cargo}</p>
          </div>
        </div>
      `;
      
      feedbackContainer.appendChild(feedbackCard);
    });
  }
  
  // Função para atualizar a paginação
  function atualizarPaginacao() {
    const paginationDots = document.getElementById('pagination-dots');
    paginationDots.innerHTML = '';
    
    for (let i = 0; i < totalPages; i++) {
      const dot = document.createElement('div');
      dot.className = `dot ${i === currentPage ? 'active' : ''}`;
      dot.dataset.page = i;
      
      dot.addEventListener('click', function() {
        currentPage = parseInt(this.dataset.page);
        renderLivros();
      });
      
      paginationDots.appendChild(dot);
    }
  }
  
  // Função para navegar para a página anterior
  function prevPage() {
    if (currentPage > 0) {
      currentPage--;
      renderLivros();
    }
  }
  
  // Função para navegar para a próxima página
  function nextPage() {
    if (currentPage < totalPages - 1) {
      currentPage++;
      renderLivros();
    }
  }
  
  // Inicialização quando o DOM estiver carregado
  document.addEventListener('DOMContentLoaded', function() {
    // Renderizar livros iniciais
    renderLivros();
    
    // Renderizar feedbacks
    renderFeedbacks();
    
    // Adicionar eventos aos botões de navegação
    document.getElementById('prev-button').addEventListener('click', prevPage);
    document.getElementById('next-button').addEventListener('click', nextPage);
  });
  