<?php

declare(strict_types=1);

use App\Logging\SocketLogChannel;
use Tempest\Log\Config\SimpleLogConfig;

use function Tempest\env;
use function Tempest\internal_storage_path;

return new SimpleLogConfig(
    path: internal_storage_path('logs', 'tempest.log'),
    channels: [
        new SocketLogChannel(env('LARAVEL_CLOUD_LOG_SOCKET', 'unix:///tmp/cloud-init.sock')),
    ],
);
