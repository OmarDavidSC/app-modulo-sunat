<?php

$map->get('index', '/', [
    'Controller' => 'App\Controllers\HomeController',
    'Action' => 'index'
]);


include __DIR__ . "/api.php";
