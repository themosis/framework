<?php

namespace Themosis\PostType;

use Themosis\Hook\IHook;
use Themosis\PostType\Contracts\PostTypeInterface;
use Themosis\Support\Facades\Metabox;

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

    /**
     * @var array
     */
    protected $status;

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
     * Return the post type slug.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Return the post type slug.
     * Aliased method for getSlug.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->getSlug();
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
        $this->registerStatus();
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

    /**
     * Register the custom status if any.
     */
    protected function registerStatus()
    {
        if (empty($this->status)) {
            return;
        }

        foreach ($this->status as $key => $args) {
            register_post_status($key, $args);
        }

        Metabox::make('themosis_publish', $this->slug)
            ->setTitle(__('Publish'))
            ->setContext('side')
            ->setPriority('core')
            ->setCallback(function ($args) {
                echo view('_themosisPublishMetabox', [
                    'statuses' => $this->status,
                    '__post' => $args['post']
                ]);
            })
            ->set();
    }

    /**
     * Set post type custom status.
     *
     * @param array|string $status
     * @param array        $args
     *
     * @return PostTypeInterface
     */
    public function status($status, array $args = []): PostTypeInterface
    {
        if (is_array($status)) {
            foreach ($status as $key => $params) {
                if (is_int($key)) {
                    $this->status($params);
                } elseif (is_string($key) && is_array($params)) {
                    $this->status($key, $params);
                }
            }

            return $this;
        }

        $this->prepareStatus($status, $args);

        return $this;
    }

    /**
     * Check if post type has custom status.
     *
     * @return bool
     */
    public function hasStatus(): bool
    {
        return ! empty($this->status);
    }

    /**
     * Register custom post type status.
     *
     * @param string $status
     * @param array  $args
     */
    protected function prepareStatus(string $status, array $args)
    {
        $this->status[$status] = $this->parseStatusArguments($status, $args);

        // Remove default publish metabox.
        $this->action->add('add_meta_boxes', function () {
            remove_meta_box('submitdiv', $this->slug, 'side');
        });

        // Apply selected status on save.
        $this->filter->add([
            'pre_post_status',
            'status_save_pre'
        ], [$this, 'applyStatus']);

        // Expose post type status for JS use.
        $this->filter->add('themosis_admin_global', function ($data) {
            $status['draft'] = $this->parseStatusArguments('draft', [
                'publish_text' => __('Save Draft')
            ]);

            $data['post_types'][$this->slug] = ['statuses' => array_merge($status, $this->status)];

            return $data;
        });

        // Reorder the list of statuses on the list table.
        // Put the "trash" item as the last one.
        $this->filter->add("views_edit-{$this->slug}", function ($views) {
            if (array_key_exists('trash', $views)) {
                $trash = $views['trash'];
                unset($views['trash']);
                end($views);
                $views['trash'] = $trash;
            }

            return $views;
        });
    }

    /**
     * Parse the status arguments.
     *
     * @param string $status
     * @param array  $args
     *
     * @return array
     */
    protected function parseStatusArguments(string $status, array $args): array
    {
        $name = ucfirst($status);

        return wp_parse_args($args, [
            'label' => $name,
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop(
                $name.' <span class="count">(%s)</span>',
                $name.' <span class="count">(%s)</span>'
            ),
            'publish_text' => __('Apply Changes')
        ]);
    }

    /**
     * Apply the selected status on post save.
     *
     * @param string $value The translated value by WordPress ("publish").
     *
     * @return string
     */
    public function applyStatus(string $value)
    {
        if (isset($_POST['post_type']) && $this->slug === $_POST['post_type'] && ! empty($this->status)) {
            if ((isset($_POST['post_status']) && 'publish' === $_POST['post_status'])
            && (isset($_REQUEST['post_status']) && 'draft' === $_REQUEST['post_status'])) {
                // New post with draft status as default and "publish" button is clicked.
                // Let's set to first registered status.
                $statuses = array_keys($this->status);

                return esc_attr(array_shift($statuses));
            } elseif (isset($_REQUEST['post_status']) && ! empty($_REQUEST['post_status'])) {
                // In case of a quickedit ajax save call, check the value of the "_status"
                // select tag before processing default post_status.
                if (isset($_POST['_status']) && ! empty($_POST['_status'])) {
                    return esc_attr($_POST['_status']);
                }

                // Else, simply apply the selected custom status value returned
                // from the edit screen of the custom post type.
                return esc_attr($_REQUEST['post_status']);
            }
        }

        return esc_attr($value);
    }
}
