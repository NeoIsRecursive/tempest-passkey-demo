<?php

declare(strict_types=1);

namespace Passkeys;

use Webauthn\PublicKeyCredentialRpEntity;

final readonly class PasskeyConfig
{
    public function __construct(
        public PublicKeyCredentialRpEntity $relyingParty,
    ) {}
}
