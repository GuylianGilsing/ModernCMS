<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\APIs\Authentication;

use ErrorException;

use function ModernCMS\Core\APIs\Cookie\cookie_exists;
use function ModernCMS\Core\APIs\Cookie\delete_cookie_if_exists;
use function ModernCMS\Core\APIs\Cookie\get_cookie_data;
use function ModernCMS\Core\APIs\Cookie\set_cookie;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\BlacklistTokens\blacklist_token;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\BlacklistTokens\delete_expired_blacklisted_token_ids;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\BlacklistTokens\token_is_blacklisted;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\JWT\authentication_token_jwt_is_valid;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\JWT\issue_authentication_token;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\JWT\issue_refresh_token;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\JWT\parse_jwt_token_string;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\JWT\refresh_token_jwt_is_valid;
use function ModernCMS\Module\CoreAuth\APIs\Users\get_user_by_id;

/**
 * Attempts to login a user.
 *
 * @param int $user ID The ID of the user that needs to be logged in.
 *
 * @throws ErrorException if the ID is of a user that does not exist.
 * @throws ErrorException if the user already has been logged in.
 */
function login_user(int $userId): bool
{
    $user = get_user_by_id($userId);

    if ($user === null)
    {
        throw new ErrorException("User with ID \"{$userId}\" does not exist.");
    }

    if (is_logged_in())
    {
        throw new ErrorException("Cannot login user, a logged in user already exists.");
    }

    $authenticationToken = issue_authentication_token($user->getId(), AUTHENTICATION_TOKEN_LIFETIME);
    $refreshToken = issue_refresh_token($user->getId(), AUTHENTICATION_COOKIES_LIFETIME);

    set_cookie('mcms_backend_login', $authenticationToken, AUTHENTICATION_TOKEN_LIFETIME);
    set_cookie('mcms_backend_login_refresh', $refreshToken, AUTHENTICATION_COOKIES_LIFETIME);

    return true;
}

function logout(): void
{
    blacklist_auth_cookie_tokens();
    delete_auth_cookies_if_exists();
}

/**
 * Checks if there is a login refresh cookie available.
 */
function is_logged_in(): bool
{
    if (!cookie_exists('mcms_backend_login_refresh'))
    {
        return false;
    }

    $jwt = get_cookie_data('mcms_backend_login_refresh');

    if (!refresh_token_jwt_is_valid($jwt))
    {
        return false;
    }

    return true;
}

function get_authentication_token_jwt(): ?string
{
    if (!cookie_exists('mcms_backend_login'))
    {
        return null;
    }

    return get_cookie_data('mcms_backend_login');
}

function get_refresh_token_jwt(): ?string
{
    if (!cookie_exists('mcms_backend_login_refresh'))
    {
        return null;
    }

    return get_cookie_data('mcms_backend_login_refresh');
}

function authentication_token_is_valid(): bool
{
    if (!cookie_exists('mcms_backend_login'))
    {
        return false;
    }

    $jwt = get_cookie_data('mcms_backend_login');

    if (!authentication_token_jwt_is_valid($jwt))
    {
        return false;
    }

    return true;
}

function refresh_authentication_token(string $refreshTokenJWT): bool
{
    if (!refresh_token_jwt_is_valid($refreshTokenJWT))
    {
        return false;
    }

    $refreshToken = parse_jwt_token_string($refreshTokenJWT);

    $authenticationToken = issue_authentication_token(
        $refreshToken->claims()->get('uid'), AUTHENTICATION_TOKEN_LIFETIME
    );

    set_cookie('mcms_backend_login', $authenticationToken, AUTHENTICATION_TOKEN_LIFETIME);

    return true;
}

function delete_auth_cookies_if_exists(): void
{
    delete_cookie_if_exists('mcms_backend_login');
    delete_cookie_if_exists('mcms_backend_login_refresh');
}

function blacklist_auth_cookie_tokens(): void
{
    if (cookie_exists('mcms_backend_login'))
    {
        $jwt = get_cookie_data('mcms_backend_login');
        $token = parse_jwt_token_string($jwt);

        if ($token !== null && $token->claims()->has('jti'))
        {
            $tokenId = $token->claims()->get('jti');

            if (!token_is_blacklisted($tokenId))
            {
                blacklist_token($tokenId, 'authentication');
            }
        }
    }

    if (cookie_exists('mcms_backend_login_refresh'))
    {
        $jwt = get_cookie_data('mcms_backend_login_refresh');
        $token = parse_jwt_token_string($jwt);

        if ($token !== null && $token->claims()->has('jti'))
        {
            $tokenId = $token->claims()->get('jti');

            if (!token_is_blacklisted($tokenId))
            {
                blacklist_token($tokenId, 'refresh');
            }
        }
    }
}

function delete_expired_blacklisted_tokens()
{
    delete_expired_blacklisted_token_ids('authentication', AUTHENTICATION_TOKEN_LIFETIME);
    delete_expired_blacklisted_token_ids('refresh', AUTHENTICATION_COOKIES_LIFETIME);
}
