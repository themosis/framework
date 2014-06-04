<?php
namespace Themosis\View;

interface IRenderable {

    /**
     * Get the evaluated content of the object.
     *
     * @return string
     */
    public function render();

} 