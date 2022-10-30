<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\Database;

use Doctrine\DBAL\Connection;

function get_database_connection_instance(): Connection
{
    static $connection;

    if (!isset($connection))
    {
        $connectionOptions = [
            'driver' => DB_DRIVER,
            'host' => DB_HOST,
            'user' => DB_USER,
            'password' => DB_PASSWORD,
            'dbname' => DB_NAME,
        ];

        $connection = \Doctrine\DBAL\DriverManager::getConnection($connectionOptions);
    }

    return $connection;
}
