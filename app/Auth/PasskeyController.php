<?php

declare(strict_types=1);

namespace App\Auth;

use App\Auth\Webauthn\RegisterPasskey;
use App\Passkey;
use App\User;
use Tempest\Auth\Authentication\Authenticator;
use Tempest\DateTime\DateTime;
use Tempest\Http\Request;
use Tempest\Http\Responses\Back;
use Tempest\Http\Responses\Forbidden;
use Tempest\Http\Responses\Json;
use Tempest\Router\Delete;
use Tempest\Router\Post;

use function Tempest\Database\query;

final readonly class PasskeyController
{
    #[Post('/auth/passkeys/add-start')]
    public function addOptions(RegisterPasskey $registration, Authenticator $auth)
    {
        /** @var User */
        $user = $auth->current();

        if (! $user) {
            return new Forbidden();
        }

        $data = $registration->start(
            email: $user->email,
            userUuid: $user->uuid,
        );

        return new Json($data);
    }

    #[Post('/auth/passkeys/add-complete')]
    public function addComplete(Request $request, RegisterPasskey $registration, Authenticator $auth)
    {
        /** @var User */
        $user = $auth->current();

        if (! $user) {
            return new Forbidden();
        }

        $data = $registration->complete($request->body);

        query(Passkey::class)
            ->create(
                user_id: (int) $user->id->value,
                credential_id: $data->credentialId,
                public_key: $data->publicKey,
                created_at: DateTime::now(),
                updated_at: DateTime::now(),
                aaguid: $data->aaguid,
            );

        return new Json(['success' => true]);
    }

    #[Delete('/auth/passkey/{id}')]
    public function remove(int $id, Authenticator $auth)
    {
        /** @var User */
        $user = $auth->current();

        if (! $user) {
            return new Forbidden();
        }

        query(Passkey::class)
            ->delete()
            ->whereField('user_id', $user->id)
            ->whereField('id', $id)
            ->execute();

        return new Back();
    }
}
