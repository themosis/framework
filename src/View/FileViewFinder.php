<?php

namespace Themosis\View;

use Illuminate\View\FileViewFinder as IlluminateFileViewFinder;

class FileViewFinder extends IlluminateFileViewFinder
{
    /**
     * Ordered location paths.
     *
     * @var array
     */
    private $orderedPaths = [];

    /**
     * Return located views.
     *
     * @return array
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Add ordered view location.
     *
     * @param string $location
     * @param int    $priority
     */
    public function addOrderedLocation(string $location, int $priority = 20)
    {
        $this->orderedPaths[] = new FileViewLocation($location, $priority);
    }

    /**
     * Get the active view paths.
     *
     * @return array
     */
    public function getPaths()
    {
        $this->parseOrderedPaths();

        return $this->paths;
    }

    /**
     * Get the fully qualified location of the view.
     *
     * @param string $name
     *
     * @return string
     */
    public function find($name)
    {
        if (isset($this->views[$name])) {
            return $this->views[$name];
        }

        if ($this->hasHintInformation($name = trim($name))) {
            return $this->views[$name] = $this->findNamespacedView($name);
        }

        return $this->views[$name] = $this->findInPaths($name, $this->getPaths());
    }

    /**
     * Parse ordered path locations and prepend
     * them to default paths.
     */
    private function parseOrderedPaths()
    {
        if (empty($this->orderedPaths)) {
            return;
        }

        /**
         * @var FileViewLocation[]
         */
        $orderedPaths = $this->orderedPaths;

        uasort($orderedPaths, function ($a, $b) {
            return $a->getPriority() < $b->getPriority();
        });

        foreach ($orderedPaths as $orderedPath) {
            $this->prependLocation($orderedPath->getPath());
        }
    }
}
