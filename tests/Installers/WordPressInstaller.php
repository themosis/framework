<?php

namespace Themosis\Tests\Installers;

class WordPressInstaller
{
    private static ?self $instance = null;

    private function __construct(private WordPressConfiguration $wordPressConfiguration)
    {
        $this->installWordPress();
    }

    public static function make(?WordPressConfiguration $configuration = null): self
    {
        if (static::$instance) {
            return static::$instance;
        }

        return static::$instance = new self(
            $configuration ?? WordPressConfiguration::make(),
        );
    }

    public function refresh(): void
    {
        $this->uninstallWordPress();
        $this->install();
    }

    public function configuration(): WordPressConfiguration
    {
        return $this->wordPressConfiguration;
    }

    private function installWordPress(): void
    {
        $this->setDefaultConstants();
        $this->setServerVariables();
        $this->setIncludePaths();
        $this->install();
    }

    private function uninstallWordPress(): void
    {
        global $wpdb;

        $tables = array_values($wpdb->tables());

        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table;");
        }
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
        define('WP_SITEURL', $this->wordPressConfiguration->siteUrl());
        define('WP_HOME', $this->wordPressConfiguration->homeUrl());
        define('WP_CONTENT_URL', $this->wordPressConfiguration->homeUrl() . DS . CONTENT_DIR);

        /**
         * Database.
         */
        define('DB_NAME', $this->wordPressConfiguration->databaseName());
        define('DB_USER', $this->wordPressConfiguration->databaseUser());
        define('DB_PASSWORD', $this->wordPressConfiguration->databasePassword());
        define('DB_HOST', $this->wordPressConfiguration->databaseHost());
        define('DB_CHARSET', $this->wordPressConfiguration->databaseCharset());
        define('DB_COLLATE', $this->wordPressConfiguration->databaseCollate());

        /**
         * Theme.
         */
        define('WP_DEFAULT_THEME', $this->wordPressConfiguration->defaultTheme());

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
