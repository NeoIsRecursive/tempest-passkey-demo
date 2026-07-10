<?php

declare(strict_types=1);

namespace App\Authentication\Webauthn;

use App\Authentication\BelongsToUser;
use App\Authentication\MustBeAuthenticated;
use App\Authentication\User;
use NeoIsRecursive\Inertia\Http\Component;
use Passkeys\PasskeyService;
use Tempest\Database\Direction;
use Tempest\Http\Responses\Back;
use Tempest\Http\Responses\Created;
use Tempest\Http\Responses\Json;
use Tempest\Router\Delete;
use Tempest\Router\Get;
use Tempest\Router\Post;
use Webauthn\PublicKeyCredentialUserEntity;

use function Tempest\Database\query;
use function Tempest\DateTime\now;

final readonly class PasskeyController
{
    #[Post('/webauthn/creation-options'), MustBeAuthenticated]
    public function registerOptions(User $user, PasskeyService $passkey): Json
    {
        $options = $passkey->creationOptions(user: new PublicKeyCredentialUserEntity(
            name: $user->email,
            id: (string) $user->id->value,
            displayName: $user->email,
        ));

        return new Json($options);
    }

    #[Post('/webauthn/attest'), MustBeAuthenticated]
    public function attest(StorePasskeyRequest $request, User $user, PasskeyService $passkey): Created
    {
        $credentialRecord = $passkey->attest($request->passkey, $request);

        $defaultName = $passkey->getAuthenticatorName($credentialRecord);

        query(Passkey::class)
            ->insert(
                user_id: $user->id->value,
                name: $defaultName ?? 'Unknown Authenticator',
                credential_id: $credentialRecord->publicKeyCredentialId,
                data: $credentialRecord,
                updated_at: now(),
                created_at: now(),
            )
            ->execute();

        return new Created();
    }

    #[Get('/webauthn/passkeys'), MustBeAuthenticated]
    public function index(User $user): Component
    {
        $passkeys = query(Passkey::class)
            ->select()
            ->transform(new BelongsToUser($user))
            ->orderBy('updated_at', Direction::DESC)
            ->all();

        return new Component('passkeys', [
            'credentials' => $passkeys,
        ]);
    }

    #[Delete('/webauthn/passkeys/{id}'), MustBeAuthenticated]
    public function destroy(User $user, string $id): Back
    {
        query(Passkey::class)->delete()->where('id', $id)->transform(new BelongsToUser($user))->execute();

        return new Back();
    }
}
