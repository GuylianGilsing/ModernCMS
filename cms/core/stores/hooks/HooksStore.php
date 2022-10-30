<?php

declare(strict_types=1);

namespace ModernCMS\Core\Stores\Hooks;

use ErrorException;
use ModernCMS\Core\Abstractions\Hooks\HooksStoreInterface;

final class HooksStore implements HooksStoreInterface
{
    /**
     * @var array<string, array<string, array<callable>>> $hooks
     */
    protected array $hooks = [];

    public function has(string $type, string $name): bool
    {
        if (!array_key_exists($type, $this->hooks))
        {
            return false;
        }

        if (!array_key_exists($name, $this->hooks[$type]))
        {
            return false;
        }

        return true;
    }

    /**
     * @throws ErrorException when the name already has been registered.
     */
    public function register(string $type, string $name): void
    {
        if ($this->has($type, $name))
        {
            throw new ErrorException(
                "Action, with name \"{$name}\" and type \"{$type}\", has already been registered."
            );
        }

        if (!array_key_exists($type, $this->hooks))
        {
            $this->hooks[$type] = [];
        }

        if (!array_key_exists($name, $this->hooks[$type]))
        {
            $this->hooks[$type][$name] = [];
        }
    }

    /**
     * @throws ErrorException when the name has not been registered.
     *
     * @return array<callable>
     */
    public function get(string $type, string $name): array
    {
        if (!$this->has($type, $name))
        {
            throw new ErrorException("Action, with name \"{$name}\" and type \"{$type}\", hasn't been registered.");
        }

        return $this->hooks[$type][$name];
    }

    /**
     * @return array<string, array<callable>>
     */
    public function getAllOfType(string $type): array
    {
        if (!array_key_exists($type, $this->hooks))
        {
            return [];
        }

        return $this->filters[$type];
    }

    /**
     * @throws ErrorException when the name has not been registered.
     */
    public function addCallbackToHook(string $type, string $name, callable $callback): void
    {
        if (!$this->has($type, $name))
        {
            throw new ErrorException("Action, with name \"{$name}\" and type \"{$type}\", hasn't been registered.");
        }

        $this->hooks[$type][$name][] = $callback;
    }
}
