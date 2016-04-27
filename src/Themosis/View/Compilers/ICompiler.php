<?php

namespace Themosis\View\Compilers;

interface ICompiler
{
    /**
     * Compile the view at the given path.
     *
     * @param string $path
     */
    public function compile($path);
}
