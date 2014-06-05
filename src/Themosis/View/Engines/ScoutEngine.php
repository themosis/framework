<?php
namespace Themosis\View\Engines;

use Themosis\View\Compilers\ICompiler;

class ScoutEngine extends PhpEngine {

    public function __construct(ICompiler $compiler)
    {
        $this->compiler = $compiler;
    }

} 