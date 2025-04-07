document.addEventListener('DOMContentLoaded', function() {
    // Configuração do carrossel de banners
    const bannerContainer = document.querySelector('.banner-container');
    const banners = document.querySelectorAll('.banner');
    const prevBtn = document.querySelector('.banner-nav.prev');
    const nextBtn = document.querySelector('.banner-nav.next');
    const indicators = document.querySelectorAll('.banner-indicators .indicator');
    let currentBannerIndex = 0;
    let bannerInterval;
    const bannerAutoplayTime = 8000; // 8 segundos
  
    // Função para mostrar um banner específico
    function showBanner(index) {
      // Remover classe 'active' de todos os banners e indicadores
      banners.forEach(banner => banner.classList.remove('active'));
      indicators.forEach(indicator => indicator.classList.remove('active'));
      
      // Adicionar classe 'active' ao banner atual e indicador correspondente
      banners[index].classList.add('active');
      indicators[index].classList.add('active');
      
      // Atualizar índice atual
      currentBannerIndex = index;
    }
  
    // Função para ir para o próximo banner
    function nextBanner() {
      let nextIndex = currentBannerIndex + 1;
      if (nextIndex >= banners.length) {
        nextIndex = 0;
      }
      showBanner(nextIndex);
    }
  
    // Função para ir para o banner anterior
    function prevBanner() {
      let prevIndex = currentBannerIndex - 1;
      if (prevIndex < 0) {
        prevIndex = banners.length - 1;
      }
      showBanner(prevIndex);
    }
  
    // Iniciar autoplay
    function startBannerAutoplay() {
      bannerInterval = setInterval(nextBanner, bannerAutoplayTime);
    }
  
    // Parar autoplay
    function stopBannerAutoplay() {
      clearInterval(bannerInterval);
    }
  
    // Adicionar event listeners para os botões de navegação
    nextBtn.addEventListener('click', function() {
      nextBanner();
      // Reiniciar o temporizador quando o usuário clica no botão
      stopBannerAutoplay();
      startBannerAutoplay();
    });
  
    prevBtn.addEventListener('click', function() {
      prevBanner();
      // Reiniciar o temporizador quando o usuário clica no botão
      stopBannerAutoplay();
      startBannerAutoplay();
    });
  
    // Adicionar event listeners para os indicadores
    indicators.forEach((indicator, index) => {
      indicator.addEventListener('click', function() {
        showBanner(index);
        // Reiniciar o temporizador quando o usuário seleciona um banner
        stopBannerAutoplay();
        startBannerAutoplay();
      });
    });
  
    // Mostrar/ocultar botões de navegação em hover
    const bannerSection = document.querySelector('.banner-section');
    
    bannerSection.addEventListener('mouseenter', function() {
      prevBtn.classList.add('visible');
      nextBtn.classList.add('visible');
    });
    
    bannerSection.addEventListener('mouseleave', function() {
      prevBtn.classList.remove('visible');
      nextBtn.classList.remove('visible');
    });
  
    // Iniciar carrossel de banners
    showBanner(0);
    startBannerAutoplay();
  
    // Funcionalidade do botão de favorito nos livros
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    
    favoriteButtons.forEach(button => {
      button.addEventListener('click', function() {
        const icon = this.querySelector('i');
        
        if (icon.classList.contains('far')) {
          // Adicionar aos favoritos
          icon.classList.remove('far');
          icon.classList.add('fas');
          this.classList.add('active');
        } else {
          // Remover dos favoritos
          icon.classList.remove('fas');
          icon.classList.add('far');
          this.classList.remove('active');
        }
      });
    });
  
    // Paginação para livros em destaque
    const featuredBooksContainer = document.querySelector('.featured-books-section');
    const bookGrid = document.querySelector('.book-grid');
    const books = document.querySelectorAll('.book-card');
    const paginationButtons = document.querySelectorAll('.page-btn');
    const booksPerPage = 10;
    
    // Verificar se precisamos mostrar a paginação
    if (books.length > booksPerPage) {
      document.querySelector('.pagination').style.display = 'flex';
    } else {
      document.querySelector('.pagination').style.display = 'none';
    }
    
    // Função para mostrar uma página específica de livros
    function showPage(pageNumber) {
      const startIndex = (pageNumber - 1) * booksPerPage;
      const endIndex = startIndex + booksPerPage;
      
      // Ocultar todos os livros primeiro
      books.forEach((book, index) => {
        if (index >= startIndex && index < endIndex) {
          book.style.display = 'block';
        } else {
          book.style.display = 'none';
        }
      });
      
      // Atualizar botões de paginação
      paginationButtons.forEach((button, index) => {
        if (index + 1 === pageNumber) {
          button.classList.add('active');
        } else {
          button.classList.remove('active');
        }
      });
    }
    
    // Adicionar event listeners para os botões de paginação
    paginationButtons.forEach((button, index) => {
      button.addEventListener('click', function() {
        showPage(index + 1);
      });
    });
    
    // Mostrar a primeira página por padrão
    showPage(1);
  });