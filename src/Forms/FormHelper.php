<?php

namespace Themosis\Forms;

trait FormHelper
{
    /**
     * Build the view path.
     *
     *
     * @return string
     */
    protected function buildViewPath(string $theme, string $view)
    {
        return $theme.'.'.$view;
    }
}
