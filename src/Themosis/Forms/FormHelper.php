<?php

namespace Themosis\Forms;

trait FormHelper
{
    /**
     * Build the view path.
     *
     * @param string $theme
     * @param string $view
     *
     * @return string
     */
    protected function buildViewPath(string $theme, string $view)
    {
        return $theme.'.'.$view;
    }
}
