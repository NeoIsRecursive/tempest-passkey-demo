<?php

declare(strict_types=1);

namespace App\Authentication;

use SensitiveParameter;
use Tempest\Database\Hashed;
use Tempest\Database\PrimaryKey;
use Tempest\DateTime\DateTime;

final class VerificationCode
{
    public PrimaryKey $id;

    public function __construct(
        #[SensitiveParameter, Hashed]
        public string $code,
        public string $email,
        public VerificationPurpose $purpose,
        public ?DateTime $consumed_at,
        public DateTime $expires_at,
        public DateTime $created_at,
    ) {}
}
