<?php

namespace App\Controllers;

class BaseController
{
    public function __construct() {}
}

class Response
{
    /**
     * Respuesta JSON
     */
    public static function json(array $data = [], int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Respuesta HTML
     */
    public static function html(string $content, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=utf-8');
        echo $content;
        exit;
    }

    /**
     * Texto plano
     */
    public static function text(string $content, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: text/plain; charset=utf-8');
        echo $content;
        exit;
    }

    /**
     * Redirección
     */
    public static function redirect(string $url, int $status = 302)
    {
        header("Location: {$url}", true, $status);
        exit;
    }

    /**
     * Renderizar una vista PHP
     */
    public static function view(string $filename, array $data = [])
    {
        $path = __DIR__ . '/../Views/' . $filename . '.php';
        if (!file_exists($path)) {
            self::html("Vista no encontrada: {$filename}", 404);
        }
        // convierte array a variables
        extract($data);
        ob_start();
        require $path;
        $content = ob_get_clean();
        self::html($content);
    }
}
