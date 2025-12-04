<?php
$databaseUrl = getenv('DATABASE_URL') ?: getenv('DATABASE_PUBLIC_URL');

if ($databaseUrl) {
    $dbParts = parse_url($databaseUrl);
    return [
        'db_driver' => 'pgsql',
        'db_host' => $dbParts['host'],
        'db_port' => $dbParts['port'] ?? 5432,
        'db_name' => ltrim($dbParts['path'], '/'),
        'db_user' => $dbParts['user'],
        'db_pass' => $dbParts['pass'],
        'base_url' => 'https://' . (getenv('RAILWAY_PUBLIC_DOMAIN') ?: 'task4php-production.up.railway.app'),
        'mail_from' => 'no-reply@example.com',
        'debug_show_confirmation_link' => true,
    ];
}

return [
    'db_driver' => 'mysql',
    'db_host' => '127.0.0.1',
    'db_port' => 3306,
    'db_name' => 'task4_php',
    'db_user' => 'root',
    'db_pass' => '',
    'base_url' => 'http://task4',
    'mail_from' => 'no-reply@example.com',
    'debug_show_confirmation_link' => true,
];
