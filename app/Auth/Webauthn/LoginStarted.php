<?php

declare(strict_types=1);

namespace App\Auth\Webauthn;

use Firehed\WebAuthn\ChallengeInterface;
use JsonSerializable;

final readonly class LoginStarted implements JsonSerializable
{
    public function __construct(
        public ChallengeInterface $challenge,
        public string $relyingPartyId,
    ) {}

    public function jsonSerialize(): mixed
    {
        return [
            'challenge' => $this->challenge->getBase64Url(),
            'rpId' => parse_url($this->relyingPartyId, PHP_URL_HOST),
            'timeout' => 60_000,
            'userVerification' => 'required',
        ];
    }
}
