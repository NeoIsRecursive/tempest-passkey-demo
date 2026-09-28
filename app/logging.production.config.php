<?php

declare(strict_types=1);

use App\Logging\SocketLogChannel;
use Tempest\Log\Config\MultipleChannelsLogConfig;

use function Tempest\env;


/** @var string */
$connectionString = env("LARAVEL_CLOUD_LOG_SOCKET", "unix:///tmp/cloud-init.sock");

return new MultipleChannelsLogConfig(
    prefix: null,
    channels: [
        new SocketLogChannel($connectionString),
    ],
);
