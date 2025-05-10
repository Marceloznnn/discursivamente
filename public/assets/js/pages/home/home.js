document.addEventListener('DOMContentLoaded', () => {
  // —— Slider de Eventos ——
  const wrapper = document.querySelector('.slider-wrapper');
  const slides = Array.from(document.querySelectorAll('.evento-item'));
  const prevBtn = document.querySelector('.prev-btn');
  const nextBtn = document.querySelector('.next-btn');

  if (wrapper && slides.length && prevBtn && nextBtn) {
    const gap = parseFloat(getComputedStyle(wrapper).gap) || 0;
    const slideWidth = slides[0].getBoundingClientRect().width + gap;
    let index = 0;
    const maxIndex = Math.max(0, slides.length - Math.floor(wrapper.parentElement.clientWidth / slideWidth));

    function update() {
      wrapper.style.transform = `translateX(-${index * slideWidth}px)`;
    }

    prevBtn.addEventListener('click', () => {
      index = Math.max(index - 1, 0);
      update();
    });

    nextBtn.addEventListener('click', () => {
      index = Math.min(index + 1, maxIndex);
      update();
    });
    
    // Inicialização para garantir estado correto
    update();
    
    // Recalcular em caso de redimensionamento
    window.addEventListener('resize', () => {
      const newSlideWidth = slides[0].getBoundingClientRect().width + gap;
      const newMaxIndex = Math.max(0, slides.length - Math.floor(wrapper.parentElement.clientWidth / newSlideWidth));
      
      // Atualizar valores
      index = Math.min(index, newMaxIndex);
      update();
    });
  }

  // —— Slider de Depoimentos ——
  const testimonials = Array.from(document.querySelectorAll('.testimonial-item'));
  const prevTest = document.getElementById('test-prev');
  const nextTest = document.getElementById('test-next');
  let tIndex = 0;

  if (testimonials.length && prevTest && nextTest) {
    function showTestimonial(i) {
      testimonials.forEach((item, idx) => {
        item.classList.toggle('active', idx === i);
      });
    }

    prevTest.addEventListener('click', () => {
      tIndex = (tIndex - 1 + testimonials.length) % testimonials.length;
      showTestimonial(tIndex);
    });

    nextTest.addEventListener('click', () => {
      tIndex = (tIndex + 1) % testimonials.length;
      showTestimonial(tIndex);
    });

    // Autoplay com opção de pausar ao interagir
    let testimonialInterval = setInterval(() => {
      tIndex = (tIndex + 1) % testimonials.length;
      showTestimonial(tIndex);
    }, 5000);

    // Pausar autoplay ao interagir com controles
    [prevTest, nextTest].forEach(btn => {
      btn.addEventListener('mouseenter', () => {
        clearInterval(testimonialInterval);
      });
      
      btn.addEventListener('mouseleave', () => {
        testimonialInterval = setInterval(() => {
          tIndex = (tIndex + 1) % testimonials.length;
          showTestimonial(tIndex);
        }, 5000);
      });
    });

    // Garantir que pelo menos um slide esteja ativo no início
    if (!testimonials.some(item => item.classList.contains('active'))) {
      showTestimonial(0);
    }
  }
});