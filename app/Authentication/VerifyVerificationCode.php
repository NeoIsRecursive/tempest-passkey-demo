<?php

declare(strict_types=1);

namespace App\Authentication;

use Tempest\Cryptography\Password\PasswordHasher;

use function Tempest\Database\query;
use function Tempest\DateTime\now;

final readonly class VerifyVerificationCode
{
    public function __construct(
        private PasswordHasher $hasher,
    ) {}

    /**
     * @throws \Exception
     */
    public function __invoke(string $email, string $code, VerificationPurpose $purpose): VerificationCode
    {
        /** @var ?VerificationCode */
        $verificationCode = query(VerificationCode::class)
            ->find(email: $email, purpose: $purpose)
            ->whereNull('consumed_at')
            ->whereAfter('expires_at', now())
            ->first();

        if (! $verificationCode) {
            throw new \Exception('No matching code found');
        }

        if ($verificationCode->expires_at->isPast()) {
            throw new \Exception('Code expired');
        }

        if (! $this->hasher->verify($code, $verificationCode->code)) {
            throw new \Exception('Invalid code');
        }

        query(VerificationCode::class)
            ->update(consumed_at: now())
            ->whereField('id', $verificationCode->id->value)
            ->execute();

        return $verificationCode;
    }
}
