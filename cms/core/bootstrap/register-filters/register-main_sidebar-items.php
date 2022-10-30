<?php

declare(strict_types=1);

namespace ModernCMS\Core\Bootstrap;

use ModernCMS\Core\Abstractions\UI\Sidebar\SidebarGroup;
use ModernCMS\Core\Abstractions\UI\Sidebar\SidebarSection;
use ModernCMS\Core\Abstractions\UI\Sidebar\SidebarURL;

use function ModernCMS\Core\APIs\Hooks\Filters\register_filter_callback;

register_filter_callback('mcms_ui_main_sidebar_items', function (array $items)
{
    // Modules
    $modulesSection = new SidebarSection('modules', 'Modules');

    // Settings
    $settingsSection = new SidebarSection('settings', 'Settings');

    $contentGroup = new SidebarGroup('frontend', 'Frontend');
    $contentGroup->addURL(new SidebarURL('general', 'General', '/settings/frontend/general'));

    $settingsSection->addGroup($contentGroup);

    $items[$modulesSection->getKey()] = $modulesSection;
    $items[$settingsSection->getKey()] = $settingsSection;

    return $items;
});
