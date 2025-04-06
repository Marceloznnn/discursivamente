<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Pesquisa de Livros</title>
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
  <!-- Breadcrumb com link relativo para a Home (Início) -->
  <div class="breadcrumb">
    <a href="../inicio/home.php">Início</a> &raquo; Buscar Livros
  </div>
  
  <div class="container">
    <h1>Resultados para: "<em><?php echo htmlspecialchars($query); ?></em>"</h1>
    
    <?php if (empty($books) && empty($categories)): ?>
      <p>Nenhum resultado encontrado.</p>
    <?php else: ?>
      <?php if (!empty($books)): ?>
        <div class="result-section">
          <h2>Livros</h2>
          <?php foreach ($books as $book): ?>
            <div class="item">
              <strong><?php echo htmlspecialchars($book['title']); ?></strong>
              <p>Autor: <?php echo htmlspecialchars($book['author']); ?></p>
              <p><a href="../biblioteca/detalhe.php?id=<?php echo $book['id']; ?>">Ver detalhes</a></p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($categories)): ?>
        <div class="result-section">
          <h2>Categorias</h2>
          <?php foreach ($categories as $cat): ?>
            <div class="item">
              <strong><?php echo htmlspecialchars($cat['name']); ?></strong>
              <p><a href="../biblioteca/categoria.php?id=<?php echo $cat['id']; ?>">Ver livros desta categoria</a></p>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
    
    <p><a href="../inicio/home.php">Voltar para a Início</a></p>
  </div>
</body>
</html>
