<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\APIs\Authorization;

use ErrorException;
use ModernCMS\Module\CoreAuth\Abstractions\Authorization\RolePermissionStoreInterface;
use ModernCMS\Module\CoreAuth\Abstractions\Users\User;
use ModernCMS\Module\CoreAuth\Stores\Authorization\RolePermissionStore;

use function ModernCMS\Core\APIs\Database\get_database_connection_instance;
use function ModernCMS\Core\APIs\Hooks\Filters\dispatch_filter;
use function ModernCMS\Module\CoreAuth\APIs\Users\get_user_by_id;

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

function get_role_permissions_store(): RolePermissionStoreInterface
{
    static $store;

    if (!isset($store))
    {
        $store = new RolePermissionStore();
    }

    return $store;
}

/**
 * @param array<string> $permissions
 */
function set_role_permissions(string $role, array $permissions): void
{
    get_role_permissions_store()->setPermissionsForRole($role, $permissions);
}

/**
 * @return array<string>
 */
function get_role_permissions(string $role): array
{
    return get_role_permissions_store()->getPermissionsFromRole($role);
}

/**
 * @param int $id The user ID.
 *
 * @return array<string>
 */
function get_user_permissions(int $id): array
{
    $connection = get_database_connection_instance();
    $queryBuilder = $connection->createQueryBuilder();

    $result = $queryBuilder->select('permission')
                            ->from('user_permissions')
                            ->where('user_id = :id')
                            ->setParameter('id', $id)
                            ->executeQuery();

    $permissions = [];

    foreach ($result->fetchAllAssociative() as $column)
    {
        $permissions[] = $column['permission'];
    }

    return $permissions;
}

function _set_stored_permissions_on_user(User $user): void
{
    $permissions = get_user_permissions($user->getId());
    $user->setPermissions($permissions);
}

/**
 * Attempts to update the permissions of an existing user.
 *
 * @param User $user the user abstraction that you want to update.
 *
 * @throws ErrorException when a user abstraction is given that does not exist.
 *
 * @return bool Returns true if the update succeeded, false otherwise.
 */
function update_user_permissions(User $user): bool
{
    _delete_non_selected_user_permissions($user->getId(), $user->getPermissions());

    if (get_user_by_id($user->getId()) === null)
    {
        throw new ErrorException("User with ID \"{$user->getId()}\" does not exist.");
    }

    $existingPermissions = get_user_permissions($user->getId());

    $connection = get_database_connection_instance();

    foreach ($user->getPermissions() as $permission)
    {
        if (!in_array($permission, $existingPermissions))
        {
            $queryBuilder = $connection->createQueryBuilder();
            $inserted = $queryBuilder->insert('user_permissions')
                                        ->values([
                                            'user_id' => ':id',
                                            'permission' => ':permission'
                                        ])
                                        ->where('user_id = :id')
                                        ->setParameter('id', $user->getId())
                                        ->setParameter('permission', $permission)
                                        ->executeStatement();

            if ($inserted === 0)
            {
                return false;
            }
        }
    }

    return true;
}

function _delete_non_selected_user_permissions(int $userId, array $permissions): void
{
    $connection = get_database_connection_instance();
    $queryBuilder = $connection->createQueryBuilder();

    if (count($permissions) === 0)
    {
        $queryBuilder->delete('user_permissions')
                    ->where('user_id = :id')
                    ->setParameter('id', $userId)
                    ->executeStatement();
        return;
    }

    $queryBuilder->delete('user_permissions')
                    ->where(
                        $queryBuilder->expr()->and(
                            $queryBuilder->expr()->eq('user_id', ':id'),
                            $queryBuilder->expr()->notIn('permission', '(:permissions)')
                        )
                    )
                    ->setParameter('id', $userId)
                    ->setParameter('permissions', $permissions)
                    ->executeStatement();
}
