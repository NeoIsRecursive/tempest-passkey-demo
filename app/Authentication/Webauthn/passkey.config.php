<?php

declare(strict_types=1);

use Passkeys\PasskeyConfig;
use Webauthn\PublicKeyCredentialRpEntity;

use function Tempest\env;

return new PasskeyConfig(
    relyingParty: new PublicKeyCredentialRpEntity(
        id: new Uri\WhatWg\Url((string) env('BASE_URI'))->getUnicodeHost(),
        name: 'Random',
    ),
);
