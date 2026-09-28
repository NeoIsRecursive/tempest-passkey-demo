<?php

declare(strict_types=1);

namespace App\Logging;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\SocketHandler;
use Monolog\Level;
use Monolog\Processor\PsrLogMessageProcessor;
use Tempest\Log\LogChannel;
use Tempest\Log\LogLevel;

final readonly class SocketLogChannel implements LogChannel
{
    /**
     * @param LogLevel $minimumLogLevel The minimum log level to record.
     * @param bool $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(
        private(set) string $connectionString,
        private(set) LogLevel $minimumLogLevel = LogLevel::DEBUG,
        private(set) bool $bubble = true,
    ) {}

    public function getHandlers(Level $level): array
    {
        if (! $this->minimumLogLevel->includes(LogLevel::fromMonolog($level))) {
            return [];
        }

        return [
            new SocketHandler(
                connectionString: $this->connectionString,
                level: $level,
                bubble: $this->bubble,
                persistent: false,
                timeout: 2,
                writingTimeout: 10,
                connectionTimeout: 2,
            )->setFormatter(new JsonFormatter()),
        ];
    }

    public function getProcessors(): array
    {
        return [
            new PsrLogMessageProcessor(),
        ];
    }
}
