<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use ModernCMS\Core\Abstractions\Migrations\MigrationInterface;

final class UsersTableMigration implements MigrationInterface
{
    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('users'))
        {
            $schema->createTable('users');
        }

        $table = $schema->getTable('users');

        if (!$table->hasColumn('id'))
        {
            $column = $table->addColumn('id', Types::BIGINT);
            $column->setUnsigned(true);
            $column->setAutoincrement(true);

            $table->setPrimaryKey(['id']);
        }

        if (!$table->hasColumn('firstname'))
        {
            $column = $table->addColumn('firstname', Types::STRING);
            $column->setLength(100);
        }

        if (!$table->hasColumn('middlenames'))
        {
            $column = $table->addColumn('middlenames', Types::STRING);
            $column->setNotnull(false);
            $column->setDefault(null);
        }

        if (!$table->hasColumn('lastname'))
        {
            $column = $table->addColumn('lastname', Types::STRING);
            $column->setLength(100);
        }

        if (!$table->hasColumn('email'))
        {
            $column = $table->addColumn('email', Types::STRING);

            $table->addUniqueConstraint(['email']);
        }

        if (!$table->hasColumn('password'))
        {
            $column = $table->addColumn('password', Types::TEXT);
        }

        if (!$table->hasColumn('role'))
        {
            $column = $table->addColumn('role', Types::STRING);

            $column->setLength(100);
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('users'))
        {
            $schema->dropTable('users');
        }
    }
}
