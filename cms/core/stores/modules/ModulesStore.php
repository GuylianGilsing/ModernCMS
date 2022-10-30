<?php

declare(strict_types=1);

namespace ModernCMS\Core\Stores\Modules;

use ErrorException;
use ModernCMS\Core\Abstractions\Modules\ModulesStoreInterface;

use function ModernCMS\Core\APIs\Logging\log_info_message;
use function ModernCMS\Core\APIs\Logging\log_warning_message;

final class ModulesStore implements ModulesStoreInterface
{
    /**
     * @var array<string, string> $modules
     */
    private array $modules = [];

    /**
     * Checks if a module is registered.
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->modules);
    }

    /**
     * Registers a modules bootstrapper file.
     *
     * @throws ErrorException when the module already has been registered.
     */
    public function registerBootstrapper(string $name, string $bootstrapperFilePath): void
    {
        if ($this->has($name))
        {
            throw new ErrorException("Module \"{$name}\" already has been registered.");
        }

        $this->modules[$name] = $bootstrapperFilePath;
    }

    /**
     * Calls all registered module bootstrap files.
     */
    public function bootstrapModules(): void
    {
        foreach ($this->modules as $moduleName => $bootstrapperFilePath)
        {
            if (!file_exists($bootstrapperFilePath))
            {
                log_warning_message(
                    "Skipping module \"$moduleName\", bootstrapper file \"{$bootstrapperFilePath}\" does not exist."
                );
                continue;
            }

            if (in_array($moduleName, INACTIVE_MODULES))
            {
                log_info_message("Skipping module \"{$moduleName}\", module is registered as inactive");
                continue;
            }

            require_once $bootstrapperFilePath;
        }
    }
}
