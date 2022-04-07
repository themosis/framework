<?php

namespace Themosis\Tests;

use Illuminate\Config\Repository;
use Themosis\Core\Application as CoreApplication;

trait Application
{
    /**
     * @var \Themosis\Core\Application
     */
    protected $application;

    /**
     * Return a core application instance.
     *
     * @return CoreApplication
     */
    public function getApplication()
    {
        if (! is_null($this->application)) {
            return $this->application;
        }

        $this->application = new CoreApplication();

        $this->application->bind('config', function () {
            $config = new Repository();
            $config->set('app.locale', 'en_US');

            return $config;
        });

        return $this->application;
    }
}
