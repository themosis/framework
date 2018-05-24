<?php

namespace Themosis\Forms;

use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Contracts\FormInterface;
use Themosis\Forms\Contracts\FormRepositoryInterface;

/**
 * Class Form
 *
 * @package Themosis\Forms
 */
class Form implements FormInterface
{
    /**
     * @var string
     */
    protected $prefix = 'th_';

    /**
     * Form groups.
     *
     * @var array
     */
    protected $groups = [];

    /**
     * @var FormRepositoryInterface
     */
    protected $repository;

    public function __construct(FormRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get the form repository instance.
     *
     * @return FormRepositoryInterface
     */
    public function repository(): FormRepositoryInterface
    {
        return $this->repository;
    }

    /**
     * Render a form and returns its HTML structure.
     *
     * @return string
     */
    public function render(): string
    {
        return '';
    }

    /**
     * Set the form prefix. If fields are attached to the form,
     * all fields are updated with the given prefix.
     *
     * @param string $prefix
     *
     * @return FormInterface
     */
    public function setPrefix(string $prefix): FormInterface
    {
        $this->prefix = $prefix;

        // Update all attached fields with the given prefix.
        foreach ($this->repository->all() as $field) {
            /** @var FieldTypeInterface $field */
            $field->setPrefix($prefix);
        }

        return $this;
    }

    /**
     * Return the form prefix.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
