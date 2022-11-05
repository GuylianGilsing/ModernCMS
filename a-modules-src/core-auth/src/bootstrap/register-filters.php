<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Bootstrap;

use ModernCMS\Module\CoreAuth\Abstractions\Authorization\BackendPermissions;
use ModernCMS\Module\CoreAuth\Abstractions\Authorization\UserPermissions;
use ModernCMS\Module\CoreAuth\Abstractions\Authorization\UserRoles;
use ModernCMS\Module\CoreAuth\Migrations\BlackListedAUTHTokensMigration;
use ModernCMS\Module\CoreAuth\Migrations\UserPermissionsTableMigration;
use ModernCMS\Module\CoreAuth\Migrations\UsersTableMigration;

use function ModernCMS\Core\APIs\Hooks\Filters\register_filter;
use function ModernCMS\Core\APIs\Hooks\Filters\register_filter_callback;
use function ModernCMS\Core\APIs\Views\get_twig_environment_instance;
use function ModernCMS\Module\CoreAuth\APIs\Users\get_logged_in_user;

register_filter('mcms_auth_get_user_roles');

register_filter_callback('mcms_auth_get_user_roles', function (array $roles): array
{
    $roles[UserRoles::ADMINISTRATOR->value] = 'Administrator';

    return $roles;
});

register_filter('mcms_auth_get_user_permissions');

register_filter_callback('mcms_auth_get_user_permissions', function (array $permissions): array
{
    $permissions[BackendPermissions::VISIT_BACKEND->value] = 'Can visit backend';

    $permissions[UserPermissions::VIEW_ALL_USERS->value] = 'Can see all registered users';
    $permissions[UserPermissions::EDIT_USER_INFO->value] = 'Can edit user information';
    $permissions[UserPermissions::EDIT_ADMINISTRATORS->value] = 'Can edit administrator accounts';
    $permissions[UserPermissions::TRASH_ADMINISTRATORS->value] = 'Can put administrator accounts in the trash';
    $permissions[UserPermissions::DELETE_ADMINISTRATORS->value] = 'Can delete administrator accounts';

    return $permissions;
});

register_filter_callback('mcms_get_twig_template_folder_paths', function (array $paths)
{
    $paths[] = CORE_AUTH_BASE_DIR.'/views/templates';

    return $paths;
});

register_filter_callback('mcms_get_migrations', function (array $migrationClasses)
{
    $migrationClasses[] = new UsersTableMigration();
    $migrationClasses[] = new UserPermissionsTableMigration();
    $migrationClasses[] = new BlackListedAUTHTokensMigration();

    return $migrationClasses;
});

register_filter_callback('mcms_extend_cms_main_header_right_side', function ()
{
    $templateData = [
        'user' => get_logged_in_user()
    ];

    return get_twig_environment_instance()->render('/backend/partials/main_header-dropdown.twig', $templateData);
});

require_once __DIR__.'/register-filters/register-main_sidebar-items.php';
