<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use ModernCMS\Core\Abstractions\Migrations\MigrationInterface;

final class UserPermissionsTableMigration implements MigrationInterface
{
    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('user_permissions'))
        {
            $schema->createTable('user_permissions');
        }

        $table = $schema->getTable('user_permissions');

        if (!$table->hasColumn('user_id'))
        {
            $column = $table->addColumn('user_id', Types::BIGINT);
            $column->setUnsigned(true);
            $column->setAutoincrement(true);

            $table->addForeignKeyConstraint(
                'users', ['user_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE']
            );
        }

        if (!$table->hasColumn('permission'))
        {
            $column = $table->addColumn('permission', Types::STRING);

            $column->setLength(120);
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('user_permissions'))
        {
            $schema->dropTable('user_permissions');
        }
    }
}
