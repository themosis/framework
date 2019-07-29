<?php

namespace Themosis\Core;

use Composer\Script\Event;

class ComposerScripts
{
    /**
     * Handle the post-install Composer event.
     *
     * @param \Composer\Script\Event $event
     */
    public static function postInstall(Event $event)
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

        static::clearCompiled();
    }

    /**
     * Handle the post-update Composer event.
     *
     * @param \Composer\Script\Event $event
     */
    public static function postUpdate(Event $event)
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

        static::clearCompiled();
    }

    /**
     * Handle the post-autoload-dump Composer event.
     *
     * @param \Composer\Script\Event $event
     */
    public static function postAutoloadDump(Event $event)
    {
        require_once $event->getComposer()->getConfig()->get('vendor-dir').'/autoload.php';

        static::clearCompiled();
    }

    /**
     * Clear the cached Themosis bootstrapping files.
     */
    protected static function clearCompiled()
    {
        /*----------------------------------------------------*/
        // Directory separator
        /*----------------------------------------------------*/
        defined('DS') ? DS : define('DS', DIRECTORY_SEPARATOR);

        /*----------------------------------------------------*/
        // Application paths
        /*----------------------------------------------------*/
        defined('THEMOSIS_PUBLIC_DIR') ? THEMOSIS_PUBLIC_DIR : define('THEMOSIS_PUBLIC_DIR', 'htdocs');
        defined('THEMOSIS_ROOT') ? THEMOSIS_ROOT : define('THEMOSIS_ROOT', realpath(getcwd()));
        defined('CONTENT_DIR') ? CONTENT_DIR : define('CONTENT_DIR', 'content');
        defined('WP_CONTENT_DIR') ? WP_CONTENT_DIR : define('WP_CONTENT_DIR', realpath(THEMOSIS_ROOT.DS.THEMOSIS_PUBLIC_DIR.DS.CONTENT_DIR));

        $app = new Application(getcwd());

        if (file_exists($servicesPath = $app->getCachedServicesPath())) {
            @unlink($servicesPath);
        }

        if (file_exists($packagesPath = $app->getCachedPackagesPath())) {
            @unlink($packagesPath);
        }
    }
}
