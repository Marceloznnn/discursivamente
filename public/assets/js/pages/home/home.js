document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.querySelector('.slider-wrapper');
    const slides  = document.querySelectorAll('.evento-item');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    let index = 0;
    const total = slides.length;
  
    function updateSlider() {
      wrapper.style.transform = `translateX(-${index * 100}%)`;
    }
  
    prevBtn.addEventListener('click', () => {
      index = (index - 1 + total) % total;
      updateSlider();
    });
  
    nextBtn.addEventListener('click', () => {
      index = (index + 1) % total;
      updateSlider();
    });
  
    // avanço automático a cada 5 segundos
    setInterval(() => {
      index = (index + 1) % total;
      updateSlider();
    }, 5000);
  });
  