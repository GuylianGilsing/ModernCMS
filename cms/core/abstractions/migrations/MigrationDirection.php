<?php

declare(strict_types=1);

namespace ModernCMS\Core\Abstractions\Migrations;

enum MigrationDirection: string
{
    case UP = 'up';
    case DOWN = 'down';
}
