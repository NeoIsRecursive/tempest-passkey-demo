<?php

declare(strict_types=1);

namespace App;

use Tempest\Database\PrimaryKey;
use Tempest\DateTime\DateTime;

final class Passkey
{
    public PrimaryKey $id;
    public int $user_id;
    public string $credential_id;
    public string $public_key;

    public DateTime $created_at;
    public DateTime $updated_at;
}
