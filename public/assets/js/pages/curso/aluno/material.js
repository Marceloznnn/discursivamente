document.addEventListener('DOMContentLoaded', () => {
  const video = document.querySelector('#material-video');
  if (!video) return;

  let sent = false;
  video.addEventListener('timeupdate', () => {
    const progress = video.currentTime / video.duration;
    if (!sent && progress >= 0.75) {
      sent = true; // evita múltiplas chamadas
      const entryId = video.closest('[data-entry-id]').dataset.entryId;
      fetch(`/courses/${courseId}/modules/${moduleId}/complete`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include', // mantém sessão
        body: JSON.stringify({ entryId: parseInt(entryId) })
      })
      .then(res => {
        if (!res.ok) throw new Error('Falha ao marcar completo');
        // opcional: atualizar o botão de toggle
        document.getElementById('toggle-complete').classList.add('on');
      })
      .catch(console.error);
    }
  });
});
