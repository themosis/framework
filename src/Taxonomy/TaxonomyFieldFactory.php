<?php

namespace Themosis\Taxonomy;

use Themosis\Hook\IHook;
use Themosis\Taxonomy\Contracts\TaxonomyInterface;

class TaxonomyFieldFactory
{
    /**
     * @var \Illuminate\View\Factory
     */
    private $factory;

    /**
     * @var \Illuminate\Contracts\Validation\Factory
     */
    private $validator;

    /**
     * @var IHook
     */
    private $action;

    public function __construct(
        \Illuminate\View\Factory $viewFactory,
        \Illuminate\Contracts\Validation\Factory $validator,
        IHook $action
    ) {
        $this->factory = $viewFactory;
        $this->validator = $validator;
        $this->action = $action;
    }

    /**
     * Create a new TaxonomyField instance.
     *
     * @param TaxonomyInterface $taxonomy
     * @param array             $options
     *
     * @return TaxonomyField
     */
    public function make(TaxonomyInterface $taxonomy, array $options = [])
    {
        $options = array_merge([
            'theme' => 'themosis.taxonomy',
            'prefix' => 'th_'
        ], $options);

        return new TaxonomyField(
            $taxonomy,
            new TaxonomyFieldRepository(),
            $this->factory,
            $this->validator,
            $this->action,
            $options
        );
    }
}
