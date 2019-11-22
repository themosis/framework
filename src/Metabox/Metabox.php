<?php

namespace Themosis\Metabox;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Renderable;
use Symfony\Component\HttpFoundation\Response;
use Themosis\Forms\Contracts\FieldsRepositoryInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Hook\IHook;
use Themosis\Metabox\Contracts\MetaboxInterface;
use Themosis\Metabox\Resources\MetaboxResourceInterface;
use Themosis\Support\CallbackHandler;
use Themosis\Support\Contracts\SectionInterface;
use Themosis\Support\Section;

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

    /**
     * @var IHook
     */
    protected $filter;

    /**
     * @var MetaboxResourceInterface
     */
    protected $resource;

    /**
     * @var FieldsRepositoryInterface
     */
    protected $repository;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $prefix = 'th_';

    /**
     * @var array
     */
    protected $l10n;

    /**
     * @var string
     */
    protected $capability;

    /**
     * @var array
     */
    protected $template;

    public function __construct(string $id, IHook $action, IHook $filter, FieldsRepositoryInterface $repository)
    {
        $this->id = $id;
        $this->action = $action;
        $this->filter = $filter;
        $this->repository = $repository;
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
        if (! is_null($this->capability) && ! current_user_can($this->capability)) {
            return;
        }

        if (! $this->hasTemplateForPost($post)) {
            return;
        }

        $this->filter->add('admin_body_class', function ($classes) {
            if (false !== strrpos($classes, 'themosis')) {
                return $classes;
            }

            $classes.= ' themosis';

            return $classes;
        });

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
     *
     * @param array $args
     */
    public function handle(array $args)
    {
        $this->filter->add('themosis_admin_global', function ($data) use ($args) {
            if (! isset($data['metabox'])) {
                $data['metabox'] = [$this->id];
            } elseif (isset($data['metabox'])) {
                $data['metabox'][] = $this->id;
            }

            /*
             * Provides WP_Post data.
             */
            $data['post'] = $args['post'];

            return $data;
        });
    }

    /**
     * Set the metabox resource abstraction layer/manager.
     *
     * @param MetaboxResourceInterface $resource
     *
     * @return MetaboxInterface
     */
    public function setResource(MetaboxResourceInterface $resource): MetaboxInterface
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Return the metabox resource manager.
     *
     * @return MetaboxResourceInterface
     */
    public function getResource(): MetaboxResourceInterface
    {
        return $this->resource;
    }

    /**
     * Return the metabox as an array resource.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->getResource()->setSource($this)->toArray();
    }

    /**
     * Return the metabox as a JSON resource.
     *
     * @return string
     */
    public function toJson(): string
    {
        return $this->getResource()->setSource($this)->toJson();
    }

    /**
     * Set the metabox locale.
     *
     * @param string $locale
     *
     * @return MetaboxInterface
     */
    public function setLocale(string $locale): MetaboxInterface
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Return the metabox locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Set the metabox prefix.
     *
     * @param string $prefix
     *
     * @return MetaboxInterface
     */
    public function setPrefix(string $prefix): MetaboxInterface
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Return the metabox prefix.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Return the metabox fields repository instance.
     *
     * @return FieldsRepositoryInterface
     */
    public function repository(): FieldsRepositoryInterface
    {
        return $this->repository;
    }

    /**
     * Add a field to the metabox.
     *
     * @param FieldTypeInterface|SectionInterface $field
     * @param SectionInterface                    $section
     *
     * @return MetaboxInterface
     */
    public function add($field, SectionInterface $section = null): MetaboxInterface
    {
        if ($field instanceof SectionInterface) {
            $section = $field;

            if ($section->hasItems()) {
                foreach ($section->getItems() as $item) {
                    /** @var FieldTypeInterface $item */
                    $item->setOptions([
                        'group' => $section->getId()
                    ]);

                    $this->add($item, $section);
                }
            }

            return $this;
        }

        $field->setLocale($this->getLocale());
        $field->setPrefix($this->getPrefix());

        if (is_null($section)) {
            if ($this->repository()->hasGroup($field->getOption('group'))) {
                $section = $this->repository()->getGroup($field->getOption('group'));
            } else {
                $section = new Section($field->getOption('group'));
            }
            $section->addItem($field);
        }

        $this->repository()->addField($field, $section);

        return $this;
    }

    /**
     * Return metabox all translations.
     *
     * @return array
     */
    public function getTranslations(): array
    {
        if (is_null($this->l10n)) {
            return [];
        }

        return $this->l10n;
    }

    /**
     * Return the translation based on given key.
     * Return empty string if not defined.
     *
     * @param string $key
     *
     * @return string
     */
    public function getTranslation(string $key): string
    {
        return $this->l10n[$key] ?? '';
    }

    /**
     * Add metabox translation.
     *
     * @param string $key
     * @param string $translation
     *
     * @return MetaboxInterface
     */
    public function addTranslation(string $key, string $translation): MetaboxInterface
    {
        $this->l10n[$key] = $translation;

        return $this;
    }

    /**
     * Set the metabox capability.
     *
     * @param string $cap
     *
     * @return MetaboxInterface
     */
    public function setCapability(string $cap): MetaboxInterface
    {
        $this->capability = $cap;

        return $this;
    }

    /**
     * Return the metabox capability.
     *
     * @return string
     */
    public function getCapability(): string
    {
        return $this->capability;
    }

    /**
     * Set the metabox template.
     *
     * @param array|string $template
     * @param string       $screen
     *
     * @return MetaboxInterface
     */
    public function setTemplate($template, string $screen = 'page'): MetaboxInterface
    {
        $this->template[$screen] = (array) $template;

        return $this;
    }

    /**
     * Return the metabox template.
     *
     * @return array
     */
    public function getTemplate(): array
    {
        return $this->template;
    }

    /**
     * Check if given post should use the template.
     *
     * @param \WP_Post|\WP_Comment $post
     *
     * @return bool
     */
    private function hasTemplateForPost($post): bool
    {
        if (is_a($post, 'WP_Comment')) {
            return false;
        }

        $postTemplate = get_post_meta($post->ID, '_wp_page_template', true);

        $templates = isset($this->template[$post->post_type]) ? $this->template[$post->post_type] : [];

        if (empty($templates)) {
            // No templates exist for the current post type so we let
            // pass through either in order to display the metabox.
            return true;
        }

        return in_array($postTemplate, $templates, true);
    }
}
