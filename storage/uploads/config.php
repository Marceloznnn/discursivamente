<?php
// storage/uploads/config.php
return [
    'max_file_size' => 5 * 1024 * 1024, // 5 MB
    'allowed_types' => ['image/jpeg', 'image/png', 'application/pdf'],
    'upload_dir'    => __DIR__ . '/',
];
?>
