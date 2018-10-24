<?php

namespace Themosis\View;

use Illuminate\View\FileViewFinder as IlluminateFileViewFinder;

class FileViewFinder extends IlluminateFileViewFinder
{
    /**
     * Return located views.
     *
     * @return array
     */
    public function getViews()
    {
        return $this->views;
    }
}
