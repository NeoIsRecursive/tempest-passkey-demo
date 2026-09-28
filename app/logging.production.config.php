<?php

declare(strict_types=1);

use App\Logging\SocketLogChannel;
use Tempest\Log\Config\SimpleLogConfig;

use function Tempest\internal_storage_path;

return new SimpleLogConfig(
    path: internal_storage_path('logs'),
    channels: [
        new SocketLogChannel('unix://tmp/cloud-init.sock'),
    ],
);
