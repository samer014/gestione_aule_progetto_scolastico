<?php
return [
    'secret_key' => getenv('JWT_SECRET_KEY') ?: 'your-secret-key-here',
    'token_lifetime' => 3600,
    'refresh_token_lifetime' => 86400,
    'algorithm' => 'HS256',
    'issuer' => 'gestione-aule',
    'rate_limit' => [
        'max_attempts' => 5,
        'window' => 900
    ]
];