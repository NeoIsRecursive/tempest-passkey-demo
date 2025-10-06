<?php

declare(strict_types=1);

namespace App\Auth;

use App\Passkey;
use App\User;
use Exception;
use Firehed\WebAuthn\ArrayBufferResponseParser;
use Firehed\WebAuthn\Codecs\Credential;
use Firehed\WebAuthn\CredentialContainer;
use Firehed\WebAuthn\ExpiringChallenge;
use Firehed\WebAuthn\SingleOriginRelyingParty;
use Tempest\Http\Session\Session;

use function Tempest\Database\query;
use function Tempest\Support\arr;

final class LoginPasskey
{
    public function __construct(
        private Session $session,
        private WebauthnConfig $config,
    ) {}

    public function start(User $user)
    {
        $this->session->set(WebauthnConfig::USER_UUID_SESSION_KEY, $user->uuid);

        $codec = new Credential();
        $challengeManager = new SessionChallengeManager($this->session);

        $credentials = arr($user->passkeys)->map(
            fn (Passkey $key) => $codec->decode($key->public_key),
        );

        $credentialsContainer = new CredentialContainer($credentials->toArray());

        // Generate and manage challenge
        $challenge = ExpiringChallenge::withLifetime(300);
        $challengeManager->manageChallenge($challenge);

        return [
            'challengeB64' => $challenge->getBase64(),
            'credential_ids' => $credentialsContainer->getBase64Ids(),
        ];
    }

    public function complete(array $requestBody)
    {
        $parser = new ArrayBufferResponseParser();
        $getResponse = $parser->parseGetResponse($requestBody);

        $userUuid = $getResponse->getUserHandle();

        $codec = new Credential();
        $challengeManager = new SessionChallengeManager($this->session);
        $rp = new SingleOriginRelyingParty($this->config->relyingPartyId);

        if ($userUuid !== null && $userUuid !== $this->session->consume(WebauthnConfig::USER_UUID_SESSION_KEY)) {
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

        return [
            'user' => $user,
            'credential' => $updatedCredential,
            'encodedCredential' => $encodedCredential,
        ];
    }
}
