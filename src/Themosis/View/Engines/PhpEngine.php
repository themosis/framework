<?php
namespace Themosis\View\Engines;

class PhpEngine implements IEngine {

    /**
     * Get the evaluated content of the view.
     *
     * @param string $path
     * @param array $data
     * @return string
     */
    public function get($path, array $data = array())
    {
        return $this->evaluatePath($path, $data);
    }

    /**
     * Get the evaluated content of a view at a given path.
     *
     * @param string $__path The view path.
     * @param array $__data The view passed data.
     * @return string
     */
    protected function evaluatePath($__path, array $__data)
    {
        ob_start();

        // Extract view datas.
        extract($__data);

        // Compile the view.
        try
        {
            // Include the view.
            include($__path);

        } catch (\Exception $e)
        {
            $this->handleException($e);
        }

        // Return the compiled view and terminate the output buffer.
        return ltrim(ob_get_clean());
    }

    /**
     * Handle view exception.
     *
     * @param \Exception $e
     * @throws \Exception
     * @return void
     */
    protected function handleException($e)
    {
        ob_get_clean();
        throw $e;
    }
}