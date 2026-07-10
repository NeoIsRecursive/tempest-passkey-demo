<?php

declare(strict_types=1);

namespace App\Authentication\Webauthn;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsJsonString;

final class AuthenticatePasskeyRequest implements Request
{
    use IsRequest;

    #[IsJsonString]
    public string $answer;
}
