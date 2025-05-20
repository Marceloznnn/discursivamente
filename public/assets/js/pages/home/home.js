document.addEventListener('DOMContentLoaded', () => {
  // —— Slider de Eventos ——
  const wrapper = document.querySelector('.slider-wrapper');
  const slides = Array.from(document.querySelectorAll('.evento-item'));
  const prevBtn = document.querySelector('.prev-btn');
  const nextBtn = document.querySelector('.next-btn');

  if (wrapper && slides.length && prevBtn && nextBtn) {
    let index = 0;
    function update() {
      slides.forEach((slide, i) => {
        slide.style.display = (i === index) ? 'flex' : 'none';
      });
    }
    function showNext() {
      index = (index + 1) % slides.length;
      update();
    }
    function showPrev() {
      index = (index - 1 + slides.length) % slides.length;
      update();
    }
    prevBtn.addEventListener('click', showPrev);
    nextBtn.addEventListener('click', showNext);
    // Inicialização
    update();
    // Responsivo: mostra todos no desktop, slider no mobile
    function adapt() {
      if (window.innerWidth < 1024) {
        update();
        prevBtn.style.display = nextBtn.style.display = 'flex';
      } else {
        slides.forEach(slide => slide.style.display = 'flex');
        prevBtn.style.display = nextBtn.style.display = 'none';
      }
    }
    adapt();
    window.addEventListener('resize', adapt);
  }

  <script>
document.addEventListener('DOMContentLoaded', () => {
  const items = document.querySelectorAll('.course-item');
  const prevBtn = document.querySelector('.nav-btn.prev');
  const nextBtn = document.querySelector('.nav-btn.next');
  const dots    = document.querySelectorAll('.slider-dots .dot');
  let index = 0;
  let timer;

  function showSlide(i) {
    items.forEach((el, idx) => {
      el.style.display = (idx === i ? 'block' : 'none');
    });
    dots.forEach((dot, idx) => {
      dot.classList.toggle('active', idx === i);
    });
  }

  function nextSlide() {
    index = (index + 1) % items.length;
    showSlide(index);
  }

  function prevSlide() {
    index = (index - 1 + items.length) % items.length;
    showSlide(index);
  }

  function startAutoplay() {
    timer = setInterval(nextSlide, 6000);
  }

  function stopAutoplay() {
    clearInterval(timer);
  }

  // eventos
  nextBtn.addEventListener('click', () => { stopAutoplay(); nextSlide(); startAutoplay(); });
  prevBtn.addEventListener('click', () => { stopAutoplay(); prevSlide(); startAutoplay(); });
  dots.forEach(dot => {
    dot.addEventListener('click', () => {
      stopAutoplay();
      index = parseInt(dot.dataset.index);
      showSlide(index);
      startAutoplay();
    });
  });

  // inicialização
  if (window.innerWidth < 768) {
    showSlide(index);
    startAutoplay();
  }

  // adapt on resize
  window.addEventListener('resize', () => {
    if (window.innerWidth < 768) {
      showSlide(index);
      startAutoplay();
    } else {
      stopAutoplay();
      items.forEach(el => el.style.display = '');
      dots.forEach(dot => dot.classList.remove('active'));
    }
  }); 
});
</script>
 

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