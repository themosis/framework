<?php
namespace Themosis\View;

defined('DS') or die('No direct script access.');

class ViewRenderer
{
	/**
	 * View data object
	*/
	private $view;

	/**
	 * View id
	*/
	private $viewID;

	/**
	 * Temp view file path
	*/
	protected $path;

	/**
	 * Cached view content
	*/
	protected static $cache = array();

    /**
     * The ViewRenderer constructor.
     *
     * @param \Themosis\View\ViewData $view The view datas.
     */
	public function __construct(ViewData $view)
	{
		$this->view = $view;
		$this->viewID = $this->view->getViewID();
	}

    /**
     * Evaluate the view and return the output.
     *
     * @throws Exception
     * @return string The view content.
     */
	public function get()
	{
		// Start output buffer
		ob_start();

		// Extract sent datas
		extract($this->view->getDatas());

        // Create the temp file and fill it with the view
        // content.
        $this->path = $this->setViewFile($this->load());

		// Compile the view
		try
		{
			// Include the view
            include($this->path);

		} catch (Exception $e)
		{
			ob_get_clean();
			throw $e;
		}

        // Remove temporary file reference.
        unlink($this->path);

		// Return the compiled view and terminate the output buffer
		return ob_get_clean();
	}

	/**
	 * Load the view content.
	 *
     * @TODO Implement cache
	 * @return string The view cached content.
	 */
	private function load()
	{
		if (isset(static::$cache[$this->viewID])) {
			return static::$cache[$this->viewID];
		} else {
			return static::$cache[$this->viewID] = $this->view->get();
		}
	}

    /**
     * Create a temporary view file, set its content and returns
     * the file path.
     *
     * @param string $content The temporary file content.
     * @throws ViewException
     * @return string|bool The temporary view file path. False if error when creating the temporary file.
     */
    private function setViewFile($content)
    {
        if(!is_string($content)){
            throw new ViewException("Invalid view content. Can't create a temporary view file.");
        }

        // Create the temporary file
        $tmp = tempnam(sys_get_temp_dir(), $this->view->getViewID());

        // Check if we get a string so it makes sure
        // the file exists.
        if(is_string($tmp) && !empty($tmp)){

            // The temporary file is  empty.
            // Add the view content to the file.
            $handle = fopen($tmp, 'w');
            fwrite($handle, $content);
            fclose($handle);

            // Return the file path
            return $tmp;
        }

        return false;
    }

}