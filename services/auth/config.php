<?php

return array(
    'jwt' => array(
        'key'       => base64_encode('sherlocked'),     // Key for signing the JWT's, I suggest generate it with base64_encode(openssl_random_pseudo_bytes(64))
        'algorithm' => 'HS512' // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
    ),
    'database' => array(
        'user'     => 'root', // Database username
        'password' => 'e842', // Database password
        'host'     => 'localhost', // Database host
        'name'     => 'iim-trbs-dev', // Database schema name
    ),
    'serverName' => 'localhost',
    'frontendUrl' => 'http://iima-trbs.in',	
    'tenantEmailId' => 'trbs2019@iima.ac.in',
    'tenantEmailName' => 'TRBS'
);
