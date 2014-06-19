<?php
namespace Themosis\Route;

abstract class Controller
{
    /**
     * The layout used by the controller.
     *
     * @var \Themosis\View\View
     */
    protected $layout;

    /**
     * Create the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout() {}

    /**
     * Execute an action on the controller.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed isn't it a string ?
     */
    public function callAction($method, $parameters)
    {
        $this->setupLayout();

        $response = call_user_func_array(array($this, $method), $parameters);

        // If no response is returned from the controller action and a layout is being
        // used we will assume we want to just return the layout view as any nested
        // views were probably bound on this view during this controller actions.
        if(is_null($response) && !is_null($this->layout))
        {
            $response = $this->layout;
        }

        return $response;
    }
}