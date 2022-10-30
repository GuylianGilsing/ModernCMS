<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\Hooks\Filters;

use ModernCMS\Core\Abstractions\Hooks\FiltersStoreInterface;
use ModernCMS\Core\Stores\Hooks\FiltersStore;

use function ModernCMS\Core\APIs\Hooks\get_hooks_store;

function get_filters_store(): FiltersStoreInterface
{
    static $store;

    if (!isset($store))
    {
        $store = new FiltersStore(get_hooks_store());
    }

    return $store;
}

/**
 * @param string $name Filter name.
 */
function filter_exists(string $name): bool
{
    return get_filters_store()->has($name);
}

/**
 * @param string $name Filter name.
 *
 * @throws ErrorException when the name already has been registered.
 */
function register_filter(string $name): void
{
    get_filters_store()->register($name);
}

/**
 * @param string $name Filter name.
 * @param callable $handler The callback that will be triggered once the filter is dispatched.
 *
 * @throws ErrorException when the filter name is not registered.
 */
function register_filter_callback(string $name, callable $callback): void
{
    get_filters_store()->addCallbackToFilter($name, $callback);
}

/**
 * Runs all callbacks that are registered to a specific filter.
 *
 * @param string $name Filter name.
 * @param mixed $filterValue The value that will be filtered.
 * @param array<mixed> $parameters Data that will be accessable through the callback's parameters.
 *
 * @throws ErrorException when the filter name is not registered.
 */
function dispatch_filter(string $name, mixed $filterValue, array $parameters = []): mixed
{
    return get_filters_store()->dispatch($name, $filterValue, $parameters);
}
