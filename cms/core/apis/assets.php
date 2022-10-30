<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\Assets;

use ModernCMS\Core\Abstractions\Assets\AssetsStoreInterface;
use ModernCMS\Core\Stores\Assets\AssetsStore;

function get_assets_store(): AssetsStoreInterface
{
    static $store;

    if (!isset($store))
    {
        $store = new AssetsStore();
    }

    return $store;
}
