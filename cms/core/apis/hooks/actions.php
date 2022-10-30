<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\Hooks\Actions;

use ModernCMS\Core\Abstractions\Hooks\ActionsStoreInterface;
use ModernCMS\Core\Stores\Hooks\ActionsStore;

use function ModernCMS\Core\APIs\Hooks\get_hooks_store;

function get_actions_store(): ActionsStoreInterface
{
    static $store;

    if (!isset($store))
    {
        $store = new ActionsStore(get_hooks_store());
    }

    return $store;
}

/**
 * @param string $name Action name.
 */
function action_exists(string $name): bool
{
    return get_actions_store()->has($name);
}

/**
 * @param string $name Action name.
 *
 * @throws ErrorException when the action already exists.
 */
function register_action(string $name): void
{
    get_actions_store()->register($name);
}

/**
 * @param string $name Action name.
 * @param array<mixed> $parameters Data that will be accessable through the callback's parameters.
 *
 * @throws ErrorException when the action name is not registered.
 */
function register_action_callback(string $name, callable $callback): void
{
    get_actions_store()->addCallbackToAction($name, $callback);
}

/**
 * Runs all callbacks that are registered to a specific action.
 *
 * @param string $name Action name.
 * @param array<mixed> $parameters Data that will be accessable through the callback's parameters.
 *
 * @throws ErrorException when the action name is not registered.
 */
function dispatch_action(string $name, array $parameters = []): void
{
    get_actions_store()->dispatch($name, $parameters);
}
