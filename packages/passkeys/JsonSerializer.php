<?php

declare(strict_types=1);

namespace Passkeys;

use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\Denormalizer\WebauthnSerializerFactory;

final readonly class JsonSerializer
{
    public static function serialize(mixed $data): string
    {
        return new WebauthnSerializerFactory(AttestationStatementSupportManager::create())
            ->create()
            ->serialize($data, format: 'json');
    }

    /**
     * @template T
     *
     * @param class-string<T> $into
     * @return T
     */
    public static function deserialize(string $json, string $into): mixed
    {
        return new WebauthnSerializerFactory(AttestationStatementSupportManager::create())
            ->create()
            ->deserialize(
                data: $json,
                type: $into,
                format: 'json',
            );
    }
}
