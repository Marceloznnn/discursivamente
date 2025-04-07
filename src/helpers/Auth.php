<?php
namespace Helpers;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Auth {
    /**
     * Verifica se o usuário está autenticado.
     *
     * @return bool Retorna true se o usuário estiver logado, false caso contrário.
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user']);
    }

    /**
     * Retorna os dados do usuário logado.
     *
     * @return array|null Dados do usuário ou null se não estiver logado.
     */
    public static function getUser() {
        return self::isLoggedIn() ? $_SESSION['user'] : null;
    }

    /**
     * Retorna o caminho da imagem de perfil do usuário.
     *
     * @return string Caminho da imagem ou o caminho padrão se não houver imagem definida.
     */
    public static function getProfileImage() {
        $user = self::getUser();
        return ($user && isset($user['profileImage']) && !empty($user['profileImage']))
            ? $user['profileImage']
            : '/assets/images/default.png';
    }
}
