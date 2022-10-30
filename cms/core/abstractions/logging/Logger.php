<?php

declare(strict_types=1);

namespace ModernCMS\Core\Abstractions\Logging;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Psr\Log\LogLevel;

class Logger implements LoggerInterface
{
    protected MonologLogger $logger;

    public function __construct(string $filePath, string $fileName)
    {
        $logFilePath = $filePath.'/'.$fileName;

        $libLogger = new MonologLogger('CMS');
        $handler = new StreamHandler($logFilePath, LogLevel::DEBUG);

        $libLogger->pushHandler($handler);

        $this->logger = $libLogger;
    }

    public function debug(string $message, mixed $contextData = []): void
    {
        $this->logger->debug($message, $contextData);
    }

    public function info(string $message, mixed $contextData = []): void
    {
        $this->logger->info($message, $contextData);
    }

    public function notice(string $message, mixed $contextData = []): void
    {
        $this->logger->notice($message, $contextData);
    }

    public function warning(string $message, mixed $contextData = []): void
    {
        $this->logger->warning($message, $contextData);
    }

    public function error(string $message, mixed $contextData = []): void
    {
        $this->logger->error($message, $contextData);
    }

    public function critical(string $message, mixed $contextData = []): void
    {
        $this->logger->critical($message, $contextData);
    }

    public function emergency(string $message, mixed $contextData = []): void
    {
        $this->logger->emergency($message, $contextData);
    }
}
