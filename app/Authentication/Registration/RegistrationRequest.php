<?php

declare(strict_types=1);

namespace App\Authentication\Registration;

use App\Validation\IsUnique;
use Tempest\Http\IsRequest;
use Tempest\Http\Request;
use Tempest\Validation\Rules\IsEmail;

final class RegistrationRequest implements Request
{
    use IsRequest;

    #[IsEmail, IsUnique('users', 'email')]
    public string $email;
}
