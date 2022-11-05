<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\APIs\Authorization;

use function ModernCMS\Core\APIs\Hooks\Filters\dispatch_filter;

/**
 * Retrieves all registered roles.
 *
 * @return array<string, string>
 */
function get_registered_roles(): array
{
    static $roles = [];

    if (empty($roles))
    {
        $roles = dispatch_filter('mcms_auth_get_user_roles', []);
    }

    return $roles;
}

/**
 * Retrieves all registered permissions.
 *
 * @return array<string, string>
 */
function get_registered_permissions(): array
{
    static $permissions = [];

    if (empty($permissions))
    {
        $permissions = dispatch_filter('mcms_auth_get_user_permissions', []);
    }

    return $permissions;
}
