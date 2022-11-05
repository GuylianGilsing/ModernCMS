<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Abstractions\Authorization;

enum UserRoles: string
{
    case ADMINISTRATOR = 'administrator';
}
