<?php

declare(strict_types=1);

namespace App\Auth\Webauthn;

use App\User;
use Firehed\WebAuthn\CredentialInterface;

final readonly class LoginCompleted
{
    public function __construct(
        public User $user,
        public CredentialInterface $credential,
        public string $publicKey,
    ) {}
}
