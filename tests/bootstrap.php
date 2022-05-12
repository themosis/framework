<?php

define('DS', DIRECTORY_SEPARATOR);

define('THEMOSIS_PUBLIC_DIR', 'public');
define('THEMOSIS_ROOT', realpath(__DIR__ . '/application'));
define('CONTENT_DIR', 'content');
define('WP_CONTENT_DIR', realpath(THEMOSIS_ROOT . DS . THEMOSIS_PUBLIC_DIR . DS . CONTENT_DIR));

define('ABSPATH', __DIR__ . '/../vendor/johnpbloch/wordpress-core/');

$table_prefix = 'wp_';

define('DB_NAME', 'themosis_tests');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');

if (file_exists($autoload = realpath(THEMOSIS_ROOT . '/../../vendor/autoload.php'))) {
    require $autoload;
}

// Verify WordPress is installed before proceeding further...

if (file_exists($wpSettings = realpath(__DIR__ . '/../vendor/johnpbloch/wordpress-core/wp-settings.php'))) {
    require $wpSettings;
}
