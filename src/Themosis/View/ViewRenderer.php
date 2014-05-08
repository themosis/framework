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
	 * Temp view file path
	*/
	protected $path;

	/**
	 * Cached view content.
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
	}

    /**
     * Evaluate the view and return the output.
     *
     * @throws \Exception
     * @return string The view content.
     */
	public function get()
	{
		// Start output buffer
		ob_start();

		// Extract view datas
		extract($this->view->getDatas());

		// Compile the view
		try
		{
			// Include the view
            include($this->load());

		} catch (\Exception $e)
		{
			ob_get_clean();
			throw $e;
		}

		// Return the compiled view and terminate the output buffer
		return ob_get_clean();
	}

	/**
	 * Load the view file.
	 *
	 * @return string The view file path.
	 */
	private function load()
	{
        // The view file path to the storage directory.
        $filename = $this->view->getViewPath();

        // Check if the file already exists.
        if(file_exists($filename)){

            // Content of the currently stored view file.
            $fileContent = file_get_contents($this->view->getViewPath());

            if($this->view->get() === $fileContent){

                // Content is the same, so let's just return it.
                return $filename;

            }

            // The content has changed, so let's remove the old file.
            unlink($filename);

        }

        // No file exists, so let's build it!
        return $this->setViewFile($filename, $this->view->get());
	}

    /**
     * Create a temporary view file, set its content and returns
     * the file path.
     *
     * @param string $path The stored file path.
     * @param string $content The temporary file content.
     * @throws ViewException
     * @return string|bool The temporary view file path. False if error when creating the temporary file.
     */
    private function setViewFile($path, $content)
    {
        if(!is_string($content)){
            throw new ViewException("Invalid view content. Can't create a temporary view file.");
        }

        // At this point, the file does not exist.
        // Create the temporary file and add its content.
        // Make sure the 'storage' directory is writable.
        // The temporary file is  empty.
        // Add the view content to the file.
        $handle = fopen($path, 'w');
        fwrite($handle, $content);
        $closed = fclose($handle);

        // Return the file path
        if($closed){

            return $path;

        }

        return false;
    }

}