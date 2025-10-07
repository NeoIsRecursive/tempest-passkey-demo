<?php

declare(strict_types=1);

namespace App;

use JsonSerializable;
use Tempest\Auth\Authentication\Authenticatable;
use Tempest\Database\PrimaryKey;
use Tempest\DateTime\DateTime;
use Tempest\Validation\Rules\IsEmail;
use Tempest\Validation\Rules\IsUuid;

final class User implements Authenticatable, JsonSerializable
{
    public PrimaryKey $id;

    #[IsUuid]
    public string $uuid;

    #[IsEmail]
    public string $email;

    public DateTime $created_at;
    public DateTime $updated_at;

    /** @var \App\Passkey[] */
    public array $passkeys = [];

    public function jsonSerialize(): array
    {
        return [
            'id' => (int) $this->id->value,
            'uuid' => $this->uuid,
            'email' => $this->email,
            'created_at' => $this->created_at->toRfc3339(),
            'updated_at' => $this->updated_at->toRfc3339(),
        ];
    }
}
