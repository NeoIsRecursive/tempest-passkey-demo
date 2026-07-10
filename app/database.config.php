<?php

declare(strict_types=1);

use Tempest\Database\Config\MysqlConfig;

use function Tempest\env;

return new MysqlConfig(
    host: env('DATABASE_HOST', default: '127.0.0.1'),
    port: env('DATABASE_PORT', default: '3306'),
    username: env('DATABASE_USERNAME', default: ''),
    password: env('DATABASE_PASSWORD', default: ''),
    database: env('DATABASE_DATABASE', default: ''),
);
