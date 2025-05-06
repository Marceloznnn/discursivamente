<?php

namespace Infrastructure\Twig;

use Twig\TwigFunction;

// Função para registrar funções personalizadas no Twig
class TwigFunctions
{
    public static function addCustomFunctions($twig)
    {
        // Função personalizada que retorna a URL atual
        $twig->addFunction(new TwigFunction('current_url', function () {
            return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }));
    }
}
