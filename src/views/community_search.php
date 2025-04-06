<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Pesquisa de Comunidades</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
    .breadcrumb { margin-bottom: 20px; }
    .breadcrumb a { text-decoration: none; color: #007bff; }
    .breadcrumb a:hover { text-decoration: underline; }
    .container { max-width: 960px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; }
    h1 { margin-bottom: 20px; }
    .result-section { margin-bottom: 40px; }
    .result-section h2 { border-bottom: 2px solid #ddd; padding-bottom: 10px; }
    .item { padding: 10px; border-bottom: 1px solid #eee; }
    .item:last-child { border-bottom: none; }
    a { text-decoration: none; color: #007bff; }
    a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <!-- Breadcrumb com link relativo para a página de Comunidades -->
  <div class="breadcrumb">
    <a href="../comunidades/index.php">Comunidades</a> &raquo; Buscar
  </div>
  
  <div class="container">
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
              <p><a href="../comunidades/forum.php?id=<?php echo $forum['id']; ?>">Ver fórum</a></p>
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
              <p><a href="../comunidades/group.php?id=<?php echo $group['id']; ?>">Ver grupo</a></p>
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
              <p><a href="../comunidades/club.php?id=<?php echo $club['id']; ?>">Ver clube</a></p>
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
              <p><a href="../comunidades/event.php?id=<?php echo $event['id']; ?>">Ver evento</a></p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
    
    <p><a href="../comunidades/index.php">Voltar para Comunidades</a></p>
  </div>
</body>
</html>
