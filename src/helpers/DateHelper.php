<?php
namespace App\Helpers;

class DateHelper {
    /**
     * Formata uma data para um padrão específico.
     *
     * @param string $date Data em formato reconhecível (ex.: "2025-04-06 12:00:00").
     * @param string $format Formato desejado (ex.: "d/m/Y H:i:s").
     * @return string Data formatada.
     */
    public static function formatDate($date, $format = 'd/m/Y H:i:s') {
        $dateObj = new \DateTime($date);
        return $dateObj->format($format);
    }

    /**
     * Retorna uma string que indica quanto tempo se passou desde a data informada.
     * Ex.: "5 minutes ago", "2 hours ago".
     *
     * @param string $date Data em formato reconhecível.
     * @return string
     */
    public static function timeAgo($date) {
        $timestamp = strtotime($date);
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return $diff . ' seconds ago';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' minutes ago';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' hours ago';
        } else {
            return floor($diff / 86400) . ' days ago';
        }
    }
}
?>
