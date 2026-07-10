<?php

declare(strict_types=1);

namespace App\Authentication\Verification;

final readonly class CodeGenerator
{
    public static function generate(): string
    {
        return (string) random_int(100_000, 999_999);
    }
}
