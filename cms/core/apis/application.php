<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\Application;

use DI\Bridge\Slim\Bridge;
use DI\Container;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Csrf\Guard;

use function ModernCMS\Core\APIs\Hooks\Actions\dispatch_action;

function get_dependency_container_instance(): ContainerInterface
{
    static $container;

    if (!isset($container))
    {
        $builder = new ContainerBuilder();

        $builder->useAnnotations(false);
        $builder->useAutowiring(true);

        dispatch_action('mcms_add_dependency_container_definitions', [$builder]);

        $container = $builder->build();
    }

    return $container;
}

function get_application_instance(): App
{
    static $app;

    if (!isset($app))
    {
        /**
         * @var Container $container
         */
        $container = get_dependency_container_instance();
        $app = Bridge::create($container);

        if (DO_CSRF)
        {
            $container->set('csrf', function () use ($app)
            {
                return new Guard($app->getResponseFactory());
            });
        }
    }

    return $app;
}

function application_cleanup(): void
{
    // TODO:: Cleanup stuff...
}
