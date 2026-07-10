<?php

declare(strict_types=1);

use Tempest\DateTime\Duration;
use Tempest\Http\Session\CleanupStrategy;
use Tempest\Http\Session\Config\DatabaseSessionConfig;

return new DatabaseSessionConfig(
    expiration: Duration::minutes(30),
    cleanupStrategy: CleanupStrategy::RANDOM_REQUESTS,
);
