<?php

declare(strict_types=1);

use App\Auth\WebauthnConfig;
use Firehed\WebAuthn\SingleOriginRelyingParty;

use function Tempest\env;

return new WebauthnConfig(
    relyingPartyName: 'Tempest Passkey App',
    relyingPartyId: env('BASE_URI'),
);
