<?php

declare(strict_types=1);

namespace ModernCMS\Core\Bootstrap;

use DI\ContainerBuilder;
use ModernCMS\Core\Abstractions\Assets\AssetsStoreInterface;
use ModernCMS\Core\Abstractions\Hooks\ActionsStoreInterface;
use ModernCMS\Core\Abstractions\Hooks\FiltersStoreInterface;
use ModernCMS\Core\Abstractions\Hooks\HooksStoreInterface;
use PHPClassMapper\ArrayMapperInterface;
use PHPClassMapper\MapperInterface;

use function ModernCMS\Core\APIs\Assets\get_assets_store;
use function ModernCMS\Core\APIs\Database\get_database_connection_instance;
use function ModernCMS\Core\APIs\Hooks\Actions\get_actions_store;
use function ModernCMS\Core\APIs\Hooks\Actions\register_action_callback;
use function ModernCMS\Core\APIs\Hooks\Filters\get_filters_store;
use function ModernCMS\Core\APIs\Hooks\get_hooks_store;
use function ModernCMS\Core\APIs\Logging\get_logger_instance;
use function ModernCMS\Core\APIs\Mapping\get_array_mapper_instance;
use function ModernCMS\Core\APIs\Mapping\get_class_mapper_instance;

register_action_callback('mcms_add_dependency_container_definitions', function (ContainerBuilder $builder)
{
    $builder->addDefinitions([
        LoggerInterface::class => \DI\factory(fn() => get_logger_instance()),

        HooksStoreInterface::class => \DI\factory(fn() => get_hooks_store()),
        ActionsStoreInterface::class => \DI\factory(fn() => get_actions_store()),
        FiltersStoreInterface::class => \DI\factory(fn() => get_filters_store()),
        AssetsStoreInterface::class => \DI\factory(fn() => get_assets_store()),

        Connection::class => \DI\factory(fn() => get_database_connection_instance()),

        MapperInterface::class => \DI\factory(fn() => get_class_mapper_instance()),
        ArrayMapperInterface::class => \DI\factory(fn() => get_array_mapper_instance()),
    ]);
});
