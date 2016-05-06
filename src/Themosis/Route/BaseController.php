<?php

namespace Themosis\Route;

class BaseController extends Controller
{
    /**
     * Setup the layout used by the controller.
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = $this->view->make($this->layout);
        }
    }
}
