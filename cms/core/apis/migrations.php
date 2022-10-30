<?php

declare(strict_types=1);

namespace ModernCMS\Core\APIs\Migrations;

use ModernCMS\Core\Abstractions\Migrations\MigrationDirection;
use ModernCMS\Core\Abstractions\Migrations\MigrationInterface;

use function ModernCMS\Core\APIs\Database\get_database_connection_instance;
use function ModernCMS\Core\APIs\Hooks\Filters\dispatch_filter;
use function ModernCMS\Core\APIs\Logging\log_warning_message;

function migrate_database(MigrationDirection $direction): void
{
    $connection = get_database_connection_instance();
    $schemaManager = $connection->createSchemaManager();

    $newSchema = $schemaManager->createSchema();

    foreach (dispatch_filter('mcms_get_migrations', []) as $migration)
    {
        if (!is_object($migration))
        {
            log_warning_message(
                'Migration "'.$migration.'" is not a class that implements the "'.MigrationInterface::class.'" interface.'
            );
            continue;
        }

        if (!($migration instanceof MigrationInterface))
        {
            log_warning_message(
                'Migration "'.$migration.'" does not implement the "'.MigrationInterface::class.'" interface.'
            );
            continue;
        }

        $migration->{$direction->value}($newSchema);
    }

    $schemaManager->migrateSchema($newSchema);
}
