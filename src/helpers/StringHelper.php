<?php
namespace App\Helpers;

class StringHelper {
    /**
     * Converte uma string para um formato "slug".
     * Ex.: "Olá Mundo!" -> "ola-mundo"
     */
    public static function slugify($string) {
        // Converte para minúsculas e remove espaços nas extremidades
        $slug = strtolower(trim($string));
        // Substitui caracteres não alfanuméricos por hífens
        $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
        // Remove hífens extras no início e no fim
        $slug = trim($slug, '-');
        return $slug;
    }

    /**
     * Trunca uma string para um tamanho máximo, preservando palavras inteiras.
     * 
     * @param string $string Texto a ser truncado.
     * @param int $limit Número máximo de caracteres.
     * @param string $break Caracter onde a quebra pode ocorrer.
     * @param string $pad Texto a ser adicionado no final (por padrão, reticências).
     * @return string
     */
    public static function truncate($string, $limit = 100, $break = " ", $pad = "...") {
        if (strlen($string) <= $limit) return $string;
        $string = substr($string, 0, $limit);
        if (false !== ($breakpoint = strrpos($string, $break))) {
            $string = substr($string, 0, $breakpoint);
        }
        return $string . $pad;
    }
}
?>
