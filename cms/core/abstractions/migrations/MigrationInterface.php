<?php

declare(strict_types=1);

namespace ModernCMS\Core\Abstractions\Migrations;

use Doctrine\DBAL\Schema\Schema;

interface MigrationInterface
{
    public function up(Schema $schema): void;
    public function down(Schema $schema): void;
}
