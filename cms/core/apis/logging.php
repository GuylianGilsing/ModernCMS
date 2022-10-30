<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\Logging;

use DateTime;
use ModernCMS\Core\Abstractions\Logging\Logger;
use ModernCMS\Core\Abstractions\Logging\LoggerInterface;

function get_log_directory_path(): string
{
    return CMS_BASE_DIR.'/logs';
}

function create_logging_directory_if_not_exists(): void
{
    $path = get_log_directory_path();

    if (!file_exists($path))
    {
        mkdir($path);
    }
}

function get_logger_instance(): ?LoggerInterface
{
    static $logger;

    if (!DO_LOGGING)
    {
        return null;
    }

    if (!isset($logger))
    {
        $logFilePath = get_log_directory_path();
        $logFileName = (new DateTime())->format('Y-m-d').'.log';

        $logger = new Logger($logFilePath, $logFileName);
    }

    return $logger;
}

/**
 * Log debug messages.
 */
function log_debug_message(string $message, mixed $contextData = []): void
{
    get_logger_instance()?->debug($message, $contextData);
}

/**
 * Log interesting events.
 */
function log_info_message(string $message, mixed $contextData = []): void
{
    get_logger_instance()?->info($message, $contextData);
}

/**
 * Log events that are normal, but significant.
 */
function log_notice_message(string $message, mixed $contextData = []): void
{
    get_logger_instance()?->notice($message, $contextData);
}

/**
 * Log events that can pose a threat to how the system should operate. Some examples are: usage of deprecated functions or
 * APIs, unexpected outcomes that aren't necessarily wrong, etc.
 */
function log_warning_message(string $message, mixed $contextData = []): void
{
    get_logger_instance()?->warning($message, $contextData);
}

/**
 * Log error events that don't require immediate action.
 */
function log_error_message(string $message, mixed $contextData = []): void
{
    get_logger_instance()?->error($message, $contextData);
}

/**
 * Log error events that require immediate action.
 */
function log_critical_message(string $message, mixed $contextData = []): void
{
    get_logger_instance()?->critical($message, $contextData);
}

/**
 * Log emergency events.
 */
function log_emergency_message(string $message, mixed $contextData = []): void
{
    get_logger_instance()?->emergency($message, $contextData);
}
