<?php

declare(strict_types=1);

namespace Passkeys;

use JsonSerializable;
use Webauthn\PublicKeyCredentialCreationOptions;

use function Tempest\Support\Json\decode;

final class CredentialCreationOptions implements JsonSerializable
{
    public function __construct(
        private(set) PublicKeyCredentialCreationOptions $options,
    ) {}

    public function jsonSerialize(): array
    {
        /** @var array */
        return decode(JsonSerializer::serialize($this->options));
    }

    public function __serialize(): array
    {
        return ['options' => JsonSerializer::serialize($this->options)];
    }

    /**
     * @param array{options:string} $data
     */
    public function __unserialize(array $data): void
    {
        $this->options = JsonSerializer::deserialize($data['options'], PublicKeyCredentialCreationOptions::class);
    }
}
