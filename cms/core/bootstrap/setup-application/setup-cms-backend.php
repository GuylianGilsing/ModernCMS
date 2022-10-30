<?php

declare(strict_types=1);

namespace ModernCMS\Core\Bootstrap;

use Slim\Routing\RouteCollectorProxy;

use function ModernCMS\Core\APIs\Application\get_application_instance;
use function ModernCMS\Core\APIs\Hooks\Actions\dispatch_action;

$app = get_application_instance();

$baseGroup = $app->group('/cms', function (RouteCollectorProxy $router)
{
    $dashboardRoute = $router->get('', \ModernCMS\Core\Routes\Backend\Dashboard\GET::class);

    if (DO_CSRF)
    {
        $dashboardRoute->add('csrf');
    }

    dispatch_action('mcms_register_backend_routes', [$router]);
});

dispatch_action('mcms_backend_base_group', [$baseGroup]);
