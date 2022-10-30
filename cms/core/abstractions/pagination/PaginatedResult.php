<?php

declare(strict_types=1);

namespace ModernCMS\Core\Abstractions\Pagination;

class PaginatedResult
{
    protected int $itemAmount = 0;
    protected int $itemsPerPage = 0;
    protected int $totalPages = 0;
    protected int $currentPage = 0;

    /**
     * @var array<mixed> $items;
     */
    protected array $items = [];

    /**
     * @param array<mixed> $items
     */
    public function __construct(
        int $itemAmount = 0,
        int $itemsPerPage = 0,
        int $totalPages = 0,
        int $currentPage = 0,
        array $items = []
    )
    {
        $this->itemAmount = $itemAmount;
        $this->itemsPerPage = $itemsPerPage;
        $this->totalPages = $totalPages;
        $this->currentPage = $currentPage;
        $this->items = $items;
    }

    public function getItemAmount(): int
    {
        return $this->itemAmount;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @return array<mixed>
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
