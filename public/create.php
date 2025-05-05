<?php

use Infrastructure\Database\Connection;
use Repositories\UserRepository;
use App\Models\User;

// Função para criar o usuário admin
function createAdminUser() {
    // Dados do usuário admin
    $name = 'Admin User';
    $email = 'admin@example.com';  // Altere para o email do admin
    $password = 'adminpassword';   // Altere para uma senha segura
    $type = 'admin';  // Tipo do usuário, 'admin' para o administrador
    $bio = 'Administrador do sistema';

    // Criptografando a senha
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Conectando ao banco de dados
    $connection = Connection::getInstance();
    $userRepository = new UserRepository($connection);

    // Criando o novo usuário
    $user = new User(
        $name,
        $email,
        $hashedPassword,
        $type,
        null,      // id será auto-incrementado
        null,      // recoveryCode
        null,      // recoveryCodeExpiration
        null,      // avatar
        date('Y-m-d H:i:s'),  // created_at
        date('Y-m-d H:i:s'),  // updated_at
        $bio
    );

    // Salvando o usuário no banco de dados
    $userRepository->save($user);

    echo "Usuário Admin criado com sucesso!";
}

// Chama a função para criar o usuário
createAdminUser();
