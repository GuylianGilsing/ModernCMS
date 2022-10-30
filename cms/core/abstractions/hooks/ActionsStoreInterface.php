<?php

declare(strict_types=1);

namespace ModernCMS\Core\Abstractions\Hooks;

interface ActionsStoreInterface
{
    /**
     * Checks if an action already is registered.
     *
     * @param string $name Action name.
     */
    public function has(string $name): bool;

    /**
     * Register an action with a name.
     *
     * @param string $name Action name.
     *
     * @throws ErrorException when the name already has been registered.
     */
    public function register(string $name): void;

    /**
     * Register a callback with an action.
     *
     * @param string $name Action name.
     * @param callable $handler The callback that will be triggered once the action is dispatched.
     *
     * @throws ErrorException when the action name is not registered.
     */
    public function addCallbackToAction(string $name, callable $handler): void;

    /**
     * Dispatches an action.
     *
     * @param string $name Action name.
     * @param array<mixed> $parameters Data that will be accessable through the callback's parameters.
     *
     * @throws ErrorException when the action name is not registered.
     */
    public function dispatch(string $name, array $parameters): void;
}
