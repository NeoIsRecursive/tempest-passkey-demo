<?php

declare(strict_types=1);

namespace App\Authentication;

use Tempest\Database\MigratesUp;
use Tempest\Database\QueryStatement;
use Tempest\Database\QueryStatements\CreateTableStatement;

final class CreateVerificationCodesTable implements MigratesUp
{
    public string $name = '2026-06-18_create_verification_codes_table';

    public function up(): QueryStatement
    {
        return new CreateTableStatement('verification_codes')
            ->primary()
            ->string('code')
            ->string('email')
            ->string('purpose')
            ->datetime('consumed_at', nullable: true)
            ->datetime('expires_at')
            ->datetime('created_at');
    }
}
