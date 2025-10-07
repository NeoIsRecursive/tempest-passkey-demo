<?php

declare(strict_types=1);

namespace App\Auth\Webauthn;

use App\Auth\SessionChallengeManager;
use App\Auth\Webauthn\RegistrationCompleted;
use App\Auth\WebauthnConfig;
use Exception;
use Firehed\WebAuthn\ArrayBufferResponseParser;
use Firehed\WebAuthn\Codecs\Credential;
use Firehed\WebAuthn\ExpiringChallenge;
use Firehed\WebAuthn\SingleOriginRelyingParty;
use Tempest\Http\Session\Session;

final readonly class RegisterPasskey
{
    public function __construct(
        private SessionChallengeManager $challengeManager,
        private WebauthnConfig $config,
        private Session $session,
    ) {}

    public function start(string $userUuid, string $email): array
    {
        $this->session->set('registration_data', [
            'userId' => $userUuid,
            'email' => $email,
        ]);

        $this->session->set(WebauthnConfig::USER_UUID_SESSION_KEY, $userUuid);

        $challengeManager = new SessionChallengeManager($this->session);

        $challenge = ExpiringChallenge::withLifetime(300);
        $challengeManager->manageChallenge($challenge);

        return [
            'challenge' => $challenge->getBase64Url(),
            'rp' => [
                'name' => $this->config->relyingPartyName,
                'id' => parse_url($this->config->relyingPartyId, PHP_URL_HOST),
            ],
            'user' => [
                'id' => base64_encode($userUuid),
                'name' => $email,
                'displayName' => $email,
            ],
            'pubKeyCredParams' => [
                ['type' => 'public-key', 'alg' => -7], // ES256
                ['type' => 'public-key', 'alg' => -257], // RS256
            ],
            'timeout' => 60000,
            'attestation' => 'none',
            'authenticatorSelection' => [
                'residentKey' => 'required',
                'userVerification' => 'required',
            ],
        ];
    }

    public function complete(array $body)
    {
        $data = $this->session->get('registration_data');

        if ($data === null) {
            throw new Exception('Missing registration data');
        }

        $parser = new ArrayBufferResponseParser();
        $createResponse = $parser->parseCreateResponse($body);

        $rp = new SingleOriginRelyingParty($this->config->relyingPartyId);
        $challengeManager = new SessionChallengeManager($this->session);

        $credential = $createResponse->verify($challengeManager, $rp);

        $codec = new Credential();
        $encodedCredential = $codec->encode($credential);

        $this->session->remove('registration_data');

        return new RegistrationCompleted(
            userUuid: $data['userId'],
            email: $data['email'],
            publicKey: $encodedCredential,
            credentialId: $credential->getStorageId(),
        );
    }
}
