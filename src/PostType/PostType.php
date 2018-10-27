<?php

namespace Themosis\PostType;

use Themosis\Hook\IHook;
use Themosis\PostType\Contracts\PostTypeInterface;

class PostType implements PostTypeInterface
{
    /**
     * Post type slug name.
     *
     * @var string
     */
    protected $slug;

    /**
     * Post type arguments.
     *
     * @var array
     */
    protected $args;

    /**
     * @var \WP_Post_Type
     */
    protected $instance;

    /**
     * @var IHook
     */
    protected $action;

    /**
     * @var IHook
     */
    protected $filter;

    public function __construct(string $slug, IHook $action, IHook $filter)
    {
        $this->slug = $slug;
        $this->action = $action;
        $this->filter = $filter;
    }

    /**
     * Set the post type labels.
     *
     * @param array $labels
     *
     * @return PostTypeInterface
     */
    public function setLabels(array $labels): PostTypeInterface
    {
        if (isset($this->args['labels'])) {
            $this->args['labels'] = array_merge($this->args['labels'], $labels);
        } else {
            $this->args['labels'] = $labels;
        }

        return $this;
    }

    /**
     * Return the post type labels.
     *
     * @return array
     */
    public function getLabels(): array
    {
        return $this->args['labels'] ?? [];
    }

    /**
     * Return a defined label value.
     *
     * @param string $name
     *
     * @return string
     */
    public function getLabel(string $name): string
    {
        $labels = $this->getLabels();

        return $labels[$name] ?? '';
    }

    /**
     * Set the post type arguments.
     *
     * @param array $args
     *
     * @return PostTypeInterface
     */
    public function setArguments(array $args): PostTypeInterface
    {
        $this->args = array_merge($this->args, $args);

        return $this;
    }

    /**
     * Return the post type arguments.
     *
     * @return array
     */
    public function getArguments(): array
    {
        return $this->args;
    }

    /**
     * Return a post type argument.
     *
     * @param string $property
     *
     * @return mixed|null
     */
    public function getArgument(string $property)
    {
        $args = $this->getArguments();

        return $args[$property] ?? null;
    }

    /**
     * Return the WordPress WP_Post_Type instance.
     *
     * @return null|\WP_Post_Type
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Register the post type.
     *
     * @return PostTypeInterface
     */
    public function set(): PostTypeInterface
    {
        if (function_exists('current_filter') && 'init' === $hook = current_filter()) {
            $this->register();
        } else {
            $this->action->add('init', [$this, 'register']);
        }

        return $this;
    }

    /**
     * Register post type hook callback.
     */
    public function register()
    {
        $this->instance = register_post_type($this->slug, $this->getArguments());
    }

    /**
     * Set the post type title input placeholder.
     *
     * @param string $title
     *
     * @return PostTypeInterface
     */
    public function setTitlePlaceholder(string $title): PostTypeInterface
    {
        $this->filter->add('enter_title_here', function ($default) use ($title) {
            $screen = get_current_screen();

            if ($this->slug === $screen->post_type) {
                return $title;
            }

            return $default;
        });

        return $this;
    }
}
