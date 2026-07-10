<?php

declare(strict_types=1);

namespace App\Authentication\Login;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsEmail;

final class LoginRequest implements Request
{
    use IsRequest;

    #[IsEmail]
    public string $email;
}
