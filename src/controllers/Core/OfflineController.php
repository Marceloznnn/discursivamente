<?php
namespace App\Controllers\Core;

class OfflineController {
    // Exibe uma página offline personalizada
    public function index() {
        include BASE_PATH . '/src/views/core/offline.php';
    }
}
