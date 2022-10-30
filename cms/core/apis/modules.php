<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\Modules;

use ErrorException;
use ModernCMS\Core\Abstractions\Modules\ModulesStoreInterface;
use ModernCMS\Core\Stores\Modules\ModulesStore;

function get_modules_store(): ModulesStoreInterface
{
    static $store;

    if (!isset($store))
    {
        $store = new ModulesStore();
    }

    return $store;
}

function module_exists(string $moduleName): bool
{
    return get_modules_store()->has($moduleName);
}

/**
 * @throws ErrorException when the module already has been registered.
 */
function register_module_bootstrapper(string $moduleName, string $bootstrapperFilePath): void
{
    $store = get_modules_store();

    if ($store->has($moduleName))
    {
        throw new ErrorException("Module \"{$moduleName}\" already has been registered.");
    }

    $store->registerBootstrapper($moduleName, $bootstrapperFilePath);
}

function load_registered_modules(): void
{
    get_modules_store()->bootstrapModules();
}
