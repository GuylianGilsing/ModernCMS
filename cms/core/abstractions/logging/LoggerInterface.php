<?php

declare(strict_types=1);

namespace ModernCMS\Core\Abstractions\Logging;

interface LoggerInterface
{
    /**
     * Log debug messages.
     */
    public function debug(string $message, mixed $contextData = []): void;

    /**
     * Log interesting events.
     */
    public function info(string $message, mixed $contextData = []): void;

    /**
     * Log events that are normal, but significant.
     */
    public function notice(string $message, mixed $contextData = []): void;

    /**
     * Log events that can pose a threat to how the system should operate. Some examples are: usage of deprecated functions or
     * APIs, unexpected outcomes that aren't necessarily wrong, etc.
     */
    public function warning(string $message, mixed $contextData = []): void;

    /**
     * Log error events that don't require immediate action.
     */
    public function error(string $message, mixed $contextData = []): void;

    /**
     * Log error events that require immediate action.
     */
    public function critical(string $message, mixed $contextData = []): void;

    /**
     * Log emergency events.
     */
    public function emergency(string $message, mixed $contextData = []): void;
}
