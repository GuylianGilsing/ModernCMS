<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\APIs\Users;

use Doctrine\DBAL\ParameterType;
use ModernCMS\Core\Abstractions\Pagination\PaginatedResult;
use ModernCMS\Module\CoreAuth\Abstractions\Users\User;

use function ModernCMS\Core\APIs\Cookie\cookie_exists;
use function ModernCMS\Core\APIs\Cookie\get_cookie_data;
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

    return get_array_mapper_instance()->fromArray($resultColumn, User::class);
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
        $users[] = $mapper->fromArray($column, User::class);
    }

    return $users;
}
