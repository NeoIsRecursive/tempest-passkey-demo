<?php

declare(strict_types=1);

namespace App\Auth\Webauthn;

final readonly class RegistrationCompleted
{
    public function __construct(
        public string $userUuid,
        public string $email,
        public string $publicKey,
        public string $credentialId,
    ) {}
}
