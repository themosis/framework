<?php
namespace Themosis\View\Engines;

use Themosis\View\Compilers\ICompiler;

class ScoutEngine extends PhpEngine {

    /**
     * The Scout compiler instance.
     *
     * @var \Themosis\View\Compilers\ICompiler
     */
    protected $compiler;

    /**
     * A list of compiled views. Keep a copy
     * of their original paths.
     *
     * @var array
     */
    protected $lastCompiled = array();

    /**
     * Build a ScoutEngine instance.
     *
     * @param ICompiler $compiler
     */
    public function __construct(ICompiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Get the evaluated content of the view.
     *
     * @param string $path
     * @param array $data
     * @return string
     */
    public function get($path, array $data = array())
    {
        // Keep a copy of the original path so if there is an
        // error, we can tell the user which view is wrong.
        $this->lastCompiled[] = $path;

        // Compile the view if it's expired or do not exists.
        if($this->compiler->isExpired($path)){
            $this->compiler->compile($path);
        }

        // Get the compiled view path.
        $compiled = $this->compiler->getCompiledPath($path);

        // Evaluate the compiled view.
        $content = $this->evaluatePath($compiled, $data);

        // Remove the currently compiled view from the lastCompiled list.
        array_pop($this->lastCompiled);

        return $content;
    }

    /**
     * Handle a view exception.
     *
     * @param \Exception $e
     * @return void
     * @throws $e
     */
    protected function handleException($e)
    {
        $e = new \ErrorException($this->getMessage($e), 0, 1, $e->getFile(), $e->getLine(), $e);

        ob_get_clean(); throw $e;
    }

    /**
     * Get the exception message for an exception.
     *
     * @param \Exception $e
     * @return string
     */
    protected function getMessage($e)
    {
        return $e->getMessage().' (View: '.realpath(end($this->lastCompiled)).')';
    }

    /**
     * Get the compiler implementation.
     *
     * @return \Themosis\View\Compilers\ICompiler
     */
    public function getCompiler()
    {
        return $this->compiler;
    }

} 