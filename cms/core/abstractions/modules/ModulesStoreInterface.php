<?php

declare(strict_types=1);

namespace ModernCMS\Core\Abstractions\Modules;

use ErrorException;

interface ModulesStoreInterface
{
    /**
     * Checks if a module is registered.
     */
    public function has(string $name): bool;

    /**
     * Registers a modules bootstrapper file.
     *
     * @throws ErrorException when the module already has been registered.
     */
    public function registerBootstrapper(string $name, string $bootstrapperFilePath): void;

    /**
     * Calls all registered module bootstrap files.
     */
    public function bootstrapModules(): void;
}
