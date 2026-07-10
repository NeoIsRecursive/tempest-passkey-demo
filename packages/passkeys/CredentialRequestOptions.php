<?php

declare(strict_types=1);

namespace Passkeys;

use JsonSerializable;
use Webauthn\PublicKeyCredentialRequestOptions;

use function Tempest\Support\Json\decode;

final class CredentialRequestOptions implements JsonSerializable
{
    public function __construct(
        private(set) PublicKeyCredentialRequestOptions $options,
    ) {}

    public function jsonSerialize(): array
    {
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
        $this->options = JsonSerializer::deserialize($data['options'], PublicKeyCredentialRequestOptions::class);
    }
}
