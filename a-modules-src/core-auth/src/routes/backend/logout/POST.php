<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Routes\Backend\Logout;

use Psr\Http\Message\ResponseInterface;

use function ModernCMS\Core\Helpers\HTTP\redirect_response;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\blacklist_auth_cookie_tokens;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\delete_auth_cookies_if_exists;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\delete_expired_blacklisted_tokens;

final class POST
{
    public function __invoke(): ResponseInterface
    {
        blacklist_auth_cookie_tokens();
        delete_auth_cookies_if_exists();
        delete_expired_blacklisted_tokens();

        return redirect_response('/cms');
    }
}
