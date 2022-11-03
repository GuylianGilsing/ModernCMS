<?php

declare(strict_types=1);

namespace ModernCMS\Core\Helpers\HTTP;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

use function ModernCMS\Core\APIs\Sessions\set_session_data;

/**
 * Returns the HTTP protocol and the site's host name as follows: `http://example.com` or `https://example.com`.
 */
function http_protocol_and_host_name(): string
{
    return isset($_SERVER['HTTPS']) ? 'https://'.$_SERVER['HTTP_HOST'] : 'http://'.$_SERVER['HTTP_HOST'];
}

/**
 * Creates a 301, moved permanently, response that redirects to another URL.
 */
function redirect_response(string $url): ResponseInterface
{
    $response = new Response(StatusCodeInterface::STATUS_MOVED_PERMANENTLY);

    return $response->withHeader('Location', http_protocol_and_host_name().$url);
}

/**
 * Creates a 404, resource not found, response.
 */
function notfound_response(): ResponseInterface
{
    $response = new Response(StatusCodeInterface::STATUS_NOT_FOUND);

    return $response;
}

/**
 * Creates a redirect with extra form validation errors and filled data sessions.
 *
 * @param string $url The url to respond with.
 * @param array<string, mixed> $validationMessages The validation error messages.
 * @param array<string, mixed> $filledData The filled in form data.
 */
function form_error_response(string $url, array $validationMessages, array $filledData = []): ResponseInterface
{
    set_session_data('validation_errors', $validationMessages);

    if (!empty($filledData))
    {
        set_session_data('filled_data', $filledData);
    }

    return redirect_response($url);
}
