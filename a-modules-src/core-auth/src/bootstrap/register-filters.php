<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth\Bootstrap;

use ModernCMS\Module\CoreAuth\Migrations\BlackListedAUTHTokensMigration;
use ModernCMS\Module\CoreAuth\Migrations\UsersTableMigration;

use function ModernCMS\Core\APIs\Hooks\Filters\register_filter_callback;
use function ModernCMS\Core\APIs\Views\get_twig_environment_instance;
use function ModernCMS\Module\CoreAuth\APIs\Users\get_logged_in_user;

register_filter_callback('mcms_get_twig_template_folder_paths', function (array $paths)
{
    $paths[] = CORE_AUTH_BASE_DIR.'/views/templates';

    return $paths;
});

register_filter_callback('mcms_get_migrations', function (array $migrationClasses)
{
    $migrationClasses[] = new UsersTableMigration();
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
