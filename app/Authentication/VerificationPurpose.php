<?php

declare(strict_types=1);

namespace App\Authentication;

enum VerificationPurpose: string
{
    case EmailVerification = 'email_verification';
    case Recovery = 'recovery';
}
