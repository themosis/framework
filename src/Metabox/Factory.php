<?php

namespace Themosis\Metabox;

use Themosis\Core\Application;
use Themosis\Forms\Fields\FieldsRepository;
use Themosis\Hook\IHook;
use Themosis\Metabox\Contracts\MetaboxInterface;
use Themosis\Metabox\Resources\MetaboxResourceInterface;

class Factory
{
    /**
     * @var Application
     */
    protected $container;

    /**
     * @var IHook
     */
    protected $action;

    /**
     * @var IHook
     */
    protected $filter;

    /**
     * @var MetaboxResourceInterface
     */
    protected $resource;

    public function __construct(
        Application $container,
        IHook $action,
        IHook $filter,
        MetaboxResourceInterface $resource
    ) {
        $this->container = $container;
        $this->action = $action;
        $this->filter = $filter;
        $this->resource = $resource;
    }

    /**
     * Create a new metabox instance.
     *
     * @param string                  $id
     * @param string|array|\WP_Screen $screen
     *
     * @throws MetaboxException
     *
     * @return MetaboxInterface
     */
    public function make(string $id, $screen = 'post'): MetaboxInterface
    {
        $metabox = (new Metabox($id, $this->action, $this->filter, new FieldsRepository()))
            ->setContainer($this->container)
            ->setTitle($this->setDefaultTitle($id))
            ->setScreen($screen)
            ->setContext('normal')
            ->setPriority('default')
            ->setArguments([
                '__block_editor_compatible_meta_box' => true,
                '__back_compat_meta_box' => false
            ])
            ->setResource($this->resource)
            ->setLocale($this->container->getLocale());

        $this->setMetaboxTranslations($metabox);

        $abstract = sprintf('themosis.metabox.%s', $id);

        if (! $this->container->bound($abstract)) {
            $this->container->instance($abstract, $metabox);
        }

        return $metabox;
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

    /**
     * Set metabox translations strings.
     *
     * @param MetaboxInterface $metabox
     *
     * @return $this
     */
    protected function setMetaboxTranslations(MetaboxInterface $metabox)
    {
        if (! function_exists('__')) {
            return $this;
        }

        $metabox->addTranslation('done', __('Saved', Application::TEXTDOMAIN));
        $metabox->addTranslation('error', __('Saved with errors', Application::TEXTDOMAIN));
        $metabox->addTranslation('saving', __('Saving', Application::TEXTDOMAIN));
        $metabox->addTranslation('submit', sprintf('%s %s', __('Save', Application::TEXTDOMAIN), $metabox->getTitle()));

        return $this;
    }
}
