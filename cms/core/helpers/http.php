<?php

declare(strict_types=1);

namespace ModernCMS\Core\Helpers\HTTP;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

/**
 * Returns the HTTP protocol and the site's host name as follows: `http://example.com` or `https://example.com`.
 */
function http_protocol_and_host_name(): string
{
    return isset($_SERVER['HTTPS']) ? 'https://'.$_SERVER['HTTP_HOST'] : 'http://'.$_SERVER['HTTP_HOST'];
}

function redirect_response(string $path): ResponseInterface
{
    $response = new Response(StatusCodeInterface::STATUS_MOVED_PERMANENTLY);

    return $response->withHeader('Location', http_protocol_and_host_name().$path);
}

function notfound_response(): ResponseInterface
{
    $response = new Response(StatusCodeInterface::STATUS_NOT_FOUND);

    return $response;
}
