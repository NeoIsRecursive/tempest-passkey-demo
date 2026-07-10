<?php

declare(strict_types=1);

namespace App\Authentication\Webauthn;

use Tempest\Database\MigratesDown;
use Tempest\Database\MigratesUp;
use Tempest\Database\QueryStatement;
use Tempest\Database\QueryStatements\CreateTableStatement;
use Tempest\Database\QueryStatements\DropTableStatement;

final class CreatePasskeysTable implements MigratesUp, MigratesDown
{
    public string $name = '2026-06-22_create_passkeys_table';

    public function up(): QueryStatement
    {
        return new CreateTableStatement('passkeys')
            ->primary()
            ->foreignId('user_id', 'users')
            ->string('name')
            ->raw('credential_id VARBINARY(255) NOT NULL UNIQUE')
            ->json('data')
            ->datetime('created_at')
            ->datetime('updated_at');
    }

    public function down(): QueryStatement
    {
        return new DropTableStatement('passkeys');
    }
}
