<?php

declare(strict_types=1);

namespace ModernCMS\Core\Abstractions\Hooks;

interface FiltersStoreInterface
{
    /**
     * Checks if a filter already is registered.
     *
     * @param string $name Filter name.
     */
    public function has(string $name): bool;

    /**
     * Register a filter with a name.
     *
     * @param string $name Filter name.
     *
     * @throws ErrorException when the name already has been registered.
     */
    public function register(string $name): void;

    /**
     * Register a callback with a filter.
     *
     * @param string $name Filter name.
     * @param mixed $filterValue The value that will be filtered.
     * @param callable $handler The callback that will be triggered once the filter is dispatched.
     *
     * @throws ErrorException when the filter name is not registered.
     */
    public function addCallbackToFilter(string $name, callable $handler): void;

    /**
     * Dispatches a filter.
     *
     * @param string $name Filter name.
     * @param array<mixed> $parameters Data that will be accessable through the callback's parameters.
     *
     * @throws ErrorException when the filter name is not registered.
     */
    public function dispatch(string $name, mixed $filterValue, array $parameters): mixed;
}
