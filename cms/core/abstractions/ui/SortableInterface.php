<?php

declare(strict_types=1);

namespace ModernCMS\Core\Abstractions\UI;

/**
 * Interface that, when implemented, can be used inside UI sorting system.
 */
interface SortableInterface
{
    public function getKey(): string;
}
