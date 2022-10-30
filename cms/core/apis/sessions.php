<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\Sessions;

use ErrorException;

function session_exists(string $key): bool
{
    return array_key_exists($key, $_SESSION);
}

/**
 * Creates/updates a session.
 */
function set_session_data(string $key, mixed $data): void
{
    $_SESSION[$key] = $data;
}

/**
 * @throws ErrorException when the session does not exist.
 */
function get_session_data(string $key): mixed
{
    if (!session_exists($key))
    {
        throw new ErrorException("Session \"{$key}\" does not exist.");
    }

    return $_SESSION[$key];
}

/**
 * @throws ErrorException when the session does not exist.
 */
function delete_session(string $key): void
{
    if (!session_exists($key))
    {
        throw new ErrorException("Session \"{$key}\" does not exist.");
    }

    unset($_SESSION[$key]);
}

function delete_session_if_exists(string $key): void
{
    if (!session_exists($key))
    {
        return;
    }

    delete_session($key);
}
