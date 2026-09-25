<?php

declare(strict_types=1);

use App\ResolveConfigFromConnectionUrl;
use Tempest\Database\Config\MysqlConfig;

use function Tempest\env;

$config = ResolveConfigFromConnectionUrl::parse(env('DATABASE_URL'));

if ($config) {
    return $config;
}

return new MysqlConfig(
    host: env('DB_HOST', default: '127.0.0.1'),
    port: env('DB_PORT', default: '3306'),
    username: env('DB_USERNAME', default: ''),
    password: env('DB_PASSWORD', default: ''),
    database: env('DB_DATABASE', default: ''),
);
