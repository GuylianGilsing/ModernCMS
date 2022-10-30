<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Routes\Backend\Users\Overview;

use ModernCMS\Core\Abstractions\Pagination\PaginatedResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function ModernCMS\Core\APIs\Database\get_database_connection_instance;
use function ModernCMS\Core\APIs\Passwords\hash_password;
use function ModernCMS\Core\APIs\Views\view_response;
use function ModernCMS\Module\CoreAuth\APIs\Users\get_user_page;

final class GET
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $templateData = [];
        // $this->insertFakeUsersIntoDatabase();

        $templateData['usersPage'] = $this->getUserPageBasedOnRequest($request);

        return view_response('/backend/users/overview.twig', $templateData);
    }

    private function getUserPageBasedOnRequest(ServerRequestInterface $request): PaginatedResult
    {
        $currentPage = 1;
        $itemsPerPage = 12;

        $queryParams = $request->getQueryParams();

        if (array_key_exists('s', $queryParams))
        {
            if (array_key_exists('page', $queryParams) && is_numeric($queryParams['page']))
            {
                $currentPage = intval($queryParams['page']);
            }

            if (array_key_exists('amount', $queryParams) && is_numeric($queryParams['amount']))
            {
                $itemsPerPage = intval($queryParams['amount']);
            }
        }

        return get_user_page($currentPage, $itemsPerPage);
    }

    private function insertFakeUsersIntoDatabase(): void
    {
        $faker = \Faker\Factory::create();
        $connection = get_database_connection_instance();

        for ($i = 0; $i < 25; $i += 1)
        {
            $queryBuilder = $connection->createQueryBuilder();

            $queryBuilder->insert('users')
                        ->values([
                            'firstname' => ':firstname',
                            'lastname' => ':lastname',
                            'email' => ':email',
                            'password' => ':password',
                        ])
                        ->setParameter('firstname', $faker->firstName())
                        ->setParameter('lastname', $faker->lastName())
                        ->setParameter('email', $faker->unique()->email())
                        ->setParameter('password', hash_password($faker->unique()->password()))
                        ->executeStatement();
        }
    }
}
