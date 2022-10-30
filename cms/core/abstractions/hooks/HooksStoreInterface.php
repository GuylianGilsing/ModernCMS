<?php

declare(strict_types=1);

namespace ModernCMS\Core\Abstractions\Hooks;

use ErrorException;

interface HooksStoreInterface
{
    /**
     * Checks if a hook already is registered with the registry.
     *
     * @param string $type Hook type.
     * @param string $name Hook name.
     */
    public function has(string $type, string $name): bool;

    /**
     * Register a hook with a name.
     *
     * @param string $type Hook type.
     * @param string $name Hook name.
     *
     * @throws ErrorException when the name already has been registered.
     */
    public function register(string $type, string $name): void;

    /**
     * Retrieves a hook and its callbacks by name.
     *
     * @param string $type Hook type.
     * @param string $name Hook name.
     *
     * @throws ErrorException when the name has not been registered.
     *
     * @return array<callable>
     */
    public function get(string $type, string $name): array;

    /**
     * Only used within automated tests.
     *
     * @return array<string, array<callable>>
     */
    public function getAllOfType(string $type): array;

    /**
     * Adds a callback to an existing hook.
     *
     * @param string $type Hook type.
     * @param string $name Hook name.
     *
     * @throws ErrorException when the name has not been registered.
     */
    public function addCallbackToHook(string $type, string $name, callable $callback): void;
}
