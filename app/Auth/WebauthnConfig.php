<?php

declare(strict_types=1);

namespace App\Auth;

use Firehed\WebAuthn\RelyingPartyInterface;

final readonly class WebauthnConfig
{
    public const string USER_UUID_SESSION_KEY = '#regestering_user_uuid';

    public function __construct(
        public string $relyingPartyName,
        public string $relyingPartyId,
    ) {}
}
