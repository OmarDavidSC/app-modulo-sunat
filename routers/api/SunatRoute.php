<?php

$map->attach('/sunat', function ($map) {

    $map->post('emit', '/emit', [
        'Controller' => 'App\Controllers\SunatController',
        'Action' => 'emit'
    ]);
    $map->get('document', '/{document_id}/document', [
        'Controller' => 'App\Controllers\SunatController',
        'Action' => 'document'
    ]);

});