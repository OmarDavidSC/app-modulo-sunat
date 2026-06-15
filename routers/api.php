<?php

$map->attach('/api', function ($map) {

    // $map->post('login', '/login', [
    //     'Controller' => 'App\Controllers\AuthController',
    //     'Action' => 'login'
    // ]);

    include __DIR__ . "/api/SunatRoute.php";
});
