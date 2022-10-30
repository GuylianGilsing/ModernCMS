<?php

declare(strict_types=1);

namespace ModernCMS\Core\Stores\Hooks;

use ErrorException;
use ModernCMS\Core\Abstractions\Hooks\FiltersStoreInterface;
use ModernCMS\Core\Abstractions\Hooks\HooksStoreInterface;

final class FiltersStore implements FiltersStoreInterface
{
    private string $hookType = 'filter';
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
            throw new ErrorException("Filter \"{$name}\" has already been registered.");
        }

        $this->hooksStore->register($this->hookType, $name);
    }

    /**
     * @throws ErrorException when the filter name is not registered.
     */
    public function addCallbackToFilter(string $name, callable $callback): void
    {
        if (!$this->hooksStore->has($this->hookType, $name))
        {
            throw new ErrorException("Filter \"{$name}\" hasn't been registered.");
        }

        $this->hooksStore->addCallbackToHook($this->hookType, $name, $callback);
    }

    /**
     * @throws ErrorException when the filter name is not registered.
     */
    public function dispatch(string $name, mixed $filterValue, array $parameters = []): mixed
    {
        if (!$this->hooksStore->has($this->hookType, $name))
        {
            throw new ErrorException("Filter \"{$name}\" hasn't been registered.");
        }

        // Will combine the filter value with all parameters into an array and call the next callback with it
        $callbackResult = $filterValue;
        $callbackParameters = array_merge([$filterValue], $parameters);

        foreach ($this->hooksStore->get($this->hookType, $name) as $callbacks)
        {
            $callbackResult = call_user_func_array($callbacks, $callbackParameters);
            $callbackParameters = array_merge([$callbackResult], $parameters);
        }

        return $callbackResult;
    }
}
