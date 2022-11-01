<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Bootstrap;

use Slim\Routing\RouteCollectorProxy;

use function ModernCMS\Core\APIs\Hooks\Actions\register_action_callback;

register_action_callback('mcms_register_backend_routes', function (RouteCollectorProxy $router)
{
    $loginRoutePOST = $router->post('/login', \ModernCMS\Module\CoreAuth\Routes\Backend\Login\POST::class);
    $logoutRoutePOST = $router->post('/logout', \ModernCMS\Module\CoreAuth\Routes\Backend\Logout\POST::class);

    $usersOverviewRouteGET = $router->get('/users', \ModernCMS\Module\CoreAuth\Routes\Backend\Users\Overview\GET::class);

    $usersManageGET = $router->get('/users/{userId}', \ModernCMS\Module\CoreAuth\Routes\Backend\Users\Manage\GET::class);
    $usersManagePOST = $router->post('/users/create', \ModernCMS\Module\CoreAuth\Routes\Backend\Users\Manage\POST::class);

    if (DO_CSRF)
    {
        $loginRoutePOST->add('csrf');
        $logoutRoutePOST->add('csrf');

        $usersOverviewRouteGET->add('csrf');

        $usersManageGET->add('csrf');
        $usersManagePOST->add('csrf');
    }
});
