<?php

declare(strict_types=1);

namespace App\Authentication\Webauthn;

use App\Authentication\User;
use JsonSerializable;
use Passkeys\CredentialRecordCaster;
use Passkeys\CredentialRecordSerializer;
use Tempest\Database\PrimaryKey;
use Tempest\DateTime\DateTime;
use Tempest\Mapper\CastWith;
use Tempest\Mapper\SerializeWith;
use Webauthn\CredentialRecord;

final class Passkey implements JsonSerializable
{
    public PrimaryKey $id;

    public User $user;

    public function __construct(
        public string $name,
        public int $user_id,
        public string $credential_id,
        #[CastWith(CredentialRecordCaster::class), SerializeWith(CredentialRecordSerializer::class)]
        public CredentialRecord $data,
        public DateTime $created_at,
        public DateTime $updated_at,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id->value,
            'name' => $this->name,
            'credential_id' => base64_encode($this->credential_id),
            'aaguid' => $this->data->aaguid,
            'created_at' => $this->created_at->toRfc3339(),
            'updated_at' => $this->updated_at->toRfc3339(),
        ];
    }
}
