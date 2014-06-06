<?php
namespace Themosis\View\Compilers;

use \Exception;

class ScoutCompiler extends Compiler implements ICompiler {

    /**
     * The original view path.
     *
     * @var string
     */
    protected $path;

    /**
     * Compile the view at the given path.
     *
     * @param string $path
     * @return void
     */
    public function compile($path)
    {
        if($path){
            $this->setPath($path);
        }

        // Compile the view content.
        $content = $this->compileString($this->getViewContent($path));

        if(!is_null($this->storage)){

            // Store the compiled view.
            file_put_contents($this->getCompiledPath($this->getPath()), $content);

        }
    }

    /**
     * Compile the scout view file.
     *
     * @param string $content
     * @return string
     */
    public function compileString($content)
    {
        return 'Super Yo';
    }

    /**
     * Set the original view path.
     *
     * @param $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Return the original view path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Return the defined view content.
     *
     * @param string $path
     * @throws \Exception
     * @return string
     */
    private function getViewContent($path)
    {
        if(is_file($path)){
            return file_get_contents($path);
        }

        throw new Exception('View file does not exists at this location: '.$path);
    }

}