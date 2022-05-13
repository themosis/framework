<?php

define('DS', DIRECTORY_SEPARATOR);

define('THEMOSIS_PUBLIC_DIR', 'public');
define('THEMOSIS_ROOT', realpath(__DIR__ . '/application'));
define('CONTENT_DIR', 'content');
define('WP_CONTENT_DIR', realpath(THEMOSIS_ROOT . DS . THEMOSIS_PUBLIC_DIR . DS . CONTENT_DIR));

define('ABSPATH', __DIR__ . '/../vendor/johnpbloch/wordpress-core/');
define('WP_SITEURL', 'https://themosis.test');
define('WP_HOME', 'https://themosis.test');
define('WP_CONTENT_URL', 'https://themosis.test/content');

define('WP_DEFAULT_THEME', 'themosis-fake-theme');

$table_prefix = 'wp_';

define('DB_NAME', 'themosis_tests');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');

define('WP_INSTALLING', true);

// Fake we're using SSL.
$_SERVER['HTTPS'] = 1;

if (file_exists($autoload = realpath(THEMOSIS_ROOT . '/../../vendor/autoload.php'))) {
    require $autoload;
}

// Verify WordPress is installed before proceeding further...
if (function_exists('error_reporting')) {
    /*
     * Initialize error reporting to a known set of levels.
     *
     * This will be adapted in wp_debug_mode() located in wp-includes/load.php based on WP_DEBUG.
     * @see http://php.net/manual/en/errorfunc.constants.php List of known error levels.
     */
    error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR);
}

if (file_exists($wpSettings = realpath(__DIR__ . '/../vendor/johnpbloch/wordpress-core/wp-settings.php'))) {
    require $wpSettings;
}

if (file_exists($wpUpgrade = realpath(__DIR__.'/../vendor/johnpbloch/wordpress-core/wp-admin/includes/upgrade.php'))) {
    require $wpUpgrade;
}

wp_install(
    'Themosis Tests',
    'phpunit',
    'support@themosis.com',
    false,
    '',
    wp_generate_password(12, false),
);
