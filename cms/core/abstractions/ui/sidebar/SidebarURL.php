<?php

declare(strict_types=1);

namespace ModernCMS\Core\Abstractions\UI\Sidebar;

use ModernCMS\Core\Abstractions\UI\SortableInterface;

class SidebarURL implements SortableInterface
{
    protected string $key = '';
    protected string $name = '';
    protected string $url = '';

    public function __construct(string $key, string $name, string $url)
    {
        $this->key = $key;
        $this->name = $name;
        $this->url = $url;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getURL(): string
    {
        return $this->url;
    }
}
