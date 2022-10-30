<?php

declare(strict_types=1);

/**
 * Used for outputting any debug, or error, information on the CMS system frontend.
 */
define('DEBUG_MODE', false);

/**
 * Dictates if the CMS system will log information to log files.
 */
define('DO_LOGGING', true);

/**
 * Dictates if the CMS system will protect its own POST routes. IT IS RECOMMENDED TO LEAVE THIS `true`.
 */
define('DO_CSRF', true);

/**
 * Used for deactivating specific modules that the CMS system uses.
 */
define('INACTIVE_MODULES', []);

/**
 * Database driver (see doctrine DBAL configuration for all options)
 */
define('DB_DRIVER', 'pdo_mysql');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'modern-cms');

/**
 * Dictates the time (in seconds) refresh tokens and authentication cookies are valid and available.
 */
define('AUTHENTICATION_COOKIES_LIFETIME', 60 * 60 * 24); // One day

/**
 * Dictates the time (in seconds) the authentication token is valid.
 */
define('AUTHENTICATION_TOKEN_LIFETIME', 60 * 1); // One minute

/**
 * JWT secret key that is used to secure the token.
 */
define('JWT_SECRET_KEY', md5('REPLACE_THIS'));
