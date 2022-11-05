<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Abstractions\Authorization;

interface RolePermissionStoreInterface
{
    /**
     * @param array<string> $permissions
     */
    public function setPermissionsForRole(string $role, array $permissions): void;

    /**
     * @return array<string>
     */
    public function getPermissionsFromRole(string $role): array;
}
