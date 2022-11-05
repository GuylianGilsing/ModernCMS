<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Bootstrap;

use ModernCMS\Module\CoreAuth\Abstractions\Users\User;
use ModernCMS\Module\CoreAuth\Mappings\Array\ToUserTypeMapping;
use ModernCMS\Module\CoreAuth\Middleware\IsAuthenticatedMiddleware;
use PHPClassMapper\Configuration\ArrayMapperConfigurationInterface;
use Slim\Interfaces\RouteGroupInterface;

use function ModernCMS\Core\APIs\Hooks\Actions\register_action_callback;
use function ModernCMS\Module\CoreAuth\APIs\Authorization\get_registered_permissions;
use function ModernCMS\Module\CoreAuth\APIs\Authorization\set_role_permissions;

register_action_callback('mcms_backend_base_group', function (RouteGroupInterface $router)
{
    $router->add(IsAuthenticatedMiddleware::class);
});

require_once __DIR__.'/register-actions/register-backend-routes.php';

register_action_callback('mcms_configure_array_mappings', function (ArrayMapperConfigurationInterface $config)
{
    $config->addFromArrayMapping(User::class, new ToUserTypeMapping());
});

register_action_callback('mcms_initialized', function (): void
{
    set_role_permissions('administrator', array_keys(get_registered_permissions()));
});
