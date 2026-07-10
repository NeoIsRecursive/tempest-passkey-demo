<?php

declare(strict_types=1);

namespace App\Authentication;

use Tempest\Database\Builder\QueryBuilders\SupportsWhereStatements;

final readonly class BelongsToUser
{
    public function __construct(
        private User $user,
    ) {}

    public function __invoke(SupportsWhereStatements $query): SupportsWhereStatements
    {
        return $query->whereField('user_id', $this->user->id->value);
    }
}
