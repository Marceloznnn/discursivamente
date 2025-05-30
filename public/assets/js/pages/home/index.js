// Slider de eventos em destaque - Sistema completo sem rotação automática
(function () {
  document.addEventListener("DOMContentLoaded", function () {
    const items = Array.from(document.querySelectorAll(".evento-item"));
    const slider = document.getElementById("eventos-slider");
    const prevBtn = document.getElementById("eventos-prev-btn");
    const nextBtn = document.getElementById("eventos-next-btn");
    const sliderWrapper = document.getElementById("eventos-slider-wrapper");

    if (!items.length || !slider) return;

    let current = 0;
    let isTransitioning = false;

    // Mostrar evento específico com animação
    function showEvent(idx, direction = "next") {
      if (isTransitioning) return;
      isTransitioning = true;

      const currentItem = items[current];
      const nextItem = items[idx];

      if (currentItem === nextItem) {
        isTransitioning = false;
        return;
      }

      // Animação de saída
      if (direction === "next") {
        currentItem.classList.add("slide-out-left");
      } else {
        currentItem.classList.add("slide-out-right");
      }

      setTimeout(() => {
        // Esconder item atual
        currentItem.classList.add("d-none");
        currentItem.classList.remove("slide-out-left", "slide-out-right");

        // Mostrar próximo item
        nextItem.classList.remove("d-none");

        if (direction === "next") {
          nextItem.classList.add("slide-in-right");
        } else {
          nextItem.classList.add("slide-in-left");
        }

        current = idx;

        setTimeout(() => {
          nextItem.classList.remove("slide-in-left", "slide-in-right");
          isTransitioning = false;
        }, 500);
      }, 500);
    }

    // Navegação para próximo evento
    function nextEvent() {
      const nextIdx = (current + 1) % items.length;
      showEvent(nextIdx, "next");
    }

    // Navegação para evento anterior
    function prevEvent() {
      const prevIdx = (current - 1 + items.length) % items.length;
      showEvent(prevIdx, "prev");
    }

    // Eventos dos botões
    if (prevBtn && nextBtn) {
      prevBtn.addEventListener("click", function (e) {
        e.preventDefault();
        prevEvent();
      });

      nextBtn.addEventListener("click", function (e) {
        e.preventDefault();
        nextEvent();
      });
    }

    // Controles de teclado
    if (slider) {
      slider.setAttribute("tabindex", "0");
      slider.addEventListener("keydown", function (e) {
        switch (e.key) {
          case "ArrowLeft":
            e.preventDefault();
            prevEvent();
            break;
          case "ArrowRight":
            e.preventDefault();
            nextEvent();
            break;
        }
      });
    }

    // Gerenciar redimensionamento da janela
    let resizeTimeout;
    window.addEventListener("resize", function () {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(function () {}, 250);
    });

    // Preloader para imagens
    function handleImageLoading() {
      items.forEach((item) => {
        const img = item.querySelector(".evento-image img");
        const imageContainer = item.querySelector(".evento-image");

        if (img && imageContainer) {
          imageContainer.classList.add("loading");

          img.addEventListener("load", function () {
            imageContainer.classList.remove("loading");
          });

          img.addEventListener("error", function () {
            imageContainer.classList.remove("loading");
            // Adicionar imagem placeholder se necessário
            img.alt = "Imagem não disponível";
          });

          // Se a imagem já estiver carregada
          if (img.complete) {
            imageContainer.classList.remove("loading");
          }
        }
      });
    }

    // Inicialização
    function init() {
      // Esconder todos os itens exceto o primeiro
      items.forEach((item, index) => {
        if (index !== current) {
          item.classList.add("d-none");
        } else {
          item.classList.remove("d-none");
        }
      });

      // Configurar carregamento de imagens
      handleImageLoading();
    }

    // Adicionar indicadores visuais de acessibilidade
    if (prevBtn && nextBtn) {
      prevBtn.setAttribute("aria-label", "Evento anterior");
      nextBtn.setAttribute("aria-label", "Próximo evento");

      // Adicionar tooltips
      prevBtn.title = "Clique para ver o evento anterior";
      nextBtn.title = "Clique para ver o próximo evento";
    }

    if (slider) {
      slider.setAttribute("role", "region");
      slider.setAttribute("aria-label", "Carrossel de eventos em destaque");
      slider.title =
        "Use as setas do teclado para navegar ou pressione espaço para pausar/retomar";
    }

    // Inicializar tudo
    init();

    // Expor funções globais para debug (opcional)
    window.eventosSlider = {
      next: nextEvent,
      prev: prevEvent,
      goTo: function (index) {
        if (index >= 0 && index < items.length) {
          showEvent(index, index > current ? "next" : "prev");
        }
      },
      getCurrentIndex: function () {
        return current;
      },
      getTotalItems: function () {
        return items.length;
      },
    };
  });
})();
