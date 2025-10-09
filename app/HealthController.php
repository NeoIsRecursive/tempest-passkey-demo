<?php

declare(strict_types=1);

namespace App;

use Tempest\Http\Responses\Ok;
use Tempest\Router\Get;

final readonly class HealthController
{
    #[Get('/health')]
    public function __invoke(): Ok
    {
        return new Ok(['message' => 'im healthy']);
    }
}
