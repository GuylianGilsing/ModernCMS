<?php

declare(strict_types=1);

namespace ModernCMS\Module\CoreAuth;

use function ModernCMS\Core\APIs\Modules\register_module_bootstrapper;

define('CORE_AUTH_MODULE_NAME', 'core-auth');

register_module_bootstrapper(CORE_AUTH_MODULE_NAME, __DIR__.'/src/bootstrap.php');
