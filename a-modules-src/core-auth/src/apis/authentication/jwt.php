<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\APIs\Authentication\JWT;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\Validator;

use function ModernCMS\Core\APIs\Logging\log_info_message;
use function ModernCMS\Core\APIs\UUID\generate_uuid;
use function ModernCMS\Core\APIs\UUID\generated_uuid_is_valid;
use function ModernCMS\Core\Helpers\HTTP\http_protocol_and_host_name;
use function ModernCMS\Module\CoreAuth\APIs\Users\get_user_by_id;

function _get_jwt_token_builder(): Builder
{
    return new Builder(new JoseEncoder(), ChainedFormatter::default());
}

/**
 * Issues a token that is used in the authentication process.
 */
function issue_authentication_token(int $userId, int $lifeTimeInSeconds): string
{
    $host = http_protocol_and_host_name();
    $builder = _get_jwt_token_builder();

    $currentTime = new DateTimeImmutable();
    $algorithm = new Sha256();
    $signingKey = InMemory::plainText(JWT_SECRET_KEY);

    return $builder->issuedBy($host)
                    ->permittedFor($host)
                    ->issuedAt($currentTime)
                    ->identifiedBy(generate_uuid())
                    ->canOnlyBeUsedAfter($currentTime)
                    ->expiresAt($currentTime->modify("+{$lifeTimeInSeconds} seconds"))
                    ->withClaim('uid', $userId)
                    ->getToken($algorithm, $signingKey)
                    ->toString();
}

/**
 * Issues a token that is used to refresh an authentication token.
 */
function issue_refresh_token(int $userId, int $lifeTimeInSeconds): string
{
    $host = http_protocol_and_host_name();
    $builder = _get_jwt_token_builder();

    $currentTime = new DateTimeImmutable();
    $algorithm = new Sha256();
    $signingKey = InMemory::plainText(JWT_SECRET_KEY);

    return $builder->issuedBy($host)
                    ->permittedFor($host)
                    ->issuedAt($currentTime)
                    ->identifiedBy(generate_uuid())
                    ->canOnlyBeUsedAfter($currentTime)
                    ->expiresAt($currentTime->modify("+{$lifeTimeInSeconds} seconds"))
                    ->withClaim('uid', $userId)
                    ->getToken($algorithm, $signingKey)
                    ->toString();
}

function parse_jwt_token_string(string $jwt): ?UnencryptedToken
{
    $token = null;
    $parser = new Parser(new JoseEncoder());

    try
    {
        $token = $parser->parse($jwt);
    }
    catch(Exception $e)
    {
        log_info_message('Failed to parse token', [$e->getMessage(), $jwt]);

        return null;
    }

    return $token;
}

function jwt_token_is_valid(UnencryptedToken $token, Validator $validator): bool
{
    $host = http_protocol_and_host_name();
    $algorithm = new Sha256();
    $signingKey = InMemory::plainText(JWT_SECRET_KEY);

    $clock = new SystemClock(new DateTimeZone(date_default_timezone_get()));

    if (!$validator->validate($token, new SignedWith($algorithm, $signingKey)))
    {
        log_info_message(
            'Token (base jwt) validation failed: wrong signing method or secret used.', [$token->toString()]
        );

        return false;
    }

    // JTI validation
    if (!$token->claims()->has('jti'))
    {
        log_info_message('Token (base jwt) validation failed: "jti" claim is missing.', [$token->toString()]);

        return false;
    }

    if (!generated_uuid_is_valid($token->claims()->get('jti')))
    {
        log_info_message('Token (base jwt) validation failed: jwt ID is not a valid uuid.', [$token->toString()]);

        return false;
    }

    // ISS validation
    if (!$validator->validate($token, new IssuedBy($host)))
    {
        log_info_message('Token (base jwt) validation failed: "iss" claim is incorrect.', [$token->toString()]);

        return false;
    }

    // AUD validation
    if (!$validator->validate($token, new PermittedFor($host)))
    {
        log_info_message('Token (base jwt) validation failed: "aud" claim is incorrect.', [$token->toString()]);

        return false;
    }

    // UID validation
    if (!$token->claims()->has('uid'))
    {
        log_info_message('Token (base jwt) validation failed: "uid" claim is missing.', [$token->toString()]);

        return false;
    }

    $tokenUID = $token->claims()->get('uid');

    if (!is_numeric($tokenUID))
    {
        log_info_message('Token (base jwt) validation failed: "uid" claim is not numeric.', [$token->toString()]);

        return false;
    }

    $user = get_user_by_id(intval($tokenUID));

    if ($user === null)
    {
        log_info_message(
            'Token (base jwt) validation failed: "uid" claim refers to a not-existing user.', [$token->toString()]
        );

        return false;
    }

    // Time validation (with 60 seconds of leeway)
    if (!$validator->validate($token, new StrictValidAt($clock, new DateInterval('PT60S'))))
    {
        log_info_message('Token (base jwt) validation failed: time validation failed.', [$token->toString()]);

        return false;
    }

    return true;
}

function authentication_token_jwt_is_valid(string $jwt): bool
{
    $token = parse_jwt_token_string($jwt);

    if ($token === null)
    {
        log_info_message('Token (authentication) validation failed: jwt could not be parsed.', [$jwt]);

        return false;
    }

    $validator = new Validator();

    if (!jwt_token_is_valid($token, $validator))
    {
        return false;
    }

    return true;
}

function refresh_token_jwt_is_valid(string $jwt): bool
{
    $token = parse_jwt_token_string($jwt);

    if ($token === null)
    {
        log_info_message('Token (refresh) validation failed: jwt could not be parsed.', [$jwt]);

        return false;
    }

    $validator = new Validator();

    if (!jwt_token_is_valid($token, $validator))
    {
        return false;
    }

    return true;
}
