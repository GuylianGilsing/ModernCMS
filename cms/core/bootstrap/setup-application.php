<?php

declare(strict_types=1);

namespace ModernCMS\Core\Bootstrap;

use ModernCMS\Core\Middleware\RemoveTrailingSlashMiddleware;

use function ModernCMS\Core\APIs\Application\get_application_instance;

$app = get_application_instance();

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

if (DEBUG_MODE)
{
    $app->addErrorMiddleware(true, true, true);
}

$app->add(RemoveTrailingSlashMiddleware::class);
