<?php

namespace Themosis\Tests\Installers;

class WordPressInstaller
{
    public function __construct(private WordPressConfiguration $wordPressConfiguration)
    {
    }

    public function installWordPress(): void
    {
        $this->setDefaultConstants();
        $this->setServerVariables();
        $this->setIncludePaths();
        $this->install();
    }

    public function uninstallWordPress(): void
    {
        // Drop database tables.
    }

    private function setDefaultConstants(): void
    {
        /**
         * Paths.
         */
        define('ABSPATH', __DIR__ . '/../../vendor/johnpbloch/wordpress-core/');
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

    private function setServerVariables(): void
    {
        /**
         * Trick the is_ssl() function.
         */
        $_SERVER['HTTPS'] = 1;
    }

    private function setIncludePaths(): void
    {
        $table_prefix = $this->wordPressConfiguration->tablePrefix();

        if (file_exists($wpSettings = realpath(__DIR__ . '/../../vendor/johnpbloch/wordpress-core/wp-settings.php'))) {
            require $wpSettings;
        }

        if (file_exists($wpUpgrade = realpath(__DIR__ . '/../../vendor/johnpbloch/wordpress-core/wp-admin/includes/upgrade.php'))) {
            require $wpUpgrade;
        }
    }

    private function install(): void
    {
        \wp_install(
            $this->wordPressConfiguration->blogTitle(),
            $this->wordPressConfiguration->username(),
            $this->wordPressConfiguration->email(),
            $this->wordPressConfiguration->isPublic(),
            '',
            $this->wordPressConfiguration->password() ?? wp_generate_password(12, false),
        );
    }
}
