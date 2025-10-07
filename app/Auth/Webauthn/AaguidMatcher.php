<?php

declare(strict_types=1);

namespace App\Auth\Webauthn;

use Tempest\Container\Singleton;

use function Tempest\Support\Filesystem\read_json;

#[Singleton]
final class AaguidMatcher
{
    public function __construct(
        private array $map = [],
    ) {
        $this->map = read_json(__DIR__ . '/aaguid.json');
    }

    public function getName(string $aaguid): ?string
    {
        return $this->map[$aaguid]['name'] ?? null;
    }
}
