<?php

declare(strict_types=1);

namespace App\Validation;

use Attribute;
use Override;
use Tempest\Validation\HasTranslationVariables;
use Tempest\Validation\Rule;

use function Tempest\Database\query;

#[Attribute]
final class IsUnique implements Rule, HasTranslationVariables
{
    public function __construct(
        private readonly string $table,
        private readonly string $column,
    ) {}

    public function isValid(mixed $value): bool
    {
        if (! is_numeric($value) && ! is_string($value)) {
            return false;
        }

        $existing = query($this->table)
            ->count($this->column)
            ->where($this->column, $value)
            ->execute();

        return $existing === 0;
    }

    #[Override]
    public function getTranslationVariables(): array
    {
        return [
            'table' => $this->table,
            'column' => $this->column,
        ];
    }
}
