<?php

return [
    'sunat' => [
        'api_url' => $ENV['SUNAT_API_URL'] ?? 'https://api.sunat.gob.pe/v1/',
        'token' => $ENV['SUNAT_API_TOKEN'] ?? '',
        'timeout' => $ENV['SUNAT_API_TIMEOUT'] ?? 30,
    ]
];
