<?php

error_reporting(E_ALL);
date_default_timezone_set('America/Lima');

session_start();

/**
 * Autoload simple
 */
spl_autoload_register(function ($class) {

    $baseDir = __DIR__ . '/../';

    $file = $baseDir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

use App\Core\Router;

/**
 * Crear router
 */
$map = new Router();

/**
 * Cargar rutas
 */
require_once __DIR__ . '/../routers/web.php';

/**
 * Ejecutar rutas
 */
$map->run();
