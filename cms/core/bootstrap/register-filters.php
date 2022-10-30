<?php

declare(strict_types=1);

namespace ModernCMS\Core\Bootstrap;

use function ModernCMS\Core\APIs\Hooks\Filters\register_filter;
use function ModernCMS\Core\APIs\Hooks\Filters\register_filter_callback;

register_filter('mcms_get_twig_template_folder_paths');
register_filter('mcms_get_migrations');
register_filter('mcms_extend_cms_main_header_right_side');
register_filter('mcms_ui_main_sidebar_items');

register_filter_callback('mcms_get_twig_template_folder_paths', function(array $paths)
{
    $paths[] = CMS_BASE_DIR.'/core/views/templates';

    return $paths;
});

require_once __DIR__.'/register-filters/register-main_sidebar-items.php';
