// progresso.js - Marca/desmarca material como concluído via AJAX e atualiza barra de progresso

document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('progress-form');
  if (!form) return;
  const checkbox = document.getElementById('material-completed');
  const feedback = document.getElementById('progress-feedback');
  const materialId = form.getAttribute('data-material-id');

  checkbox.addEventListener('change', function() {
    fetch(`/user/progress/toggle/${materialId}`, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
    .then(res => res.json())
    .then(data => {
      if (data.completed) {
        feedback.textContent = 'Material marcado como concluído!';
      } else {
        feedback.textContent = 'Material desmarcado.';
      }
      setTimeout(() => feedback.textContent = '', 2000);
      // Atualiza barra de progresso na página do curso, se existir
      if (window.parent && window.parent.updateCourseProgress) {
        window.parent.updateCourseProgress();
      }
    })
    .catch(() => {
      feedback.textContent = 'Erro ao atualizar progresso.';
    });
  });
});
