<?php

declare(strict_types=1);

namespace ModernCMS\Core\Abstractions\UI\Sidebar;

use ModernCMS\Core\Abstractions\UI\SortableInterface;

class SidebarSection implements SortableInterface
{
    protected string $key = '';
    protected string $name = '';

    /**
     * @var array<SidebarGroup>
     */
    protected array $groups = [];

    public function __construct(string $key, string $name)
    {
        $this->key = $key;
        $this->name = $name;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addGroup(SidebarGroup $group): void
    {
        $this->groups[] = $group;
    }

    /**
     * @return array<SidebarGroup>
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}
