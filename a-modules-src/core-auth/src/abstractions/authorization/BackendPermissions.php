<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Abstractions\Authorization;

enum BackendPermissions: string
{
    case VISIT_BACKEND = 'visit_backend';
}
