<?php

namespace Themosis\Tests;

trait HasWordPress
{
    public function installWordPress(): void
    {
        $this->setDefaultConstants();

        $table_prefix = 'wp_';

        /**
         * Trick the is_ssl() function.
         */
        $_SERVER['HTTPS'] = 1;
    }

    public function uninstallWordPress(): void
    {
    }

    private function setDefaultConstants(): void
    {
        /**
         * Paths.
         */
        define('ABSPATH', __DIR__ . '/../vendor/johnpbloch/wordpress-core/');
        define('CONTENT_DIR', 'content');
        define('WP_CONTENT_DIR', realpath(THEMOSIS_ROOT . DS . THEMOSIS_PUBLIC_DIR . DS . CONTENT_DIR));

        /**
         * URLs.
         */
        define('WP_SITEURL', 'https://themosis.test');
        define('WP_HOME', 'https://themosis.test');
        define('WP_CONTENT_URL', 'https://themosis.test/content');

        /**
         * Database.
         */
        define('DB_NAME', 'themosis_tests');
        define('DB_USER', 'root');
        define('DB_PASSWORD', 'root');
        define('DB_HOST', 'localhost');
        define('DB_CHARSET', 'utf8mb4');
        define('DB_COLLATE', 'utf8mb4_unicode_ci');

        /**
         * Theme.
         */
        define('WP_DEFAULT_THEME', 'themosis-fake-theme');

        /**
         * Installation.
         */
        define('WP_INSTALLING', true);
    }
}
