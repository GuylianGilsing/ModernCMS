<?php

declare(strict_types=1);

namespace ModernCMS;

use function ModernCMS\Core\APIs\Assets\get_assets_store;
use function ModernCMS\Core\APIs\Hooks\Actions\dispatch_action;
use function ModernCMS\Core\APIs\Logging\create_logging_directory_if_not_exists;
use function ModernCMS\Core\APIs\Modules\load_registered_modules;

require_once __DIR__.'/../vendor/autoload.php';

/**
 * Base directory of the CMS system.
 */
define('CMS_BASE_DIR', __DIR__);

if (session_status() !== PHP_SESSION_ACTIVE)
{
    session_start();
}

require_once __DIR__.'/core/bootstrap/load-config-file.php';

if (DO_LOGGING)
{
    create_logging_directory_if_not_exists();
}

require_once __DIR__.'/core/bootstrap/register-actions.php';
require_once __DIR__.'/core/bootstrap/register-filters.php';

dispatch_action('mcms_core_hooks_initialized');

load_registered_modules();

dispatch_action('mcms_initialized');

require_once __DIR__.'/core/bootstrap/setup-application.php';
require_once __DIR__.'/core/bootstrap/setup-application/setup-cms-api.php';
require_once __DIR__.'/core/bootstrap/setup-application/setup-cms-backend.php';

dispatch_action('mcms_core_routes_registered');
