// home/index.js - Navegação dos depoimentos (testimonials)
document.addEventListener('DOMContentLoaded', function () {
  const testimonials = Array.from(document.querySelectorAll('.testimonial-item'));
  const prevBtn = document.getElementById('test-prev');
  const nextBtn = document.getElementById('test-next');
  let current = testimonials.findIndex(t => t.classList.contains('active'));
  if (current === -1) current = 0;

  function showTestimonial(idx) {
    testimonials.forEach((el, i) => {
      el.classList.toggle('active', i === idx);
    });
  }

  prevBtn.addEventListener('click', function () {
    current = (current - 1 + testimonials.length) % testimonials.length;
    showTestimonial(current);
  });

  nextBtn.addEventListener('click', function () {
    current = (current + 1) % testimonials.length;
    showTestimonial(current);
  });

  // Acessibilidade: setas do teclado
  document.querySelector('.testimonials-section').addEventListener('keydown', function (e) {
    if (e.key === 'ArrowLeft') prevBtn.click();
    if (e.key === 'ArrowRight') nextBtn.click();
  });
});
