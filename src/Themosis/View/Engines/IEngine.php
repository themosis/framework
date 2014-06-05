<?php
namespace Themosis\View\Engines;

interface IEngine {

    /**
     * Get the evaluated content of the view.
     *
     * @param string $path
     * @param array $data
     * @return string
     */
    public function get($path, array $data = array());

} 