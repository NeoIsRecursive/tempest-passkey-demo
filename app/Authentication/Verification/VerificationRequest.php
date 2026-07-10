<?php

declare(strict_types=1);

namespace App\Authentication\Verification;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\HasLength;

final class VerificationRequest implements Request
{
    use IsRequest;

    #[HasLength(min: 6, max: 6)]
    public string $code;

    public ?string $email;
}
