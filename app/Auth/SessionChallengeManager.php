<?php

declare(strict_types=1);

namespace App\Auth;

use Firehed\WebAuthn\ChallengeInterface;
use Firehed\WebAuthn\ChallengeManagerInterface;
use Tempest\Http\Session\Session;

class SessionChallengeManager implements ChallengeManagerInterface
{
    private const string SESSION_KEY = 'passkey_challenge';

    public function __construct(
        private Session $session,
    ) {}

    public function manageChallenge(ChallengeInterface $c): void
    {
        $this->session->set(self::SESSION_KEY, $c);
    }

    public function useFromClientDataJSON(string $base64Url): ?ChallengeInterface
    {
        $challenge = $this->session->consume(self::SESSION_KEY);

        assert($challenge instanceof ChallengeInterface);

        // Validate that the stored challenge matches the CDJ value
        if ($challenge->getBase64Url() === $base64Url) {
            return $challenge;
        }

        return null;
    }
}
