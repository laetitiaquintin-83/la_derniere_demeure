<?php

require_once __DIR__ . '/Config/config.local.php';

spl_autoload_register(function (string $class): void {
    $directories = ['Controllers', 'Models'];

    foreach ($directories as $directory) {
        $filePath = __DIR__ . '/' . $directory . '/' . $class . '.php';
        if (is_file($filePath)) {
            require_once $filePath;
            return;
        }
    }
});