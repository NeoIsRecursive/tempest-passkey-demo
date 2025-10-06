<?php

declare(strict_types=1);

namespace App\Auth\Webauthn;

use Firehed\WebAuthn\ChallengeInterface;
use Firehed\WebAuthn\CredentialContainer;
use JsonSerializable;

use function Tempest\Support\arr;

final readonly class LoginStarted implements JsonSerializable
{
    /**
     * @param $credentialIds string[]
     */
    public function __construct(
        public ChallengeInterface $challenge,
        public CredentialContainer $credentials,
    ) {}

    private static function credentialBase64ToUrl(string $id): string
    {
        return rtrim(strtr($id, '+/', '-_'), '=');
    }

    public function jsonSerialize(): mixed
    {
        return [
            'challenge' => $this->challenge->getBase64Url(),
            'allowCredentials' => arr($this->credentials->getBase64Ids())->map(fn (string $cred) => [
                'type' => 'public-key',
                'id' => static::credentialBase64ToUrl($cred),
            ])->toArray(),
        ];
    }
}
