<?php require_once BASE_PATH . '/src/views/partials/header.php'; ?>

<div class="breadcrumb">
  <a href="/comunidades">Comunidades</a> &raquo; Buscar
</div>

<div class="container community-search-container">
  <h1>Resultados para: "<em><?php echo htmlspecialchars($query); ?></em>"</h1>
  
  <?php if (empty($forums) && empty($groups) && empty($clubs) && empty($events)): ?>
    <p>Nenhum resultado encontrado.</p>
  <?php else: ?>
    <?php if (!empty($forums)): ?>
      <div class="result-section">
        <h2>Fóruns</h2>
        <?php foreach ($forums as $forum): ?>
          <div class="item">
            <strong><?php echo htmlspecialchars($forum['title']); ?></strong>
            <p><?php echo htmlspecialchars($forum['description']); ?></p>
            <p><a href="/comunidades/forum.php?id=<?php echo $forum['id']; ?>">Ver fórum</a></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    
    <?php if (!empty($groups)): ?>
      <div class="result-section">
        <h2>Grupos de Livros</h2>
        <?php foreach ($groups as $group): ?>
          <div class="item">
            <strong><?php echo htmlspecialchars($group['name']); ?></strong>
            <p><?php echo htmlspecialchars($group['description']); ?></p>
            <p><a href="/comunidades/group.php?id=<?php echo $group['id']; ?>">Ver grupo</a></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    
    <?php if (!empty($clubs)): ?>
      <div class="result-section">
        <h2>Clubes</h2>
        <?php foreach ($clubs as $club): ?>
          <div class="item">
            <strong><?php echo htmlspecialchars($club['name']); ?></strong>
            <p><?php echo htmlspecialchars($club['description']); ?></p>
            <p><a href="/comunidades/club.php?id=<?php echo $club['id']; ?>">Ver clube</a></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    
    <?php if (!empty($events)): ?>
      <div class="result-section">
        <h2>Eventos</h2>
        <?php foreach ($events as $event): ?>
          <div class="item">
            <strong><?php echo htmlspecialchars($event['title']); ?></strong>
            <p><?php echo htmlspecialchars($event['description']); ?></p>
            <p><a href="/comunidades/event.php?id=<?php echo $event['id']; ?>">Ver evento</a></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
  
  <p><a href="/comunidades">Voltar para Comunidades</a></p>
</div>

<?php require_once BASE_PATH . '/src/views/partials/footer.php'; ?>
