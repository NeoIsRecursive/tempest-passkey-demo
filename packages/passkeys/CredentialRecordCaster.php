<?php

declare(strict_types=1);

namespace Passkeys;

use Override;
use Tempest\Mapper\Caster;
use Webauthn\CredentialRecord;

final readonly class CredentialRecordCaster implements Caster
{
    #[Override]
    public function cast(mixed $input): CredentialRecord
    {
        if ($input instanceof CredentialRecord) {
            return $input;
        }

        if (is_string($input)) {
            return JsonSerializer::deserialize($input, CredentialRecord::class);
        }

        throw new \InvalidArgumentException('Cannot cast to CredentialRecord');
    }
}
