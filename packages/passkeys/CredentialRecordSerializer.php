<?php

declare(strict_types=1);

namespace Passkeys;

use Override;
use Tempest\Mapper\Serializer;
use Webauthn\CredentialRecord;

final readonly class CredentialRecordSerializer implements Serializer
{
    #[Override]
    public function serialize(mixed $input): string
    {
        if ($input instanceof CredentialRecord) {
            return JsonSerializer::serialize($input);
        }

        throw new \InvalidArgumentException('Cannot serialize CredentialRecord');
    }
}
