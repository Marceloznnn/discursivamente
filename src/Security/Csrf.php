<?php

namespace Security;

class Csrf
{
    const TOKEN_TTL = 900; // 15 minutos (em segundos)

    public static function generateToken(string $form = '_default'): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['_csrf'][$form] = [
            'token' => $token,
            'created_at' => time()
        ];

        return $token;
    }

    public static function getToken(string $form = '_default'): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['_csrf'][$form]) || self::isExpired($_SESSION['_csrf'][$form]['created_at'])) {
            return self::generateToken($form);
        }

        return $_SESSION['_csrf'][$form]['token'];
    }

    public static function validateToken(string $submittedToken, string $form = '_default'): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['_csrf'][$form])) {
            return false;
        }

        $stored = $_SESSION['_csrf'][$form];

        if (self::isExpired($stored['created_at'])) {
            unset($_SESSION['_csrf'][$form]);
            return false;
        }

        $valid = hash_equals($stored['token'], $submittedToken);

        // Regenerar token após verificação
        unset($_SESSION['_csrf'][$form]);

        return $valid;
    }
 
    private static function isExpired(int $createdAt): bool
    {
        return (time() - $createdAt) > self::TOKEN_TTL;
    }
}
