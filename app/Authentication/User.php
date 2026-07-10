<?php

declare(strict_types=1);

namespace App\Authentication;

use JsonSerializable;
use Tempest\Auth\Authentication\Authenticatable;
use Tempest\Database\PrimaryKey;
use Tempest\DateTime\DateTime;

final class User implements Authenticatable, JsonSerializable
{
    public PrimaryKey $id;

    public function __construct(
        public string $email,
        public ?DateTime $email_verified_at,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->value,
            'email' => $this->email,
        ];
    }
}
