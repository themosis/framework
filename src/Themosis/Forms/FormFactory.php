<?php

namespace Themosis\Forms;

use Themosis\Forms\Fields\FieldBuilder;
use Themosis\Html\HtmlBuilder;

class FormFactory
{
    /**
     * Namespaces where forms are stored.
     *
     * @var array
     */
    protected $namespaces;

    /**
     * FormFactory constructor.
     *
     * @param string|array $namespaces
     */
    public function __construct($namespaces)
    {
        $this->namespaces = (array) $namespaces;
    }

    /**
     * Add form factory namespaces.
     *
     * @param string|array $namespaces
     */
    public function addNamespaces($namespaces)
    {
        foreach ((array) $namespaces as $namespace) {
            $this->namespaces[] = $namespace;
        }
    }

    /**
     * Return registered form namespaces.
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * Creates a new form instance and returns it.
     *
     * @param mixed $form
     */
    public function make($form)
    {
        $class = $this->findForm($form);

        return new $class(new HtmlBuilder(), new FieldBuilder());
    }

    /**
     * Find the form class.
     *
     * @param string $name
     *
     * @return string
     */
    protected function findForm($name)
    {
        $baseNamespace = array_filter($this->namespaces, function ($namespace) use ($name) {
            $className = rtrim($namespace, '\/').'\\'.ltrim($name, '\/');

            return class_exists($className);
        });

        return rtrim(array_shift($baseNamespace), '\/').'\\'.ltrim($name, '\/');
    }
}
