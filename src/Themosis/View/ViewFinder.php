<?php

namespace Themosis\View;

use Illuminate\View\FileViewFinder;

class ViewFinder extends FileViewFinder
{
    /**
     * Return a list of found views.
     *
     * @return array
     */
    public function getViews()
    {
        return $this->views;
    }
}
