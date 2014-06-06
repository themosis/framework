<?php
namespace Themosis\View\Compilers;

abstract class Compiler {

    /**
     * View storage path.
     *
     * @var string
     */
    protected $storage;

    /**
     * Define children compiler constructor.
     *
     * @param string $storage
     */
    public function __construct($storage = null)
    {
        $this->storage = $storage;
    }

    /**
     * Check if a compiled view is expired or not.
     *
     * @param string $path
     * @return bool
     */
    public function isExpired($path)
    {
        $compiled = $this->getCompiledPath($path);

        // If the compiled view doesn't exists, return.
        if(!$this->storage || !file_exists($compiled)){
            return true;
        }

        // If the view code has changed, mark it as expired.
        $lastModified = filemtime($path);

        return $lastModified >= filemtime($compiled);
    }

    /**
     * Return the compiled view path.
     *
     * @param string $path The original view path.
     * @return string
     */
    public function getCompiledPath($path)
    {
        return $this->storage.md5($path);
    }

} 