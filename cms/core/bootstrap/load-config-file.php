<?php

declare(strict_types=1);

if (file_exists(CMS_BASE_DIR.'/../config.php'))
{
    require_once CMS_BASE_DIR.'/../config.php';
}
else
{
    define('DEBUG_MODE', true);
    define('DO_LOGGING', true);
    define('DO_CSRF', true);

    define('INACTIVE_MODULES', []);

    define('DB_DRIVER', 'pdo_mysql');
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'modern_cms');

    define('AUTHENTICATION_COOKIES_LIFETIME', 60 * 60 * 24); // One day
    define('AUTHENTICATION_TOKEN_LIFETIME', 60 * 1); // One minute

    define('JWT_SECRET_KEY', md5('modern_cms'));
}
