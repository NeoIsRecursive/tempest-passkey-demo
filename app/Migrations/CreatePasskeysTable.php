<?php

declare(strict_types=1);

namespace App\Migrations;

use Tempest\Database\MigratesDown;
use Tempest\Database\MigratesUp;
use Tempest\Database\QueryStatement;
use Tempest\Database\QueryStatements\CreateTableStatement;
use Tempest\Database\QueryStatements\DropTableStatement;

final class CreatePasskeysTable implements MigratesUp, MigratesDown
{
    public string $name = '2025-10-05_passkeys';

    public function up(): QueryStatement
    {
        return new CreateTableStatement('passkeys')
            ->primary()
            ->integer('user_id', unsigned: true)
            ->text('credential_id')
            ->text('public_key')
            ->string('aaguid', nullable: true)
            ->datetime('created_at')
            ->datetime('updated_at');
    }

    public function down(): QueryStatement
    {
        return new DropTableStatement('passkeys');
    }
}
