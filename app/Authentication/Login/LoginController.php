<?php

declare(strict_types=1);

namespace App\Authentication\Login;

use App\Authentication\MustBeGuest;
use App\Authentication\User;
use App\Authentication\Webauthn\AuthenticatePasskeyRequest;
use App\Authentication\Webauthn\Passkey;
use App\DashboardController;
use NeoIsRecursive\Inertia\Http\Component;
use Passkeys\PasskeyService;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\Http\Responses\Json;
use Tempest\Router\Get;
use Tempest\Router\Post;
use Tempest\Validation\Exceptions\ValidationFailed;

use function Tempest\Database\query;
use function Tempest\DateTime\now;
use function Tempest\Router\uri;

#[MustBeGuest]
final readonly class LoginController
{
    #[Get('/login')]
    public function view(): Component
    {
        return new Component('login');
    }

    #[Get('/login/authentication-options')]
    public function authenticationOptions(PasskeyService $passkey): Json
    {
        $options = $passkey->authenticateOptions();

        return new Json($options);
    }

    #[Post('/login/authenticate')]
    public function authenticate(AuthenticatePasskeyRequest $request, PasskeyService $passkey, Authenticator $authenticator): Json
    {
        $credentialRecord = $passkey->authenticate(
            $request->answer,
            findPasskeyByCredentialId: static fn (string $id) => query(Passkey::class)->find(credential_id: $id)->first()?->data,
        );

        query(Passkey::class)
            ->update(data: $credentialRecord, updated_at: now())
            ->whereField('credential_id', $credentialRecord->getAttestedCredentialData()->credentialId)
            ->execute();

        /** @var ?User */
        $user = query(User::class)->findById($credentialRecord->userHandle);

        if (! $user) {
            throw ValidationFailed::withMessages([
                'answer' => 'This credential does not exist',
            ]);
        }

        $authenticator->authenticate($user);

        return new Json([
            'redirectTo' => uri(DashboardController::class),
        ]);
    }
}
