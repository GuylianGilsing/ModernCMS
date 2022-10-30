<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\Hooks;

use ModernCMS\Core\Abstractions\Hooks\HooksStoreInterface;
use ModernCMS\Core\Stores\Hooks\HooksStore;

function get_hooks_store(): HooksStoreInterface
{
    static $store;

    if (!isset($store))
    {
        $store = new HooksStore();
    }

    return $store;
}
