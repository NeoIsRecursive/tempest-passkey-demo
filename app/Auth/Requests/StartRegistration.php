<?php

declare(strict_types=1);

namespace App\Auth\Requests;

use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsEmail;

final class StartRegistration implements Request
{
    use IsRequest;

    #[IsEmail]
    public string $email;
}
