<?php

declare(strict_types=1);

namespace App;

use App\Auth\GenerateWebauthnOptions;
use App\Auth\Requests\StartRegistration;
use App\Auth\SessionChallengeManager;
use App\Auth\WebauthnConfig;
use Exception;
use Firehed\WebAuthn\ArrayBufferResponseParser;
use Firehed\WebAuthn\Codecs\Credential;
use Firehed\WebAuthn\CredentialContainer;
use Firehed\WebAuthn\ExpiringChallenge;
use NeoIsRecursive\Inertia\Http\InertiaResponse;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\DateTime\DateTime;
use Tempest\Http\HttpRequestFailed;
use Tempest\Http\Request;
use Tempest\Http\Responses\Forbidden;
use Tempest\Http\Responses\Json;
use Tempest\Http\Responses\Redirect;
use Tempest\Http\Session\Session;
use Tempest\Http\Status;
use Tempest\Router\Get;
use Tempest\Router\Post;

use function NeoIsRecursive\Inertia\inertia;
use function Tempest\Database\query;
use function Tempest\env;
use function Tempest\Router\uri;
use function Tempest\Support\arr;
use function Tempest\Support\Random\uuid;

final readonly class AuthController
{
    #[Get('/login')]
    public function login(Authenticator $auth): InertiaResponse|Redirect
    {
        if ($auth->current()) {
            return new Redirect(uri([DashboardController::class, 'index']));
        }

        return inertia('auth');
    }

    #[Post('/auth/register/options')]
    public function registrationOptions(StartRegistration $request, GenerateWebauthnOptions $webauthn)
    {
        $user = query(User::class)->find(email: $request->email)->first();

        if ($user !== null) {
            throw new HttpRequestFailed(
                request: $request,
                status: Status::CONFLICT,
                message: 'A user with that email already exists',
            );
        }

        $data = $webauthn->forRegistration(
            userUuid: uuid(),
            email: $request->email,
        );

        return new Json($data);
    }

    #[Post('/auth/register/complete')]
    public function completeRegistration(Request $request, Authenticator $authenticator, GenerateWebauthnOptions $webauthn)
    {
        $data = $webauthn->verifyRegistration($request->body);

        $user = query(User::class)
            ->create(
                uuid: $data['userId'],
                email: $data['email'],
                created_at: DateTime::now(),
                updated_at: DateTime::now(),
            );

        $authenticator->authenticate($user);

        query(Passkey::class)
            ->create(
                user_id: (int) $user->id->value,
                credential_id: $data['credential_id'],
                public_key: $data['public_key'],
                created_at: DateTime::now(),
                updated_at: DateTime::now(),
            );

        return new Json([
            'redirectUri' => uri([DashboardController::class, 'index']),
        ]);
    }

    #[Post('/auth/login/options')]
    public function loginOptions(Request $request, Session $session)
    {
        $user = query(User::class)
            ->find(email: $request->get('email'))
            ->with('passkeys')
            ->first();

        if ($user === null) {
            return new Forbidden();
        }

        $session->set(WebauthnConfig::USER_UUID_SESSION_KEY, $user->uuid);

        $codec = new Credential();
        $challengeManager = new SessionChallengeManager($session);

        $credentials = arr($user->passkeys)->map(
            fn (Passkey $key) => $codec->decode($key->public_key),
        );

        $credentialsContainer = new CredentialContainer($credentials->toArray());

        // Generate and manage challenge
        $challenge = ExpiringChallenge::withLifetime(300);
        $challengeManager->manageChallenge($challenge);

        return new Json([
            'challengeB64' => $challenge->getBase64(),
            'credential_ids' => $credentialsContainer->getBase64Ids(),
        ]);
    }

    #[Post('/auth/login/complete')]
    public function completeLogin(Request $request, Session $session, Authenticator $authenticator)
    {
        $parser = new ArrayBufferResponseParser();
        $getResponse = $parser->parseGetResponse($request->body);
        $userUuid = $getResponse->getUserHandle();

        $codec = new Credential();
        $challengeManager = new SessionChallengeManager($session);
        $rp = new \Firehed\WebAuthn\SingleOriginRelyingParty(env('BASE_URI'));

        if ($userUuid !== null && $userUuid !== $session->consume(WebauthnConfig::USER_UUID_SESSION_KEY)) {
            throw new Exception('User handle does not match authentcating user');
        }

        $user = query(User::class)
            ->find(uuid: $userUuid)
            ->with('passkeys')
            ->first();

        $credentials = arr($user->passkeys)->map(
            fn (Passkey $key) => $codec->decode($key->public_key),
        );

        $credentialContainer = new CredentialContainer($credentials->toArray());

        $updatedCredential = $getResponse->verify($challengeManager, $rp, $credentialContainer);

        // Update the credential
        $encodedCredential = $codec->encode($updatedCredential);

        query(Passkey::class)
            ->update(
                public_key: $encodedCredential,
                updated_at: DateTime::now(),
            )
            ->whereField('credential_id', $updatedCredential->getStorageId())
            ->whereField('user_id', $user->id->value)
            ->execute();

        $authenticator->authenticate($user);

        return new Json([
            'redirectUri' => uri([DashboardController::class, 'index']),
        ]);
    }

    #[Post('/auth/logout')]
    public function logout(Authenticator $authenticator)
    {
        $authenticator->deauthenticate();

        return new Redirect('/');
    }
}
