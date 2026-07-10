<?php

declare(strict_types=1);

namespace App\Authentication\Registration;

use App\Authentication\SendVerificationCode;
use App\Authentication\User;
use App\Authentication\Verification\VerificationRequest;
use App\Authentication\VerificationPurpose;
use App\Authentication\VerifyVerificationCode;
use App\DashboardController;
use Exception;
use NeoIsRecursive\Inertia\Http\Component;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Http\Responses\Back;
use Tempest\Http\Responses\Redirect;
use Tempest\Http\Session\Session;
use Tempest\Router\Get;
use Tempest\Router\Post;

use function NeoIsRecursive\Inertia\inertia;
use function Tempest\Database\query;
use function Tempest\DateTime\now;
use function Tempest\Router\uri;

final readonly class RegistrationController
{
    #[Get('/register')]
    public function view(): Component
    {
        return inertia('register');
    }

    #[Post('/authentication/register')]
    public function create(RegistrationRequest $request, SendVerificationCode $sendVerificationCode): Redirect
    {
        $sendVerificationCode($request->email, VerificationPurpose::EmailVerification);

        return new Redirect(uri([self::class, 'verifyForm']));
    }

    #[Post('/authentication/registration/resend-code')]
    public function resendVerificationCode(Session $session, SendVerificationCode $sendVerificationCode): Back
    {
        $email = $session->get('email');

        if (! $email || ! is_string($email)) {
            throw new Exception('No registration in progress');
        }

        $sendVerificationCode($email, VerificationPurpose::EmailVerification);

        return new Back();
    }

    #[Get('/authentication/verify')]
    public function verifyForm(): Component
    {
        return inertia('verify-email');
    }

    #[Post('/authentication/verify')]
    public function verify(
        VerificationRequest $request,
        Session $session,
        Authenticator $authenticator,
        VerifyVerificationCode $verifyVerificationCode,
    ): Redirect {
        $email = $request->email ?? $session->get('email');

        if (! $email || ! is_string($email)) {
            throw new Exception('No email provided');
        }

        $verificationCode = $verifyVerificationCode(
            email: $email,
            code: $request->code,
            purpose: VerificationPurpose::EmailVerification,
        );

        /** @var User */
        $user = query(User::class)->create(
            email: $verificationCode->email,
            email_verified_at: now(),
        );

        $authenticator->authenticate($user);

        return new Redirect(uri(DashboardController::class));
    }
}
