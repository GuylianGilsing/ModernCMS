<?php

declare(strict_types=1);

namespace ModernCMS\Core\Bootstrap;

use Slim\Routing\RouteCollectorProxy;

use function ModernCMS\Core\APIs\Application\get_application_instance;

$app = get_application_instance();

$app->group('/cms/api', function (RouteCollectorProxy $group)
{

});
