<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Csrf\Guard;
use Slim\Psr7\Response;

use function ModernCMS\Core\APIs\Application\get_dependency_container_instance;
use function ModernCMS\Core\APIs\Sessions\delete_session_if_exists;
use function ModernCMS\Core\APIs\Sessions\get_session_data;
use function ModernCMS\Core\APIs\Sessions\session_exists;
use function ModernCMS\Core\APIs\Sessions\set_session_data;
use function ModernCMS\Core\APIs\Views\view_response;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\authentication_token_is_valid;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\blacklist_auth_cookie_tokens;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\delete_auth_cookies_if_exists;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\get_refresh_token_jwt;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\is_logged_in;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\refresh_authentication_token;

final class IsAuthenticatedMiddleware
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->requestsWhiteListedPath($request))
        {
            return $handler->handle($request);
        }

        if (is_logged_in())
        {
            // Refresh the authentication token before handling the request in order to prevent token retrieval bugs
            if (!authentication_token_is_valid())
            {
                $refreshToken = get_refresh_token_jwt();

                if (!refresh_authentication_token($refreshToken))
                {
                    set_session_data('login_errors', ['global' => 'Failed to refresh login session']);

                    return $this->serveLoginPage();
                }

                $response = new Response(StatusCodeInterface::STATUS_TEMPORARY_REDIRECT);

                // Add new CSRF headers to the response to avoid CSRF validation errors
                if (DO_CSRF)
                {
                    $csrf = get_dependency_container_instance()->get('csrf');

                    if ($csrf instanceof Guard)
                    {
                        if ($request->hasHeader($csrf->getTokenNameKey()))
                        {
                            $response = $response->withHeader($csrf->getTokenNameKey(), $csrf->getTokenName());
                        }

                        if ($request->hasHeader($csrf->getTokenValueKey()))
                        {
                            $response = $response->withHeader($csrf->getTokenValueKey(), $csrf->getTokenValue());
                        }
                    }
                }

                // Internally, tokens are being stored as cookies
                // The page needs to be refreshed to update the cookie with the refreshed token
                return $response->withHeader('Location', strval($request->getUri()));
            }

            return $handler->handle($request);
        }

        return $this->serveLoginPage();
    }

    private function requestsWhiteListedPath(ServerRequestInterface $request): bool
    {
        $whiteListedPaths = [
            '/cms/login',
            '/cms/logout',
            '/cms/reset-password'
        ];

        return in_array($request->getUri()->getPath(), $whiteListedPaths, true);
    }

    private function serveLoginPage(): ResponseInterface
    {
        blacklist_auth_cookie_tokens();
        delete_auth_cookies_if_exists();

        if (DO_CSRF)
        {
            get_dependency_container_instance()->get('csrf')?->generateToken();
        }

        $response = view_response('/backend/login.twig', $this->getLoginPageTemplateData());

        // Delete any flash messages
        delete_session_if_exists('login_errors');
        delete_session_if_exists('login_filled_data');

        return $response;
    }

    private function getLoginPageTemplateData(): array
    {
        $templateData = [];

        if (session_exists('login_errors'))
        {
            $templateData['errors'] = get_session_data('login_errors');
        }

        if (session_exists('login_filled_data'))
        {
            $templateData['filledData'] = get_session_data('login_filled_data');
        }

        return $templateData;
    }
}
