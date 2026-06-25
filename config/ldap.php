<?php

return [
    'host' => env('LDAP_HOST', '10.121.1.162'),
    'port' => env('LDAP_PORT', 389),
    'domain' => env('LDAP_DOMAIN', 'domain-anda.local'),
    'base_dn' => env('LDAP_BASE_DN', ''),
    'admin_user' => env('LDAP_ADMIN_USER', ''),
    'admin_pass' => env('LDAP_ADMIN_PASS', ''),
];
