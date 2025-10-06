<?php

declare(strict_types=1);

namespace App;

use Tempest\Auth\Authentication\Authenticatable;
use Tempest\Database\PrimaryKey;
use Tempest\DateTime\DateTime;
use Tempest\Validation\Rules\IsEmail;
use Tempest\Validation\Rules\IsUuid;

final class User implements Authenticatable
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
}
