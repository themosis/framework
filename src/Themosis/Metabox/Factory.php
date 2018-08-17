<?php

namespace Themosis\Metabox;

class Factory
{
    /**
     * Create a new metabox instance.
     *
     * @param string                  $id
     * @param string|array|\WP_Screen $screen
     *
     * @return MetaboxInterface
     */
    public function make(string $id, $screen = 'post'): MetaboxInterface
    {
        return (new Metabox($id))
            ->setTitle($this->setDefaultTitle($id))
            ->setScreen($screen)
            ->setContext('advanced')
            ->setPriority('default');
    }

    /**
     * Format a default title based on given name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function setDefaultTitle(string $name): string
    {
        return ucfirst(str_replace(['_', '-', '.'], ' ', $name));
    }
}
