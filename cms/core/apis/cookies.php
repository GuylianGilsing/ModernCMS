<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\Cookie;

use ErrorException;

function cookie_exists(string $key): bool
{
    return array_key_exists($key, $_COOKIE);
}

/**
 * Creates/updates a cookie.
 */
function set_cookie(
    string $key,
    string $data,
    int $aliveTime,
    string $path = '/',
    string $domain = '',
    bool $secure = false,
    bool $httpOnly = false
): void {
    setcookie($key, $data, time() + $aliveTime, $path, $domain, $secure, $httpOnly);
}

/**
 * @throws ErrorException when the cookie does not exist.
 */
function get_cookie_data(string $key): string
{
    if (!cookie_exists($key))
    {
        throw new ErrorException("Cookie \"{$key}\" does not exist.");
    }

    return $_COOKIE[$key];
}

/**
 * @throws ErrorException when the cookie does not exist.
 */
function delete_cookie(
    string $key,
    string $path = '/',
    string $domain = '',
    bool $secure = false,
    bool $httpOnly = false
): void {
    if (!cookie_exists($key))
    {
        throw new ErrorException("Cookie \"{$key}\" does not exist.");
    }

    setcookie($key, '', -1, $path, $domain, $secure, $httpOnly);
}

function delete_cookie_if_exists(
    string $key,
    string $path = '/',
    string $domain = '',
    bool $secure = false,
    bool $httpOnly = false
): void {
    if (cookie_exists($key))
    {
        delete_cookie($key, $path, $domain, $secure, $httpOnly);
    }
}
