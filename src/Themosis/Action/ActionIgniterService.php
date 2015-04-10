<?php
namespace Themosis\Action;

use Themosis\Core\IgniterService;

class ActionIgniterService extends IgniterService
{
    /**
     * Make the Action API classes available.
     *
     * @return void
     */
    public function ignite()
    {
        $this->igniteAction();
    }

    /**
     * Build an instance of the Action API.
     *
     * @return \Themosis\Action\ActionBuilder
     */
    protected function igniteAction()
    {
        $this->app->bindShared('action', function($app)
        {
            /**
             * This creates an ActionBuilder instance with its dependencies.
             */
            return new ActionBuilder($app);
        });
    }
}