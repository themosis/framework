<?php
namespace Themosis\Core;

interface IWrapper {

    /**
     * The wrapper display method.
     *
     * @return mixed
     */
    public function display();

    /**
     * The wrapper install method. Save container values.
     *
     * @return mixed
     */
    public function save();

} 