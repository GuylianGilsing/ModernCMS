<?php

declare(strict_types=1);

namespace ModernCMS\Core\Bootstrap;

use ModernCMS\Core\Abstractions\Assets\AssetsStoreInterface;
use ModernCMS\Core\Abstractions\Assets\AssetType;

use function ModernCMS\Core\APIs\Hooks\Actions\register_action_callback;

register_action_callback('mcms_register_assets', function (AssetsStoreInterface $store)
{
    $store->register('core', AssetType::CSS, 'main-css', CMS_BASE_DIR.'/core/assets/css/main.css');

    $store->register('core', AssetType::IMAGE, 'logo-blue-svg', CMS_BASE_DIR.'/core/assets/img/logos/logo-blue.svg');
    $store->register('core', AssetType::IMAGE, 'logo-gray_cutout-svg', CMS_BASE_DIR.'/core/assets/img/logos/logo-gray_cutout.svg');
});
