<?php

namespace Themosis\Metabox;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\Response;
use Themosis\Hook\IHook;
use Themosis\Support\CallbackHandler;

class Metabox implements MetaboxInterface
{
    use CallbackHandler;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string|array|\WP_Screen
     */
    protected $screen;

    /**
     * @var string
     */
    protected $context;

    /**
     * @var string
     */
    protected $priority;

    /**
     * @var string|callable
     */
    protected $callback;

    /**
     * @var array
     */
    protected $args;

    /**
     * @var string
     */
    protected $layout;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var IHook
     */
    protected $action;

    public function __construct(string $id, IHook $action)
    {
        $this->id = $id;
        $this->action = $action;
    }

    /**
     * Return the metabox id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set the metabox title.
     *
     * @param string $title
     *
     * @return MetaboxInterface
     */
    public function setTitle(string $title): MetaboxInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Return the metabox title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the metabox screen.
     *
     * @param string|array|\WP_Screen $screen
     *
     * @return MetaboxInterface
     */
    public function setScreen($screen): MetaboxInterface
    {
        $this->screen = $screen;

        return $this;
    }

    /**
     * Return the metabox screen.
     *
     * @return array|string|\WP_Screen
     */
    public function getScreen()
    {
        return $this->screen;
    }

    /**
     * Set the metabox context.
     *
     * @param string $context
     *
     * @return MetaboxInterface
     */
    public function setContext(string $context): MetaboxInterface
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Return the metabox context.
     *
     * @return string
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * Set the metabox priority.
     *
     * @param string $priority
     *
     * @return MetaboxInterface
     */
    public function setPriority(string $priority): MetaboxInterface
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Return the metabox priority.
     *
     * @return string
     */
    public function getPriority(): string
    {
        return $this->priority;
    }

    /**
     * Set the metabox callback.
     *
     * @param callable|string $callback
     *
     * @return MetaboxInterface
     */
    public function setCallback($callback): MetaboxInterface
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Return the metabox callback.
     * If the user has not defined its own callback, we
     * use our core implementation.
     *
     * @return callable|string|array
     */
    public function getCallback()
    {
        if (is_null($this->callback)) {
            return [$this, 'handle'];
        }

        return $this->callback;
    }

    /**
     * Set the metabox callback arguments.
     *
     * @param array $args
     *
     * @return MetaboxInterface
     */
    public function setArguments(array $args): MetaboxInterface
    {
        $this->args = $args;

        return $this;
    }

    /**
     * Return the metabox callback arguments.
     *
     * @return array
     */
    public function getArguments(): array
    {
        if (is_null($this->args)) {
            return [];
        }

        return $this->args;
    }

    /**
     * Set the metabox layout.
     *
     * @param string $layout
     *
     * @return MetaboxInterface
     */
    public function setLayout(string $layout): MetaboxInterface
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Return the metabox layout.
     *
     * @return string
     */
    public function getLayout(): string
    {
        if (is_null($this->layout)) {
            return 'default';
        }

        return $this->layout;
    }

    /**
     * Set the metabox for display.
     *
     * @return MetaboxInterface
     */
    public function set(): MetaboxInterface
    {
        $this->action->add('add_meta_boxes', [$this, 'display']);

        return $this;
    }

    /**
     * Handle "add_meta_boxes" hook and display the metabox.
     *
     * @param string   $post_type
     * @param \WP_Post $post
     */
    public function display($post_type, $post)
    {
        add_meta_box(
            $this->getId(),
            $this->getTitle(),
            [$this, 'render'],
            $this->getScreen(),
            $this->getContext(),
            $this->getPriority(),
            $this->getArguments()
        );
    }

    /**
     * Render the metabox.
     *
     * @param \WP_Post $post
     * @param array    $args
     */
    public function render(\WP_Post $post, array $args)
    {
        $args = array_merge($args['args'], [
            'metabox' => $this,
            'post' => $post,
            'screen' => $this->getScreen()
        ]);

        $response = $this->handleCallback($this->getCallback(), $args);

        if ($response instanceof Renderable) {
            echo $response->render();
        } elseif ($response instanceof Response) {
            echo $response->getContent();
        }
    }

    /**
     * Core framework metabox management. Default callback.
     */
    public function handle()
    {
        echo 'Handled by Themosis';
    }
}
