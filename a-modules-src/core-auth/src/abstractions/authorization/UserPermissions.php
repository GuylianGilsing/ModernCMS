<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Abstractions\Authorization;

enum UserPermissions: string
{
    case VIEW_ALL_USERS = 'view_all_users';
    case EDIT_USER_INFO = 'edit_user_info';
    case EDIT_ADMINISTRATORS = 'edit_administrators';
    case TRASH_ADMINISTRATORS = 'trash_administrators';
    case DELETE_ADMINISTRATORS = 'delete_administrators';
}
