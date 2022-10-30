<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Bootstrap;

use ModernCMS\Core\Abstractions\UI\Sidebar\SidebarGroup;
use ModernCMS\Core\Abstractions\UI\Sidebar\SidebarSection;
use ModernCMS\Core\Abstractions\UI\Sidebar\SidebarURL;

use function ModernCMS\Core\APIs\Hooks\Filters\register_filter_callback;

/**
 * @param array<string, SidebarSection> $items
 */
register_filter_callback('mcms_ui_main_sidebar_items', function (array $items)
{
    if (array_key_exists('modules', $items))
    {
        $usersGroup = new SidebarGroup('users', 'Users', '/users');

        $usersGroup->addURL(new SidebarURL('overview', 'Overview', '/users'));
        $usersGroup->addURL(new SidebarURL('create-new', 'Create new', '/users/new'));

        $items['modules']->addGroup($usersGroup);
    }

    return $items;
});
