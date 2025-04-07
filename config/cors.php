<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'], // Rutas afectadas
    'allowed_methods' => ['*'],
   'allowed_origins' => ['http://127.0.0.1:8002'], // ¡Dominio de tu compañero! 
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];