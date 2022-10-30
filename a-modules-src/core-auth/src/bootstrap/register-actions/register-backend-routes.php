<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Bootstrap;

use Slim\Routing\RouteCollectorProxy;

use function ModernCMS\Core\APIs\Hooks\Actions\register_action_callback;

register_action_callback('mcms_register_backend_routes', function (RouteCollectorProxy $router)
{
    $loginRoute = $router->post('/login', \ModernCMS\Module\CoreAuth\Routes\Backend\Login\POST::class);
    $logoutRoute = $router->post('/logout', \ModernCMS\Module\CoreAuth\Routes\Backend\Logout\POST::class);

    $usersOverviewRoute = $router->get('/users', \ModernCMS\Module\CoreAuth\Routes\Backend\Users\Overview\GET::class);

    if (DO_CSRF)
    {
        $loginRoute->add('csrf');
        $logoutRoute->add('csrf');
        $usersOverviewRoute->add('csrf');
    }
});
