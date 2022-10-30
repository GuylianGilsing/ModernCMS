<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use ModernCMS\Core\Abstractions\Migrations\MigrationInterface;

final class BlackListedAUTHTokensMigration implements MigrationInterface
{
    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('blacklisted_auth_tokens'))
        {
            $schema->createTable('blacklisted_auth_tokens');
        }

        $table = $schema->getTable('blacklisted_auth_tokens');

        if (!$table->hasColumn('token_id'))
        {
            $column = $table->addColumn('token_id', Types::STRING);
            $column->setLength(255);

            $table->addUniqueConstraint(['token_id']);
        }

        if (!$table->hasColumn('token_type'))
        {
            $column = $table->addColumn('token_type', Types::STRING);
            $column->setLength(255);
        }

        if (!$table->hasColumn('blacklisted_at'))
        {
            $table->addColumn('blacklisted_at', Types::DATETIME_IMMUTABLE);
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('blacklisted_auth_tokens'))
        {
            $schema->dropTable('blacklisted_auth_tokens');
        }
    }
}
