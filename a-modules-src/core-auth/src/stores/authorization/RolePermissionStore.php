<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Stores\Authorization;

use ModernCMS\Module\CoreAuth\Abstractions\Authorization\RolePermissionStoreInterface;

final class RolePermissionStore implements RolePermissionStoreInterface
{
    /**
     * @var array<string, array<string>> $permissions
     */
    private array $permissions = [];

    /**
     * @param array<string> $permissions
     */
    public function setPermissionsForRole(string $role, array $permissions): void
    {
        $this->permissions[$role] = $permissions;
    }

    /**
     * @return array<string>
     */
    public function getPermissionsFromRole(string $role): array
    {
        if (!array_key_exists($role, $this->permissions))
        {
            return [];
        }

        return $this->permissions[$role];
    }
}
