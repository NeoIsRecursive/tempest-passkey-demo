<?php
declare(strict_types=1);

use Tempest\Database\Config\MysqlConfig;

final readonly class ResolveConfigFromConnectionUrl
{
    public static function parse(?string $url): ?MysqlConfig
    {
        if (! $url) {
            return null;
        }

        $parts = parse_url($url);

        if ($parts === false) {
            throw new InvalidArgumentException('Could not parse the DATABASE_URL environment variable.');
        }

        $host = $parts['host'] ?? '127.0.0.1';
        $port = (string) ($parts['port'] ?? '3306');
        $username = $parts['user'] ?? '';
        $password = $parts['pass'] ?? '';
        $database = $parts['path'] ?? '';

        return new MysqlConfig(
            host: $host,
            port: $port,
            username: rawurldecode($username),
            password: rawurldecode($password),
            database: ltrim($database, '/'),
        );
    }
}
