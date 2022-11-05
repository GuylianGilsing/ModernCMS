<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\APIs\Users;

use Doctrine\DBAL\ParameterType;
use ErrorException;
use ModernCMS\Core\Abstractions\Pagination\PaginatedResult;
use ModernCMS\Module\CoreAuth\Abstractions\Users\User;

use function ModernCMS\Core\APIs\Database\get_database_connection_instance;
use function ModernCMS\Core\APIs\Mapping\get_array_mapper_instance;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\get_authentication_token_jwt;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\is_logged_in;
use function ModernCMS\Module\CoreAuth\APIs\Authentication\JWT\parse_jwt_token_string;

function get_user_by_id(int $id): ?User
{
    $queryBuilder = get_database_connection_instance()->createQueryBuilder();
    $result = $queryBuilder->select('*')
                            ->from('users')
                            ->where('id = :id')
                            ->setMaxResults(1)
                            ->setParameter('id', $id, ParameterType::INTEGER)
                            ->executeQuery();

    $resultColumn = $result->fetchAssociative();

    if (!is_array($resultColumn))
    {
        return null;
    }

    $user = get_array_mapper_instance()->fromArray($resultColumn, User::class);
    _set_stored_permissions_on_user($user);

    return $user;
}

function get_user_by_email(string $email): ?User
{
    $queryBuilder = get_database_connection_instance()->createQueryBuilder();
    $result = $queryBuilder->select('*')
                            ->from('users')
                            ->where('email = :email')
                            ->setMaxResults(1)
                            ->setParameter('email', $email, ParameterType::STRING)
                            ->executeQuery();

    $resultColumn = $result->fetchAssociative();

    if (!is_array($resultColumn))
    {
        return null;
    }

    $user = get_array_mapper_instance()->fromArray($resultColumn, User::class);
    _set_stored_permissions_on_user($user);

    return get_array_mapper_instance()->fromArray($resultColumn, User::class);
}

function get_logged_in_user(): ?User
{
    static $user;

    if (!isset($user))
    {
        if (!is_logged_in())
        {
            return null;
        }

        $jwt = get_authentication_token_jwt();
        $token = parse_jwt_token_string($jwt);

        if ($token === null)
        {
            return null;
        }

        if (!$token->claims()->has('uid'))
        {
            return false;
        }

        $userId = $token->claims()->get('uid', 0);

        if (!is_numeric($userId))
        {
            return null;
        }

        $user = get_user_by_id(intval($userId));
    }

    return $user;
}

function get_user_page(int $pageNumber = 1, int $maxItemsPerPage = 24): PaginatedResult
{
    $totalItems = _query_total_users_amount();
    $totalPages = intval(ceil($totalItems / $maxItemsPerPage));

    $users = _query_users_with_range($maxItemsPerPage, $pageNumber);

    return new PaginatedResult(
        $totalItems,
        $maxItemsPerPage,
        $totalPages,
        $pageNumber,
        $users
    );
}

/**
 * Queries the users table and returns the amount of users.
 */
function _query_total_users_amount(): int
{
    $connection = get_database_connection_instance();
    $queryBuilder = $connection->createQueryBuilder();

    $queryResult = $queryBuilder->select('COUNT(`id`) AS totalRows')
                                ->from('users')
                                ->executeQuery();

    $column = $queryResult->fetchAssociative();

    if (!is_array($column) || !array_key_exists('totalRows', $column))
    {
        return 0;
    }

    return $column['totalRows'];
}

/**
 * Queries the users table with a range and returns the results.
 *
 * @return array<User>
 */
function _query_users_with_range(int $itemAmount, int $pageNumber): array
{
    $connection = get_database_connection_instance();
    $queryBuilder = $connection->createQueryBuilder();

    if ($pageNumber <= 0)
    {
        $pageNumber = 1;
    }

    $queryResult = $queryBuilder->select('*')
                                ->from('users')
                                ->setFirstResult(($pageNumber - 1) * $itemAmount)
                                ->setMaxResults($itemAmount)
                                ->executeQuery();

    $columns = $queryResult->fetchAllAssociative();

    if (!is_array($columns))
    {
        return [];
    }

    $users = [];
    $mapper = get_array_mapper_instance();

    foreach ($columns as $column)
    {
        $user = $mapper->fromArray($column, User::class);
        _set_stored_permissions_on_user($user);

        $users[] = $user;
    }

    return $users;
}

/**
 * Attempts to create a new user.
 *
 * @param User $user the user abstraction that you want to create.
 *
 * @return ?User Returns the user when it could be created, `null` otherwise.
 */
function create_user(User $user): ?User
{
    $connection = get_database_connection_instance();
    $queryBuilder = $connection->createQueryBuilder();

    $rowsAffected = $queryBuilder->insert('users')
                                ->values([
                                    'firstname' => ':firstname',
                                    'lastname' => ':lastname',
                                    'email' => ':email',
                                    'password' => ':password',
                                    'role' => ':role',
                                ])
                                ->setParameter('firstname', $user->getFirstname())
                                ->setParameter('lastname', $user->getLastname())
                                ->setParameter('email', $user->getEmail())
                                ->setParameter('password', $user->getPassword())
                                ->setParameter('role', $user->getRole())
                                ->executeStatement();

    if ($rowsAffected === 0)
    {
        return null;
    }

    $insertedUserId = $connection->lastInsertId();

    if (!is_numeric($insertedUserId))
    {
        return null;
    }

    $updatedUser = get_user_by_id(intval($insertedUserId));

    if ($updatedUser === null)
    {
        return null;
    }

    $updatedUser->setPermissions($user->getPermissions());

    if (!update_user_permissions($updatedUser))
    {
        $updatedUser->setPermissions([]);
    }

    return $updatedUser;
}

/**
 * Attempts to update an existing user.
 *
 * @param User $user the user abstraction that you want to update.
 *
 * @throws ErrorException when a user abstraction is given that does not exist.
 *
 * @return bool Returns true if the update succeeded, false otherwise.
 */
function update_user(User $user): bool
{
    if (get_user_by_id($user->getId()) === null)
    {
        throw new ErrorException("User with ID \"{$user->getId()}\" does not exist.");
    }

    $connection = get_database_connection_instance();
    $queryBuilder = $connection->createQueryBuilder();

    $rowsAffected = $queryBuilder->update('users', 'u')
                                ->set('u.firstname', ':firstname')
                                ->set('u.lastname', ':lastname')
                                ->set('u.email', ':email')
                                ->set('u.password', ':password')
                                ->set('u.role', ':role')
                                ->where('u.id = :id')
                                ->setParameter('id', $user->getId())
                                ->setParameter('firstname', $user->getFirstname())
                                ->setParameter('lastname', $user->getLastname())
                                ->setParameter('email', $user->getEmail())
                                ->setParameter('password', $user->getPassword())
                                ->setParameter('role', $user->getRole())
                                ->executeStatement();

    if ($rowsAffected === 0 && !update_user_permissions($user))
    {
        return false;
    }

    return true;
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
