<?php
// Arquivo: src/Infrastructure/Database/pdo.php
// Retorna uma instância PDO para uso nos repositórios
return new PDO(
    'mysql:host=localhost;dbname=discursivamente;charset=utf8mb4',
    'root', // usuário do banco
    '',     // senha do banco
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);
