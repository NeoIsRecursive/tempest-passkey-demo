<?php

declare(strict_types=1);

namespace App\Auth;

use App\Auth\Requests\StartRegistration;
use App\Auth\Webauthn\LoginPasskey;
use App\Auth\Webauthn\RegisterPasskey;
use App\DashboardController;
use App\Passkey;
use App\User;
use NeoIsRecursive\Inertia\Http\InertiaResponse;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\DateTime\DateTime;
use Tempest\Http\HttpRequestFailed;
use Tempest\Http\Request;
use Tempest\Http\Responses\Forbidden;
use Tempest\Http\Responses\Json;
use Tempest\Http\Responses\Redirect;
use Tempest\Http\Status;
use Tempest\Router\Get;
use Tempest\Router\Post;

use function NeoIsRecursive\Inertia\inertia;
use function Tempest\Database\query;
use function Tempest\Router\uri;
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

    #[Post('/auth/logout')]
    public function logout(Authenticator $authenticator)
    {
        $authenticator->deauthenticate();

        return new Redirect('/');
    }

    #[Post('/auth/register/options')]
    public function registrationOptions(StartRegistration $request, RegisterPasskey $webauthn)
    {
        $user = query(User::class)->find(email: $request->email)->first();

        if ($user !== null) {
            throw new HttpRequestFailed(
                request: $request,
                status: Status::CONFLICT,
                message: 'A user with that email already exists',
            );
        }

        $data = $webauthn->start(
            userUuid: uuid(),
            email: $request->email,
        );

        return new Json($data);
    }

    #[Post('/auth/register/complete')]
    public function completeRegistration(Request $request, Authenticator $authenticator, RegisterPasskey $webauthn)
    {
        $data = $webauthn->complete($request->body);

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
    public function loginOptions(Request $request, LoginPasskey $login)
    {
        $user = query(User::class)
            ->find(email: $request->get('email'))
            ->with('passkeys')
            ->first();

        if ($user === null) {
            return new Forbidden();
        }

        $data = $login->start($user);

        return new Json($data->jsonSerialize());
    }

    #[Post('/auth/login/complete')]
    public function completeLogin(Request $request, LoginPasskey $login, Authenticator $authenticator)
    {
        $data = $login->complete($request->body);

        $authenticator->authenticate($data->user);

        query(Passkey::class)
            ->update(
                public_key: $data->publicKey,
                updated_at: DateTime::now(),
            )
            ->whereField('credential_id', $data->credential->getStorageId())
            ->whereField('user_id', $data->user->id->value)
            ->execute();

        return new Json([
            'redirectUri' => uri([DashboardController::class, 'index']),
        ]);
    }
}
