<?php

declare(strict_types=1);

use function ModernCMS\Core\APIs\Application\application_cleanup;
use function ModernCMS\Core\APIs\Application\get_application_instance;

define('PUBLIC_FOLDER', __DIR__);

require_once __DIR__.'/../cms/bootstrap.php';

$app = get_application_instance();
$app->run();

application_cleanup();
