<?php

declare(strict_types=1);

namespace ModernCMS\Core\Abstractions\UI\Sidebar;

use ModernCMS\Core\Abstractions\UI\SortableInterface;

class SidebarGroup implements SortableInterface
{
    protected string $key = '';
    protected string $name = '';
    protected string $url = '';

    /**
     * @var array<SidebarURL> $urls
     */
    protected array $urls = [];

    public function __construct(string $key, string $name, string $url = '')
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

    public function addURL(SidebarURL $url): void
    {
        $this->urls[] = $url;
    }

    /**
     * @return array<SidebarURL>
     */
    public function getURLs(): array
    {
        return $this->urls;
    }
}
