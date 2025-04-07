<?php
// Exemplo: Verifica se a variável $forum foi definida e é um objeto
if (!isset($forum) || !is_object($forum)) {
    // Você pode definir um objeto padrão ou exibir uma mensagem de erro
    $forum = new stdClass();
    $forum->title       = 'Fórum não encontrado';
    $forum->description = 'Nenhuma descrição disponível.';
    $forum->author      = 'Desconhecido';
    $forum->date        = 'Data não informada';
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($forum->title); ?></title>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($forum->title); ?></h1>
    </header>

    <section>
        <p><?php echo htmlspecialchars($forum->description); ?></p>
    </section>

    <footer>
        <p>
            <?php 
                // Verifica se a propriedade existe antes de tentar exibir
                echo 'Autor: ' . (isset($forum->author) ? htmlspecialchars($forum->author) : 'N/A'); 
            ?>
        </p>
        <p>
            <?php 
                echo 'Data: ' . (isset($forum->date) ? htmlspecialchars($forum->date) : 'N/A'); 
            ?>
        </p>
    </footer>
</body>
</html>
