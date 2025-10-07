<?php

declare(strict_types=1);

namespace App;

use App\Auth\Webauthn\AaguidMatcher;
use JsonSerializable;
use Tempest\Database\PrimaryKey;
use Tempest\DateTime\DateTime;

use function Tempest\get;

final class Passkey implements JsonSerializable
{
    public PrimaryKey $id;
    public int $user_id;
    public string $credential_id;
    public string $public_key;
    public ?string $aaguid;

    public DateTime $created_at;
    public DateTime $updated_at;

    public function jsonSerialize(): array
    {
        return [
            'id' => (int) $this->id->value,
            'user_id' => $this->user_id,
            'credential_id' => $this->credential_id,
            'provider' => $this->aaguid ? get(AaguidMatcher::class)->getName($this->aaguid) : null,
            'created_at' => $this->created_at->toRfc3339(),
            'updated_at' => $this->updated_at->toRfc3339(),
        ];
    }
}
