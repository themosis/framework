<?php
namespace Themosis\Route;

use Countable;

class RouteCollection implements Countable {

    /**
     * All routes in the collection.
     *
     * @var array
     */
    protected $allRoutes = array();

    /**
     * Return all routes of the collection.
     *
     * @return array
     */
    public function getRoutes()
    {
        // Associative array ? 'uri' => $routeInstance
        return array_values($this->allRoutes);
    }

    /**
     * Return the number of routes in the collection.
     * Method from "Countable" interface. -> triggered when using count($obj).
     *
     * @return int
     */
    public function count()
    {
        return count($this->getRoutes());
    }
}