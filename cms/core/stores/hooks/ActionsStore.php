<?php

declare(strict_types=1);

namespace ModernCMS\Core\Stores\Hooks;

use ErrorException;
use ModernCMS\Core\Abstractions\Hooks\ActionsStoreInterface;
use ModernCMS\Core\Abstractions\Hooks\HooksStoreInterface;

final class ActionsStore implements ActionsStoreInterface
{
    private string $hookType = 'action';
    private HooksStoreInterface $hooksStore;

    public function __construct(HooksStoreInterface $hooksStore)
    {
        $this->hooksStore = $hooksStore;
    }

    /**
     * Only used within automated tests.
     *
     * @return array<string, array<callable>>
     */
    public function getInternalData(): array
    {
        return $this->hooksStore->getAllOfType($this->hookType);
    }

    /**
     * @param string $name Action name.
     */
    public function has(string $name): bool
    {
        return $this->hooksStore->has($this->hookType, $name);
    }

    /**
     * @throws ErrorException when the name already has been registered.
     */
    public function register(string $name): void
    {
        if ($this->hooksStore->has($this->hookType, $name))
        {
            throw new ErrorException("Action \"{$name}\" has already been registered.");
        }

        $this->hooksStore->register($this->hookType, $name);
    }

    /**
     * @throws ErrorException when the action name is not registered.
     */
    public function addCallbackToAction(string $name, callable $callback): void
    {
        if (!$this->hooksStore->has($this->hookType, $name))
        {
            throw new ErrorException("Action \"{$name}\" hasn't been registered.");
        }

        $this->hooksStore->addCallbackToHook($this->hookType, $name, $callback);
    }

    /**
     * @throws ErrorException when the action name is not registered.
     */
    public function dispatch(string $name, array $parameters = []): void
    {
        if (!$this->hooksStore->has($this->hookType, $name))
        {
            throw new ErrorException("Action \"{$name}\" hasn't been registered.");
        }

        foreach ($this->hooksStore->get($this->hookType, $name) as $callbacks)
        {
            call_user_func_array($callbacks, $parameters);
        }
    }
}
